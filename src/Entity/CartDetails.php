<?php

namespace App\Entity;

use App\Repository\CartDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartDetailsRepository::class)
 */
class CartDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $productName;

    /**
     * @ORM\Column(type="float")
     */
    private $productPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $productQuantity;

    /**
     * @ORM\Column(type="float")
     */
    private $subTotalht;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taxe;

    /**
     * @ORM\Column(type="float")
     */
    private $subTotalttc;

    /**
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="CartDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Carts;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;

        return $this;
    }

    public function getProductPrice(): ?float
    {
        return $this->productPrice;
    }

    public function setProductPrice(float $productPrice): self
    {
        $this->productPrice = $productPrice;

        return $this;
    }

    public function getProductQuantity(): ?int
    {
        return $this->productQuantity;
    }

    public function setProductQuantity(int $productQuantity): self
    {
        $this->productQuantity = $productQuantity;

        return $this;
    }

    public function getSubTotalht(): ?float
    {
        return $this->subTotalht;
    }

    public function setSubTotalht(float $subTotalht): self
    {
        $this->subTotalht = $subTotalht;

        return $this;
    }

    public function getTaxe(): ?float
    {
        return $this->taxe;
    }

    public function setTaxe(?float $taxe): self
    {
        $this->taxe = $taxe;

        return $this;
    }

    public function getSubTotalttc(): ?float
    {
        return $this->subTotalttc;
    }

    public function setSubTotalttc(float $subTotalttc): self
    {
        $this->subTotalttc = $subTotalttc;

        return $this;
    }

    public function getCarts(): ?Cart
    {
        return $this->Carts;
    }

    public function setCarts(?Cart $Carts): self
    {
        $this->Carts = $Carts;

        return $this;
    }
}
