<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\Katottg\CategoryEnum;
use App\Repository\KatottgRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KatottgRepository::class)]
#[ApiResource]
class Katottg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $level1 = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $level2 = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $level3 = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $level4 = null;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $level5 = null;
    #[ORM\Column(type: 'string', enumType: CategoryEnum::class)]
    private ?CategoryEnum $category = null;
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    public function getLevel1(): ?string
    {
        return $this->level1;
    }

    public function setLevel1(?string $level1): Katottg
    {
        $this->level1 = $level1;
        return $this;
    }

    public function getLevel2(): ?string
    {
        return $this->level2;
    }

    public function setLevel2(?string $level2): Katottg
    {
        $this->level2 = $level2;
        return $this;
    }

    public function getLevel3(): ?string
    {
        return $this->level3;
    }

    public function setLevel3(?string $level3): Katottg
    {
        $this->level3 = $level3;
        return $this;
    }

    public function getLevel4(): ?string
    {
        return $this->level4;
    }

    public function setLevel4(?string $level4): Katottg
    {
        $this->level4 = $level4;
        return $this;
    }

    public function getLevel5(): ?string
    {
        return $this->level5;
    }

    public function setLevel5(?string $level5): Katottg
    {
        $this->level5 = $level5;
        return $this;
    }

    public function getCategory(): ?CategoryEnum
    {
        return $this->category;
    }

    public function setCategory(?CategoryEnum $category): Katottg
    {
        $this->category = $category;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Katottg
    {
        $this->name = $name;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
