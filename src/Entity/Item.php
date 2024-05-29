<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Item\ItemTag;
use App\Entity\Item\ValueItemAttributes;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING)]
    private ?string $userId = null;

    #[ORM\Column(type: Types::STRING, length: 180)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $addedAt;

    #[ORM\ManyToOne(targetEntity: ItemCollection::class, fetch: 'EAGER', inversedBy: 'item')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'cascade')]
    private ?ItemCollection $itemCollection;

    #[ORM\OneToMany(targetEntity: ValueItemAttributes::class, mappedBy: 'item', cascade: ['persist'], orphanRemoval: true)]
    private Collection $valueAttributes;

    #[ORM\JoinTable(name: 'items_to_tags')]
    #[ORM\JoinColumn(name: 'item_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'item_tag_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: ItemTag::class)]
    private Collection $tags;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->addedAt = new \DateTimeImmutable();
        $this->itemCollection = null;
        $this->valueAttributes = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getAddedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function getItemCollection(): ItemCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(ItemCollection $itemCollection): self
    {
        $this->itemCollection = $itemCollection;

        return $this;
    }

    public function getValueAttributes(): Collection
    {
        return $this->valueAttributes;
    }

    public function setValueAttributes(array $valueAttributes): self
    {
        $this->valueAttributes = new ArrayCollection($valueAttributes);

        return $this;
    }

    /**
     * @return array<ItemTag>
     */
    public function getTags(): array
    {
        return $this->tags->toArray();
    }

    /**
     * @param array<ItemTag> $tags
     */
    public function setTags(array $tags): self
    {
        $this->tags = new ArrayCollection($tags);

        return $this;
    }
}
