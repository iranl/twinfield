<?php

namespace PhpTwinfield\IntegrationTests;

use DateTimeImmutable;
use Money\Currency;
use Money\Money;
use PhpTwinfield\ApiConnectors\TransactionApiConnector;
use PhpTwinfield\BankTransaction;
use PhpTwinfield\BankTransactionLine;
use PhpTwinfield\DomDocuments\TransactionsDocument;
use PhpTwinfield\Enums\DebitCredit;
use PhpTwinfield\Enums\Destiny;
use PhpTwinfield\Enums\LineType;
use PhpTwinfield\Mappers\TransactionMapper;
use PhpTwinfield\Office;
use PhpTwinfield\Response\Response;

/**
 * @covers BankTransaction
 * @covers BankTransactionLine
 * @covers TransactionsDocument
 * @covers TransactionMapper
 * @covers TransactionApiConnector
 */
class BankTransactionIntegrationTest extends BaseIntegrationTest
{
    /**
     * @var TransactionApiConnector|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionApiConnector;

    protected function setUp()
    {
        parent::setUp();

        $this->transactionApiConnector = new TransactionApiConnector($this->connection);
    }

    public function testGetBankTransactionWorks()
    {
        $response = Response::fromString(file_get_contents(__DIR__ . '/resources/bankTransactionGetResponse.xml'));

        $this->processXmlService
            ->expects($this->once())
            ->method("sendDocument")
            ->with($this->isInstanceOf(\PhpTwinfield\Request\Read\Transaction::class))
            ->willReturn($response);

        /** @var BankTransaction $bankTransaction */
        $bankTransaction = $this->transactionApiConnector->get(BankTransaction::class, 'BNK', '201300008', $this->office);

        $this->assertInstanceOf(BankTransaction::class, $bankTransaction);
        $this->assertEquals(Destiny::TEMPORARY(), $bankTransaction->getDestiny());
        $this->assertNull($bankTransaction->isAutoBalanceVat());
        $this->assertSame(false, $bankTransaction->getRaiseWarning());
        $this->assertEquals(Office::fromCode('001'), $bankTransaction->getOffice());
        $this->assertSame('BNK', $bankTransaction->getCode());
        $this->assertSame(201300008, $bankTransaction->getNumber());
        $this->assertSame('2013/11', $bankTransaction->getPeriod());
        $this->assertEquals(new Currency('EUR'), $bankTransaction->getCurrency());
        $this->assertEquals(new DateTimeImmutable('2013-11-04'), $bankTransaction->getDate());
        $this->assertSame('import', $bankTransaction->getOrigin());
        $this->assertNull($bankTransaction->getFreetext1());
        $this->assertNull($bankTransaction->getFreetext2());
        $this->assertNull($bankTransaction->getFreetext3());
        $this->assertSame(4, $bankTransaction->getStatementnumber());
        $this->assertTrue(Money::EUR(97401)->equals($bankTransaction->getStartvalue()));
        $this->assertTrue(Money::EUR(140956)->equals($bankTransaction->getClosevalue()));

        /** @var BankTransactionLine[] $bankTransactionLines */
        $bankTransactionLines = $bankTransaction->getLines();
        $this->assertCount(2, $bankTransactionLines);
        [$totalLine, $detailLine] = $bankTransactionLines;

        $this->assertEquals(LineType::TOTAL(), $totalLine->getLineType());
        $this->assertSame(1, $totalLine->getId());
        $this->assertSame('1001', $totalLine->getDim1());
        $this->assertEquals(DebitCredit::DEBIT(), $totalLine->getDebitCredit());
        $this->assertEquals(Money::EUR(43555), $totalLine->getValue());
        $this->assertEquals(Money::EUR(43555), $totalLine->getBaseValue());
        $this->assertSame(1.0, $totalLine->getRate());
        $this->assertEquals(Money::EUR(65333), $totalLine->getRepValue());
        $this->assertSame(1.500000000, $totalLine->getRepRate());
        $this->assertSame(BankTransactionLine::MATCHSTATUS_NOTMATCHABLE, $totalLine->getMatchStatus());
        $this->assertNull($totalLine->getMatchLevel());
        $this->assertNull($totalLine->getBaseValueOpen());
        $this->assertNull($totalLine->getVatCode());
        $this->assertNull($totalLine->getVatValue());
        $this->assertNull($totalLine->getVatTotal());
        $this->assertNull($totalLine->getVatBaseTotal());
        $this->assertNull($totalLine->getPerformanceType());
        $this->assertNull($totalLine->getPerformanceCountry());
        $this->assertNull($totalLine->getPerformanceVatNumber());
        $this->assertNull($totalLine->getPerformanceDate());

