<?php

declare(strict_types=1);

namespace App\Entity\Item;

use App\Entity\Item;
use App\Entity\ItemCollection\CustomItemAttribute;
use App\Enum\CustomAttributeType;
use App\Repository\ValueItemAttributesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ValueItemAttributesRepository::class)]
class ValueItemAttributes
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(enumType: CustomAttributeType::class)]
    private CustomAttributeType $type;

    #[ORM\Column(type: Types::STRING)]
    private string $value;

    #[ORM\ManyToOne(targetEntity: Item::class, inversedBy: 'AddedItemAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Item $item;

    #[ORM\ManyToOne(targetEntity: CustomItemAttribute::class, inversedBy: 'AddedItemAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CustomItemAttribute $customItemAttribute;

    public function __construct(mixed $value, CustomItemAttribute $customItemAttribute, Item $item)
    {
        $this->id = Uuid::v7();
        $this->item = $item;
        $this->customItemAttribute = $customItemAttribute;
        $this->type = $customItemAttribute->getType();
        $this->value = match ($customItemAttribute->getType()) {
            CustomAttributeType::Integer => (string)$value,
            CustomAttributeType::String, CustomAttributeType::Text => $value,
            CustomAttributeType::Boolean => $value ? 'true' : 'false',
            CustomAttributeType::Date => $value->format('Y-m-d'),
        };
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function updateValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getCustomItemAttribute(): ?CustomItemAttribute
    {
        return $this->customItemAttribute;
    }

    public function getType(): CustomAttributeType
    {
        return $this->type;
    }

    public function getValueAttributes(): mixed
    {
        if ($this->customItemAttribute->getType() !== $this->type) {
            return null;
        }

        return match ($this->customItemAttribute->getType()) {
            CustomAttributeType::Integer => (int)$this->value,
            CustomAttributeType::String, CustomAttributeType::Text => $this->value,
            CustomAttributeType::Boolean => $this->value === 'true',
            CustomAttributeType::Date => \DateTimeImmutable::createFromFormat('Y-m-d', $this->value),
        };
    }


}