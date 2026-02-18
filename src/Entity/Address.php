<?php

declare(strict_types=1);

namespace PandawanTechnology\GeolocBundle\Entity;

class Address
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $number;

    private ?string $repetitor = null;

    /**
     * @var Street
     */
    private $street;

    private ?string $latitude = null;

    private ?string $longitude = null;

    public function getAddress(): \Stringable|string
    {
        $output = $this->getNumber();

        if ($repetitor = $this->getRepetitor()) {
            $output .= ' '.$repetitor;
        }

        $street = $this->getStreet();
        $output .= ' '.$street;
        $city = $street->getCity();
        $output .= ', '.$city->getZipCode().' '.$city->getName();
        $output .= ', '.$city->getCountryCode();

        return $output;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getRepetitor(): ?string
    {
        return $this->repetitor;
    }

    public function setRepetitor(?string $repetitor): static
    {
        $this->repetitor = $repetitor;

        return $this;
    }

    public function getStreet(): Street
    {
        return $this->street;
    }

    public function setStreet(Street $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude = null): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude = null): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}
