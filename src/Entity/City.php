<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class City
{
    /**
     * @var int
     */
    private $id;

    private ?string $inseeCode = null;

    /**
     * @var string
     */
    private $postCode;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var Collection<int, Street>
     */
    private Collection $streets;

    public function __construct()
    {
        $this->streets = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getInseeCode(): ?string
    {
        return $this->inseeCode;
    }

    public function setInseeCode(?string $inseeCode = null): static
    {
        $this->inseeCode = $inseeCode;

        return $this;
    }

    public function getPostCode(): string
    {
        return $this->postCode;
    }

    public function setPostCode(string $postCode): static
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return Collection<int, Street>
     */
    public function getStreets(): Collection
    {
        return $this->streets;
    }

    public function addStreet(Street $street): static
    {
        if (!$this->streets->contains($street)) {
            $this->streets->add($street);
        }

        $street->setCity($this);

        return $this;
    }

    public function removeStreet(Street $street): static
    {
        $this->streets->removeElement($street);

        return $this;
    }
}
