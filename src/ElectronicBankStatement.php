<?php

namespace PhpTwinfield;

use Money\Currency;
use Money\Money;
use PhpTwinfield\Enums\DebitCredit;
use PhpTwinfield\Fields\DateField;
use PhpTwinfield\Fields\OfficeField;
use PhpTwinfield\Fields\Transaction\CloseValueField;
use PhpTwinfield\Fields\Transaction\StartValueField;
use PhpTwinfield\Fields\Transaction\StatementNumberField;
use Webmozart\Assert\Assert;

/**
 * @link https://c3.twinfield.com/webservices/documentation/#/ApiReference/Transactions/BankStatements
 */
class ElectronicBankStatement
{
    use CloseValueField;
    use DateField;
    use OfficeField;
    use StartValueField;
    use StatementNumberField;

    /*
     * Account number. Either account or iban or code should be set.
     *
     * @var ?string
     */
    private $account;

    /*
     * Code of the corresponding bank book. Either account or iban or code should be set.
     *
     * @var ?string
     */
    private $code;

    /*
     * IBAN account number. Either account or iban or code should be set.
     *
     * @var ?string
     */
    private $iban;

    /*
     * Optional attribute to indicate whether duplicates may be imported or not.
     *
     * @var bool
     */
    private $importDuplicate = false;

    /*
     * Contains the bank statement transactions.
     *
     * @var array
     */
    private $transactions = [];

    public function __construct()
    {
        $this->currency   = new Currency("EUR");
        $this->startValue = new Money(0, $this->getCurrency());
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(string $account): void
    {
        $this->account = $account;
        $this->iban = null;
        $this->code = null;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
        $this->account = null;
        $this->iban = null;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): void
    {
        $this->iban = $iban;
        $this->account = null;
        $this->code = null;
    }

    /**
     * @return bool
     */
    public function isImportDuplicate(): bool
    {
        return $this->importDuplicate;
    }

    public function setImportDuplicate(bool $importDuplicate): void
    {
        $this->importDuplicate = $importDuplicate;
    }

    /**
     * @return array|ElectronicBankStatementTransaction[]
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }

    /**
     * @param array|ElectronicBankStatementTransaction[] $transactions
     */
    public function setTransactions(array $transactions): void
    {
        Assert::allIsInstanceOf($transactions, ElectronicBankStatementTransaction::class);
        Assert::notEmpty($this->startvalue);

        $this->transactions = $transactions;

        $this->closevalue = $this->startvalue;

        foreach ($transactions as $transaction) {
            if ($transaction->getDebitCredit() == DebitCredit::CREDIT()) {
                $this->closevalue = $this->closevalue->add($transaction->getValue());
            } else {
                $this->closevalue = $this->closevalue->subtract($transaction->getValue());
            }
        }
    }

}