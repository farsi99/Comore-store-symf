<?php

namespace App\Entity;



class SearchProduct
{

    private $minprice = null;


    private $maxprice = null;

    /**
     * @var Categories[]
     */
    private $categories = [];


    private $tags = null;


    public function getMinprice(): ?int
    {
        return $this->minprice;
    }

    public function setMinprice(?int $minprice): self
    {
        $this->minprice = $minprice;

        return $this;
    }

    public function getMaxprice(): ?int
    {
        return $this->maxprice;
    }

    public function setMaxprice(?int $maxprice): self
    {
        $this->maxprice = $maxprice;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }
}
