<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use App\Enum\Katottg\CategoryEnum;
use App\Repository\AdministrativeUnitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AdministrativeUnitRepository::class)]
#[ApiResource(
    cacheHeaders: [
        'max_age' => 3600,
        'shared_max_age' => 7200,
        'vary' => ['Authorization', 'Accept-Language'],
    ],
    paginationEnabled: false,

)]
#[Index(name: 'category_idx', columns: ['category'])]
class AdministrativeUnit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_IPARTIAL)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: CategoryEnum::class)]
    #[ApiFilter(SearchFilter::class, strategy: SearchFilterInterface::STRATEGY_EXACT)]
    private ?CategoryEnum $category = null;

    #[ORM\Column(length: 255, unique: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'exact', properties: ['code'])]
    private ?string $code = null;

    #[ORM\Column(type: 'integer')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact', properties: ['level'])]
    #[ApiFilter(RangeFilter::class, properties: ['level'])]
    private ?int $level = null;
    #[ORM\JoinTable(name: 'administrative_unit_branch')]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\OrderBy(['level' => 'ASC'])]
    #[Groups(['unit:read'])]
    #[ApiFilter(SearchFilter::class, properties: ['path.id' => 'exact', 'path.parent.id' => 'exact'])]
    #[ApiProperty(readableLink: true)]
    private Collection $path;
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[Link(toProperty: 'children')]
    #[ApiFilter(SearchFilter::class, strategy: 'exact', properties: ['parent.id'])]
    #[ApiProperty(readableLink: true)]
    private ?self $parent = null;
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[Link(toProperty: 'parent')]
    private Collection $children;

    public function __construct()
    {
        $this->path = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): AdministrativeUnit
    {
        $this->level = $level;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function setChildren(Collection $children): AdministrativeUnit
    {
        $this->children = $children;
        $this->children->forAll(fn($i, $child) => $child->getParent() !== $this ? $child->setParent($this) : $child);

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;
        $children = $this->parent?->getChildren();
        if (!$children->contains($this)) {
            $children->add($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            match (true) {
                CategoryEnum::Region === $this->getCategory() && $this->getCode(
                ) !== 'UA01000000000013043' => '%s обл.',
                in_array(
                    $this->getCategory(),
                    [
                        CategoryEnum::CityWithSpecialStatus,
                        CategoryEnum::City,
                    ],
                    true
                ) => 'м. %s',
                in_array($this->getCategory(), [CategoryEnum::District, CategoryEnum::CityDistrict], true) => '%s р-н',
                CategoryEnum::Community === $this->getCategory() => '%s т.г.',
                in_array(
                    $this->getCategory(),
                    [
                        CategoryEnum::UrbanTypeSettlement,
                        CategoryEnum::Settlement,
                    ],
                    true
                ) => 'с-ще %s',
                CategoryEnum::Village === $this->getCategory() => 'с. %s',
                default => '%s',
            },
            $this->getName()
        );
    }

    public function getCategory(): ?CategoryEnum
    {
        return $this->category;
    }

    public function setCategory(CategoryEnum $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    #[Groups(['unit:read'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        return $this;
    }

    public function getFullPathName(): string
    {
        return implode(' > ', $this->getPath()->toArray());
    }

    #[Groups(['unit:read'])]
    public function getPath(): Collection
    {
        return $this->path;
    }

    public function setPath(Collection $path): static
    {
        $this->path = $path;

        return $this;
    }


}