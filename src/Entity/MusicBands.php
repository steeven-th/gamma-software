<?php

namespace App\Entity;

use App\Repository\MusicBandsRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicBandsRepository::class)]
class MusicBands {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    private ?string $origin = null;

    #[ORM\Column(length: 50)]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $startYear = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $endYear = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $founders = null;

    #[ORM\Column(nullable: true)]
    private ?int $members = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $musicalStyle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getOrigin(): ?string {
        return $this->origin;
    }

    public function setOrigin(string $origin): static {
        $this->origin = $origin;

        return $this;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(string $city): static {
        $this->city = $city;

        return $this;
    }

    public function getStartYear(): ?DateTimeInterface {
        return $this->startYear;
    }

    public function setStartYear(DateTimeInterface $startYear): static {
        $this->startYear = $startYear;

        return $this;
    }

    public function getEndYear(): ?DateTimeInterface {
        return $this->endYear;
    }

    public function setEndYear(DateTimeInterface $endYear): static {
        $this->endYear = $endYear;

        return $this;
    }

    public function getFounders(): ?string {
        return $this->founders;
    }

    public function setFounders(?string $founders): static {
        $this->founders = $founders;

        return $this;
    }

    public function getMembers(): ?int {
        return $this->members;
    }

    public function setMembers(?int $members): static {
        $this->members = $members;

        return $this;
    }

    public function getMusicalStyle(): ?string {
        return $this->musicalStyle;
    }

    public function setMusicalStyle(?string $musicalStyle): static {
        $this->musicalStyle = $musicalStyle;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): static {
        $this->description = $description;

        return $this;
    }
}
