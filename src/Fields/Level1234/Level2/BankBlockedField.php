<?php

namespace PhpTwinfield\Fields\Level1234\Level2;

trait BankBlockedField
{
    /**
     * Bank blocked field
     * Used by: CustomerBank, SupplierBank
     *
     * @var bool
     */
    private $blocked;

    /**
     * @return bool
     */
    public function getBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function getBlockedToString(): ?string
    {
        return ($this->getBlocked()) ? 'true' : 'false';
    }

    /**
     * @param bool $blocked
     * @return $this
     */
    public function setBlocked(?bool $blocked): self
    {
        $this->blocked = $blocked;
        return $this;
    }

    /**
     * @param string|null $blockedString
     * @return $this
     * @throws Exception
     */
    public function setBlockedFromString(?string $blockedString)
    {
        return $this->setBlocked(filter_var($blockedString, FILTER_VALIDATE_BOOLEAN));
    }
}