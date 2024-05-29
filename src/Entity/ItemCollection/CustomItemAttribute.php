<?php

declare(strict_types=1);

namespace App\Entity\ItemCollection;

use App\Entity\ItemCollection;
use App\Enum\CustomAttributeType;
use App\Repository\CustomItemAttributeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(CustomItemAttributeRepository::class)]
class CustomItemAttribute
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: CustomAttributeType::class)]
    private ?CustomAttributeType $type = null;

    #[ORM\ManyToOne(inversedBy: 'customItemAttribute')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ItemCollection $itemCollection = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getType(): ?CustomAttributeType
    {
        return $this->type;
    }

    public function setType(?CustomAttributeType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getItemCollection(): ?ItemCollection
    {
        return $this->itemCollection;
    }

    public function setItemCollection(?ItemCollection $itemCollection): self
    {
        $this->itemCollection = $itemCollection;

        return $this;
    }





}