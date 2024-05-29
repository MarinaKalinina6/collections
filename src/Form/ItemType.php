<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Enum\CustomAttributeType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ItemType extends AbstractType implements DataMapperInterface
{
    private const string FIELD_ATTRIBUTE_PREFIX = 'attribute_';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ItemCollection $collection */
        $collection = $options['collection'];

        $builder->add('reference', HiddenType::class)
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 180]),
                ],
            ])
            ->add('tags', ItemTagAutocompleteField::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);

        foreach ($collection->getCustomItemAttributes() as $customAttribute) {
            $options = [
                'required' => false,
                'label' => $customAttribute->getName(),
                'constraints' => [
                    new NotBlank(),
                ],
            ];
            $builder
                ->add(
                    sprintf('%s%s', self::FIELD_ATTRIBUTE_PREFIX, $customAttribute->getId()->toBase58()),
                    match ($customAttribute->getType()) {
                        CustomAttributeType::Integer => IntegerType::class,
                        CustomAttributeType::String, => TextType::class,
                        CustomAttributeType::Boolean => CheckboxType::class,
                        CustomAttributeType::Date => DateType::class,
                        CustomAttributeType::Text => TextareaType::class,
                    },
                    $options,
                );
        }

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('collection')
            ->setAllowedTypes('collection', ItemCollection::class);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        $item = $viewData;
        if (!$item instanceof Item) {
            return;
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        $forms['name']->setData($item->getName());
        $forms['tags']->setData(new ArrayCollection($item->getTags()));
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);


        /** @var ItemCollection $collection */
        $collection = $forms['reference']->getParent()->getConfig()->getOption('collection');
        $item = $viewData;
        if($item === []){
            $item = new Item();
        }

        $item->setName($forms['name']->getData());

        $customAttributes = [];
        foreach ($collection->getCustomItemAttributes() as $customAttribute) {
            $attributeForm = $forms[sprintf(
                '%s%s',
                self::FIELD_ATTRIBUTE_PREFIX,
                $customAttribute->getId()->toBase58(),
            )];

            $value = $attributeForm->getData();
            if ($value === null) {
                continue;
            }

            $customAttributes[] = new Item\ValueItemAttributes($value, $customAttribute, $item);
        }

        $item->setValueAttributes($customAttributes);
        $item->setTags($forms['tags']->getData() ? $forms['tags']->getData()->toArray() : []);

        $viewData = $item;
    }
}