        $this->assertEquals(LineType::DETAIL(), $detailLine->getLineType());
        $this->assertSame(2, $detailLine->getId());
        $this->assertSame('1300', $detailLine->getDim1());
        $this->assertSame('1000', $detailLine->getDim2());
        $this->assertEquals(DebitCredit::CREDIT(), $detailLine->getDebitCredit());
        $this->assertSame('11001770', $detailLine->getInvoiceNumber());
        $this->assertEquals(Money::EUR(43555), $detailLine->getValue());
        $this->assertEquals(Money::EUR(43555), $totalLine->getBaseValue());
        $this->assertSame(1.0, $totalLine->getRate());
        $this->assertEquals(Money::EUR(65333), $totalLine->getRepValue());
        $this->assertSame(1.500000000, $totalLine->getRepRate());
        $this->assertSame('Invoice paid', $detailLine->getDescription());
        $this->assertSame(BankTransactionLine::MATCHSTATUS_AVAILABLE, $detailLine->getMatchStatus());
        $this->assertSame(2, $detailLine->getMatchLevel());
        $this->assertEquals(Money::EUR(43555), $detailLine->getBaseValueOpen());
        $this->assertEquals(Money::EUR(65333), $detailLine->getRepValue());
        $this->assertNull($detailLine->getVatCode());
        $this->assertNull($detailLine->getVatValue());
        $this->assertNull($detailLine->getVatTotal());
        $this->assertNull($detailLine->getVatBaseTotal());
        $this->assertNull($detailLine->getPerformanceType());
        $this->assertNull($detailLine->getPerformanceCountry());
        $this->assertNull($detailLine->getPerformanceVatNumber());
        $this->assertNull($detailLine->getPerformanceDate());
    }

    public function testSendBankTransactionWorks()
    {
        $bankTransaction = new BankTransaction();
        $bankTransaction
            ->setOffice(Office::fromCode('001'))
            ->setDestiny(Destiny::TEMPORARY())
            ->setRaiseWarning(false)
            ->setCode('BNK')
            ->setCurrency(new Currency('EUR'))
            ->setDate(new DateTimeImmutable('2013-11-04'))
            ->setStatementnumber(4)
            ->setStartvalue(Money::EUR(97401));

        $totalLine = new BankTransactionLine();
        $totalLine
            ->setLineType(LineType::TOTAL())
            ->setId('1')
            ->setDim1('1001')
            ->setValue(Money::EUR(43555));

        $detailLine = new BankTransactionLine();
        $detailLine
            ->setLineType(LineType::DETAIL())
            ->setId('2')
            ->setDim1('1300')
            ->setDim2('1000')
            ->setValue(Money::EUR(43555))
            ->setInvoiceNumber('11001770')
            ->setDescription('Invoice paid');

        $bankTransaction
            ->addLine($totalLine)
            ->addLine($detailLine);

        $this->processXmlService
            ->expects($this->once())
            ->method("sendDocument")
            ->with($this->isInstanceOf(TransactionsDocument::class))
            ->willReturnCallback(function (TransactionsDocument $transactionsDocument) {
                $this->assertXmlStringEqualsXmlString(
                    file_get_contents(realpath(__DIR__ . '/resources/bankTransactionSendRequest.xml')),
                    $transactionsDocument->saveXML()
                );

                return $this->getSuccessfulResponse();
            });

        $this->transactionApiConnector->send($bankTransaction);
    }

    protected function getSuccessfulResponse(): Response
    {
        return Response::fromString(
            '<transactions result="1"><transaction location="temporary">
                <header>
                    <office name="001" shortname="001">001</office>
                    <code name="Bank" shortname="Bank">BNK</code>
                    <currency name="Euro" shortname="Euro">EUR</currency>
                    <date>20131104</date>
                    <statementnumber>4</statementnumber>
                    <startvalue>974.01</startvalue>
                    <closevalue>1409.56</closevalue>
                    <origin>import</origin>
                    <user name="Marcel" shortname="Marcel">MARCEL</user>
                    <period>2013/11</period>
                    <number>201300008</number>
                </header>
            </transaction>
        </transactions>'
        );
    }
}
