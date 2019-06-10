<?php

namespace PhpTwinfield\Mappers;

use Money\Currency;
use Money\Money;
use PhpTwinfield\Message\Message;
use PhpTwinfield\Office;
use PhpTwinfield\Util;
use Webmozart\Assert\Assert;

abstract class BaseMapper
{
    /**
     * @throws \PhpTwinfield\Exception
     */
    protected static function setFromTagValue(\DOMDocument $document, string $tag, callable $setter): void
    {
        $value = self::getValueFromTag($document, $tag);

        if ($value === null) {
            return;
        }

        if ($tag === "office") {
            \call_user_func($setter, Office::fromCode($value));
            return;
        }

        if ($tag === "date") {
            \call_user_func($setter, Util::parseDate($value));
            return;
        }

        if ($tag === "startvalue") {
            $currency = new Currency(self::getValueFromTag($document, "currency"));

            \call_user_func($setter, Util::parseMoney($value, $currency));

            return;
        }

        \call_user_func($setter, $value);
    }

    protected static function getValueFromTag(\DOMDocument $document, string $tag): ?string
    {
        /** @var \DOMNodeList $nodelist */
        $nodelist = $document->getElementsByTagName($tag);

        if ($nodelist->length === 0) {
            return null;
        }

        Assert::greaterThanEq($nodelist->length, 1);

        /** @var \DOMElement $element */
        $element = $nodelist[0];

        if ("" === $element->textContent) {
            return null;
        }

        return $element->textContent;
    }

    protected static function checkForMessage($object, \DOMElement $element): void
    {
        if ($element->hasAttribute('msg')) {
            $message = new Message();
            $message->setType($element->getAttribute('msgtype'));
            $message->setMessage($element->getAttribute('msg'));
            $message->setField($element->nodeName);

            $object->addMessage($message);
        }
    }

    protected static function getAttribute(\DOMElement $element, string $fieldTagName, string $attributeName): ?string
    {
        $fieldElement = $element->getElementsByTagName($fieldTagName)->item(0);

        if (!isset($fieldElement)) {
            return null;
        }

        if ($fieldElement->getAttribute($attributeName) === "") {
            return null;
        }

        return $fieldElement->getAttribute($attributeName);
    }

    protected static function getField(\DOMElement $element, string $fieldTagName, $object = null): ?string
    {
        $fieldElement = $element->getElementsByTagName($fieldTagName)->item(0);

        if (!isset($fieldElement)) {
            return null;
        }

        if (isset($object)) {
            self::checkForMessage($object, $fieldElement);
        }

        if ($fieldElement->textContent === "") {
            return null;
        }

        return $fieldElement->textContent;
    }

    protected static function parseBooleanAttribute(?string $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    protected static function parseDateAttribute(?string $value): ?\DateTimeImmutable
    {
        if (false !== strtotime($value)) {
            return Util::parseDate($value);
        }
        
        return null;
    }

    protected static function parseDateTimeAttribute(?string $value): ?\DateTimeImmutable
    {
        if (false !== strtotime($value)) {
            return Util::parseDateTime($value);
        }
        
        return null;
    }

    protected static function parseEnumAttribute(string $enumClass, ?string $value)
    {
        if ($value === null) {
            return null;
        }

        try {
            $classReflex = new \ReflectionClass($enumClass);
            $classConstants = $classReflex->getConstants();

            foreach ($classConstants as $classConstant) {
                if ($value == $classConstant) {
                    return new $enumClass($value);
                }
            }
        } catch (\ReflectionException $e) {
            throw new \Exception("Non existant Enum, got \"{$enumClass}\".");
        }

        return null;
    }

    protected static function parseMoneyAttribute(?float $value): ?Money
    {
        if ($value === null) {
            return null;
        }

        return Util::parseMoney($value, new Currency('EUR'));
    }

    /** @var SomeClassWithMethodsetCode $object2 */
    protected static function parseObjectAttribute(string $objectClass, $object, \DOMElement $element, string $fieldTagName, array $attributes = [])
    {
        if ($objectClass == "DimensionGroupDimension" || $objectClass == "UnknownDimension") {
            if ($objectClass == "DimensionGroupDimension") {
                $type = self::getField($element, "type", $object);
            } elseif ($objectClass == "UnknownDimension") {
                $type = self::getAttribute($element, $fieldTagName, "dimensiontype");
            }

            switch ($type) {
                case "ACT":
                    $objectClass = \PhpTwinfield\Activity::class;
                    break;
                case "AST":
                    $objectClass = \PhpTwinfield\FixedAsset::class;
                    break;
                case "BAS":
                    $objectClass = \PhpTwinfield\GeneralLedger::class;
                    break;
                case "CRD":
                    $objectClass = \PhpTwinfield\Supplier::class;
                    break;
                case "DEB":
                    $objectClass = \PhpTwinfield\Customer::class;
                    break;
                case "KPL":
                    $objectClass = \PhpTwinfield\CostCenter::class;
                    break;
                case "PNL":
                    $objectClass = \PhpTwinfield\GeneralLedger::class;
                    break;
                case "PRJ":
                    $objectClass = \PhpTwinfield\Project::class;
                    break;
                default:
                    throw new \InvalidArgumentException("parseObjectAttribute function does not accept \"{$objectClass}\" as valid input for the \$object argument");
            }
        }

        $object2 = new $objectClass();
        $object2->setCode(self::getField($element, $fieldTagName, $object));

        foreach ($attributes as $attributeName => $method) {
            $object2->$method(self::getAttribute($element, $fieldTagName, $attributeName));
        }

        return $object2;
    }
}