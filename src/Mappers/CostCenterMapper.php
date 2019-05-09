<?php
namespace PhpTwinfield\Mappers;

use PhpTwinfield\CostCenter;
use PhpTwinfield\Response\Response;

/**
 * Maps a response DOMDocument to the corresponding entity.
 *
 * @package PhpTwinfield
 * @subpackage Mapper
 * @author Yannick Aerssens <y.r.aerssens@gmail.com>
 */
class CostCenterMapper extends BaseMapper
{
    /**
     * Maps a Response object to a clean CostCenter entity.
     *
     * @access public
     *
     * @param \PhpTwinfield\Response\Response $response
     *
     * @return CostCenter
     * @throws \PhpTwinfield\Exception
     */
    public static function map(Response $response)
    {
        // Generate new CostCenter object
        $costCenter = new CostCenter();

        // Gets the raw DOMDocument response.
        $responseDOM = $response->getResponseDocument();

        // Get the root/costcenter element
        $costCenterElement = $responseDOM->documentElement;

        // Set the result and status attribute
        $costCenter->setResult($costCenterElement->getAttribute('result'))
            ->setStatus(self::parseEnumAttribute('Status', $costCenterElement->getAttribute('status')));

        // Set the cost center elements from the cost center element
        $costCenter->setBehaviour(self::parseEnumAttribute('Behaviour', self::getField($costCenter, $costCenterElement, 'behaviour')))
            ->setCode(self::getField($costCenter, $costCenterElement, 'code'))
            ->setInUse(self::parseBooleanAttribute(self::getField($costCenter, $costCenterElement, 'name')))
            ->setName(self::getField($costCenter, $costCenterElement, 'name'))
            ->setOffice(self::parseObjectAttribute('Office', $costCenter, $costCenterElement, 'office', array('name' => 'setName', 'shortname' => 'setShortName')))
            ->setTouched(self::getField($costCenter, $costCenterElement, 'touched'))
            ->setType(self::parseObjectAttribute('DimensionType', $costCenter, $costCenterElement, 'type', array('name' => 'setName', 'shortname' => 'setShortName')))
            ->setUID(self::getField($costCenter, $costCenterElement, 'uid'));

        // Return the complete object
        return $costCenter;
    }
}