<?php

namespace App\Entity;

use App\Repository\MeasureTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MeasureTypeRepository::class)]
class MeasureType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[ORM\Column(length: 16)]
    private ?string $units = null;

    #[ORM\ManyToMany(targetEntity: Module::class, inversedBy: 'measureTypes')]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: MeasuredValue::class)]
    private Collection $measuredValues;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->measuredValues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): static
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
        }

        return $this;
    }

    public function removeModule(Module $module): static
    {
        $this->modules->removeElement($module);

        return $this;
    }

    /**
     * @return Collection<int, MeasuredValue>
     */
    public function getMeasuredValues(): Collection
    {
        return $this->measuredValues;
    }

    public function addMeasuredValue(MeasuredValue $measuredValue): static
    {
        if (!$this->measuredValues->contains($measuredValue)) {
            $this->measuredValues->add($measuredValue);
            $measuredValue->setType($this);
        }

        return $this;
    }

    public function removeMeasuredValue(MeasuredValue $measuredValue): static
    {
        if ($this->measuredValues->removeElement($measuredValue)) {
            // set the owning side to null (unless already changed)
            if ($measuredValue->getType() === $this) {
                $measuredValue->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUnits(): ?string
    {
        return $this->units;
    }

    /**
     * @param string|null $units
     */
    public function setUnits(?string $units): void
    {
        $this->units = $units;
    }

    public function __toString(): string
    {
        return sprintf("%s (%s)", $this->name ?? '#' . $this->id, $this->units ?? '-');
    }
}
