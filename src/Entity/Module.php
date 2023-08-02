<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: StateHistory::class)]
    private Collection $stateHistories;

    #[ORM\ManyToMany(targetEntity: MeasureType::class, mappedBy: 'modules')]
    private Collection $measureTypes;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: MeasuredValue::class)]
    private Collection $measuredValues;

    public function __construct()
    {
        $this->stateHistories = new ArrayCollection();
        $this->measureTypes = new ArrayCollection();
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
     * @return Collection<int, StateHistory>
     */
    public function getStateHistories(): Collection
    {
        return $this->stateHistories;
    }

    public function addStateHistory(StateHistory $stateHistory): static
    {
        if (!$this->stateHistories->contains($stateHistory)) {
            $this->stateHistories->add($stateHistory);
            $stateHistory->setModule($this);
        }

        return $this;
    }

    public function removeStateHistory(StateHistory $stateHistory): static
    {
        if ($this->stateHistories->removeElement($stateHistory)) {
            // set the owning side to null (unless already changed)
            if ($stateHistory->getModule() === $this) {
                $stateHistory->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MeasureType>
     */
    public function getMeasureTypes(): Collection
    {
        return $this->measureTypes;
    }

    public function addMeasureType(MeasureType $measureType): static
    {
        if (!$this->measureTypes->contains($measureType)) {
            $this->measureTypes->add($measureType);
            $measureType->addModule($this);
        }

        return $this;
    }

    public function removeMeasureType(MeasureType $measureType): static
    {
        if ($this->measureTypes->removeElement($measureType)) {
            $measureType->removeModule($this);
        }

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
            $measuredValue->setModule($this);
        }

        return $this;
    }

    public function removeMeasuredValue(MeasuredValue $measuredValue): static
    {
        if ($this->measuredValues->removeElement($measuredValue)) {
            // set the owning side to null (unless already changed)
            if ($measuredValue->getModule() === $this) {
                $measuredValue->setModule(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? ('#' . $this->id);
    }

    public function setMeasureTypes(ArrayCollection|Collection $measureTypes): void
    {
        $this->measureTypes = $measureTypes;
    }
}
