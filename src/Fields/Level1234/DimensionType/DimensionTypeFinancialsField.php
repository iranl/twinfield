<?php

namespace PhpTwinfield\Fields\Level1234\DimensionType;

trait DimensionTypeFinancialsField
{
    /**
     * Dimension type financials field
     * Used by: DimensionTypeLevels
     *
     * @var int|null
     */
    private $financials;

    /**
     * @return null|int
     */
    public function getFinancials(): ?int
    {
        return $this->financials;
    }

    /**
     * @param null|int $financials
     * @return $this
     */
    public function setFinancials(?int $financials): self
    {
        $this->financials = $financials;
        return $this;
    }
}