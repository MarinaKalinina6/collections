<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Item\ItemTag;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class ItemTagAutocompleteField extends AbstractType
{
    /** @var array<string, ItemTag> */
    private static array $newTagsCache = [];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => ItemTag::class,
            'searchable_fields' => ['name'],
            'choice_label' => fn(ItemTag $itemTag): string => $itemTag->getName(),
            'tom_select_options' => [
                'create' => true,
            ],
            'multiple' => true,
            ['constraints' => [
                    new NotBlank()],],

        ]);

        $resolver->setNormalizer(
            'choice_loader',
            function (OptionsResolver $resolver, ChoiceLoaderInterface $choiceLoader): ChoiceLoaderInterface {
                return new class ($choiceLoader, $this->entityManager, self::$newTagsCache) implements
                    ChoiceLoaderInterface {
                    public function __construct(
                        private readonly ChoiceLoaderInterface $choiceLoader,
                        private readonly EntityManagerInterface $entityManager,
                        private array &$newTagsCache,
                    ) {
                    }

                    public function loadChoiceList(?callable $value = null): ChoiceListInterface
                    {
                        return $this->choiceLoader->loadChoiceList($value);
                    }

                    public function loadChoicesForValues(array $values, ?callable $value = null): array
                    {
                        $toCreate = [];
                        $toFetch = [];

                        foreach ($values as $item) {
                            if (Uuid::isValid($item) === false) {
                                $toCreate[] = $item;
                            } else {
                                $toFetch[] = $item;
                            }
                        }

                        $choices = $this->choiceLoader->loadChoicesForValues($toFetch, $value);

                        foreach ($toCreate as $name) {
                            if (isset($this->newTagsCache[$name]) === false) {
                                $itemTag = new ItemTag($name);

                                $this->entityManager->persist($itemTag);

                                $this->newTagsCache[$name] = $itemTag;
                            }

                            $choices[] = $this->newTagsCache[$name];
                        }

                        return $choices;
                    }

                    public function loadValuesForChoices(array $choices, ?callable $value = null): array
                    {
                        return $this->choiceLoader->loadValuesForChoices($choices, $value);
                    }
                };
            },
        );
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
