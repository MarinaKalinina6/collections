<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ItemCollection\CollectionCategory;
use App\Entity\ItemCollection\CustomItemAttribute;
use App\Repository\ItemCollectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ItemCollectionRepository::class)]
class ItemCollection
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 180)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 180)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $userId = null;

    #[ORM\ManyToOne(targetEntity: CollectionCategory::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CollectionCategory $category = null;

    #[ORM\OneToMany(targetEntity: CustomItemAttribute::class, mappedBy: 'itemCollection', cascade: ['persist'], orphanRemoval: true)]
    private Collection $customItemAttributes;

    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'itemCollection')]
    private Collection $item;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->customItemAttributes = new ArrayCollection();
        $this->item = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
     public function setUserId(string $userId): self
     {
         $this->userId = $userId;
         return $this;
     }

    public function getCategory(): ?CollectionCategory
    {
        return $this->category;
    }

    public function setCategory(?CollectionCategory $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCustomItemAttributes(): Collection
    {
        return $this->customItemAttributes;
    }

    public function addCustomItemAttribute(CustomItemAttribute $customItemAttribute): self
    {
        if (!$this->customItemAttributes->contains($customItemAttribute)) {
            $this->customItemAttributes->add($customItemAttribute);
            $customItemAttribute->setItemCollection($this);
        }

        return $this;
    }

    public function removeCustomItemAttribute(CustomItemAttribute $customItemAttribute): self
    {
        if ($this->customItemAttributes->removeElement($customItemAttribute)) {
            if ($customItemAttribute->getItemCollection() === $this) {
                $customItemAttribute->setItemCollection(null);
            }
        }

        return $this;
    }

    public function getItem(): Collection
    {
        return $this->item;
    }

}
