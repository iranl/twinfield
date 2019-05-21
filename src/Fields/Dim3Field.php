<?php

namespace PhpTwinfield\Fields;

use PhpTwinfield\Dummy;

/**
 * The dimension
 * Used by: BaseTransactionLine, FixedAssetTransactionLine
 *
 * @package PhpTwinfield\Traits
 */
trait Dim3Field
{
    /**
     * @var object|null
     */
    private $dim3;

    public function getDim3()
    {
        return $this->dim3;
    }

    public function getDim3ToString(): ?string
    {
        if ($this->getDim3() != null) {
            return $this->dim3->getCode();
        } else {
            return null;
        }
    }

    /**
     * @return $this
     */
    public function setDim3($dim3): self
    {
        $this->dim3 = $dim3;
        return $this;
    }
    
    /**
     * @param string|null $dim3String
     * @return $this
     * @throws Exception
     */
    public function setDim3FromString(?string $dim3String)
    {
        $dim3 = new Dummy();
        $dim3->setCode($dim3String);
        return $this->setDim3($dim3);
    }
}
