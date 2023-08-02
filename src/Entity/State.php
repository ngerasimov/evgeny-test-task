<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StateRepository::class)]
class State
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 32)]
    private ?string $code = null;

    #[ORM\Column()]
    private ?bool $isOperable = null;

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
     * @return bool|null
     */
    public function isOperable(): ?bool
    {
        return $this->isOperable;
    }

    /**
     * @param bool|null $isOperable
     */
    public function setIsOperable(?bool $isOperable): void
    {
        $this->isOperable = $isOperable;
    }

    public function __toString(): string
    {
        return $this->name ?? ('#' . $this->id);
    }
}
