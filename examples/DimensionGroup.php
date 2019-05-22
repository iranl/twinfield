<?php

/* DimensionGroup
 * Twinfield UI:            https://accounting.twinfield.com/UI/#/Settings/Company/DimensionGroups
 * API Documentation:       https://c3.twinfield.com/webservices/documentation/#/ApiReference/Masters/DimensionGroups
 */

//Optionally declare the namespace PhpTwinfield so u can call classes without prepending \PhpTwinfield\
namespace PhpTwinfield;

// Use the ResponseException class to handle errors when listing, getting and sending objects to/from Twinfield
use PhpTwinfield\Response\ResponseException;

require_once('vendor/autoload.php');

// Retrieve an OAuth 2 connection
require_once('Connection.php');

/* DimensionGroup API Connector
 * \PhpTwinfield\ApiConnectors\DimensionGroupApiConnector
 * Available methods: delete, get, listAll, send, sendAll
 */

// Run all or only some of the following examples
$executeListAllWithFilter           = false;
$executeListAllWithoutFilter        = true;
$executeRead                        = false;
$executeCopy                        = false;
$executeNew                         = false;
$executeDelete                      = false;

$dimensionGroupApiConnector = new \PhpTwinfield\ApiConnectors\DimensionGroupApiConnector($connection);

// Office code
$officeCode = "SomeOfficeCode";

// Create a new Office object from the $officeCode
$office = \PhpTwinfield\Office::fromCode($officeCode);

/* List all dimension groups
 * @param string $pattern  The search pattern. May contain wildcards * and ?
 * @param int    $field    The search field determines which field or fields will be searched. The available fields
 *                         depends on the finder type. Passing a value outside the specified values will cause an
 *                         error.
 * @param int    $firstRow First row to return, useful for paging
 * @param int    $maxRows  Maximum number of rows to return, useful for paging
 * @param array  $options  The Finder options. Passing an unsupported name or value causes an error. It's possible
 *                         to add multiple options. An option name may be used once, specifying an option multiple
 *                         times will cause an error.
 *
 *                         Available options:      office, vattype
 *
 *                         office                  Sets the office code.
 *                         Usage:                  $options['office'] = 'SomeOfficeCode';
 *
 *                         prefix                  Specifies that the search field should start with the given value.
 *                         Usage:                  $options['prefix'] = 'DIM';
 *
 *                         dimtype                 Specifies the dimension type. Returns all groups that contain at least one dimension with the specified type
 *                         Available values:       BAS, PNL, DEB, CRD, KPL, PRJ, AST, ACT
 *                         Usage:                  $options['dimtype'] = 'BAS';
 *
 */

//List all with pattern "*", field 0 (= search code or number), firstRow 1, maxRows 10, options -> dimtype = 'BAS'
if ($executeListAllWithFilter) {
    $options = array('dimtype' => 'BAS');

    try {
        $dimensionGroups = $dimensionGroupApiConnector->listAll("*", 0, 1, 10, $options);
    } catch (ResponseException $e) {
        $dimensionGroups = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroups);
    echo "</pre>";
}

//List all with default settings (pattern '*', field 0, firstRow 1, maxRows 100, options [])
if ($executeListAllWithoutFilter) {
    try {
        $dimensionGroups = $dimensionGroupApiConnector->listAll();
    } catch (ResponseException $e) {
        $dimensionGroups = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroups);
    echo "</pre>";
}

/* DimensionGroup
 * \PhpTwinfield\DimensionGroup
 * Available getters: getCode, getMessages, getName, getOffice, getOfficeToString, getResult, getShortName, getStatus, hasMessages, getDimensions
 * Available setters: setCode, setName, setOffice, setOfficeFromString, setShortName, setStatus, setStatusFromString, addDimension,removeDimension
 */

/* DimensionGroupDimension
 * \PhpTwinfield\DimensionGroupDimension
 * Available getters: getCode, getCodeToString, getMessages, getResult, getType, getTypeToString, hasMessages
 * Available setters: setCode, setCodeFromString, setType, setTypeFromString
 */

if ($executeListAllWithFilter || $executeListAllWithoutFilter) {
    foreach ($dimensionGroups as $key => $dimensionGroup) {
        echo "DimensionGroup {$key}<br />";
        echo "Code: {$dimensionGroup->getCode()}<br />";
        echo "Name: {$dimensionGroup->getName()}<br /><br />";
    }
}

// Read a DimensionGroup based off the passed in code and optionally the office.
if ($executeRead) {
    try {
        $dimensionGroup = $dimensionGroupApiConnector->get("TSTDIMGRP", $office);
    } catch (ResponseException $e) {
        $dimensionGroup = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroup);
    echo "</pre>";

    echo "DimensionGroup<br />";
    echo "Code: {$dimensionGroup->getCode()}<br />";                                                                                   					// string|null                  Dimension group code.

    if ($dimensionGroup->hasMessages()) {                                                                                              					// bool                         Object contains (error) messages true/false.
        echo "Messages: " . print_r($dimensionGroup->getMessages(), true) . "<br />";                                                  					// Array|null                   (Error) messages.
    }

    echo "Name: {$dimensionGroup->getName()}<br />";                                                                                   					// string|null                  Name of the dimension group.
    echo "Office (\\PhpTwinfield\\Office): <pre>" . print_r($dimensionGroup->getOffice(), true) . "</pre><br />";                      					// Office|null                  Office code.
    echo "Office (string): {$dimensionGroup->getOfficeToString()}<br />";                                                              					// string|null
    echo "Result: {$dimensionGroup->getResult()}<br />";                                                                               					// int|null                     Result (0 = error, 1 or empty = success).
    echo "ShortName: {$dimensionGroup->getShortName()}<br />";                                                                         					// string|null                  Short name of the dimension group.
    echo "Status: {$dimensionGroup->getStatus()}<br />";                                                                               					// Status|null                  Status of the dimension group.

    $dimensionGroupDimensions = $dimensionGroup->getDimensions();                                                                           			// Array|null                   Array of DimensionGroupDimension objects.

    foreach ($dimensionGroupDimensions as $key => $dimensionGroupDimension) {
        echo "DimensionGroupDimension {$key}<br />";

        if ($dimensionGroupDimension->hasMessages()) {                                                                                					// bool                         Object contains (error) messages true/false.
            echo "Messages: " . print_r($dimensionGroupDimension->getMessages(), true) . "<br />";                                    					// Array|null                   (Error) messages.
        }

        echo "Code: <pre>" . print_r($dimensionGroupDimension->getCode(), true) . "</pre><br />";                                                       // object|null                  Code of the dimension.
        echo "Code (string): {$dimensionGroupDimension->getCodeToString()}<br />";                                                                      // string|null
        echo "Result: {$dimensionGroupDimension->getResult()}<br />";                                                                                   // int|null                     Result (0 = error, 1 or empty = success).
        echo "Type (\\PhpTwinfield\\DimensionType): <pre>" . print_r($dimensionGroupDimension->getType(), true) . "</pre><br />";                       // DimensionType|null           Dimension type.
        echo "Type (string): {$dimensionGroupDimension->getTypeToString()}<br />";                                                                      // string|null
    }
}

// Copy an existing DimensionGroup to a new entity
if ($executeCopy) {
    try {
        $dimensionGroup = $dimensionGroupApiConnector->get("TSTDIMGRP", $office);
    } catch (ResponseException $e) {
        $dimensionGroup = $e->getReturnedObject();
    }

    $dimensionGroup->setCode("TSTDIMGRP2");

    try {
        $dimensionGroupCopy = $dimensionGroupApiConnector->send($dimensionGroup);
    } catch (ResponseException $e) {
        $dimensionGroupCopy = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroupCopy);
    echo "</pre>";

    echo "Result of copy process: {$dimensionGroupCopy->getResult()}<br />";
    echo "Code of copied DimensionGroup: {$dimensionGroupCopy->getCode()}<br />";
}

// Create a new DimensionGroup from scratch, alternatively read an existing DimensionGroup as shown above and than modify the values in the same way as shown below
if ($executeNew) {
    $dimensionGroup = new \PhpTwinfield\DimensionGroup;

    // Required values for creating a new DimensionGroup
    $dimensionGroup->setCode('DIMGRP2');                                                                                // string|null                    Dimension group code.
    $dimensionGroup->setName("Dimension Group 2");                                                                      // string|null                    Name of the dimension group.
    $dimensionGroup->setOffice($office);                                                                                // Office|null                    Office code.
    $dimensionGroup->setOfficeFromString($officeCode);                                                                  // string|null
    $dimensionGroup->setStatus(\PhpTwinfield\Enums\Status::ACTIVE());                                                   // Status|null                    For creating and updating active should be used. For deleting deleted should be used.
    //$dimensionGroup->setStatus(\PhpTwinfield\Enums\Status::DELETED());                                                // Status|null
    $dimensionGroup->setStatusFromString('active');                                                                     // string|null
    //$dimensionGroup->setStatusFromString('deleted');                                                                  // string|null

    // Optional values for creating a new DimensionGroup
    $dimensionGroup->setShortName("DIM GRP 2");                                                                         // string|null                    Short name of the dimension group.

    // The minimum amount of DimensionGroupDimensions linked to a DimensionGroup object is 0
    $dimensionGroupDimension = new \PhpTwinfield\DimensionGroupDimension;
    $type = new \PhpTwinfield\DimensionType;
    $type->setCode('BAS');
    $code = new \PhpTwinfield\GeneralLedger;
    $code->setType($type);
    $code->setCode('1010');
    $dimensionGroupDimension->setCode($code);                                                                           // object|null                    Code of the dimension.
    $dimensionGroupDimension->setCodeFromString('1010');                                                                // string|null
    $dimensionGroupDimension->setType($type);                                                                           // DimensionType|null             Dimension type.
    $dimensionGroupDimension->setTypeFromString('BAS');                                                                 // string|null

    $dimensionGroup->addDimension($dimensionGroupDimension);                                                            // DimensionGroupDimension        Add a DimensionGroupDimension object to the DimensionGroup object
    //$dimensionGroup->removeDimension(0);                                                                              // int                            Remove a dimension based on the index of the dimension within the array

    try {
        $dimensionGroupNew = $dimensionGroupApiConnector->send($dimensionGroup);
    } catch (ResponseException $e) {
        $dimensionGroupNew = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroupNew);
    echo "</pre>";

    echo "Result of creation process: {$dimensionGroupNew->getResult()}<br />";
    echo "Code of new DimensionGroup: {$dimensionGroupNew->getCode()}<br />";
}

// Delete a DimensionGroup based off the passed in code and optionally the office.
if ($executeDelete) {
    try {
        $dimensionGroupDeleted = $dimensionGroupApiConnector->delete("TSTDIMGRP2", $office);
    } catch (ResponseException $e) {
        $dimensionGroupDeleted = $e->getReturnedObject();
    }

    echo "<pre>";
    print_r($dimensionGroupDeleted);
    echo "</pre>";

    echo "Result of deletion process: {$dimensionGroupDeleted->getResult()}<br />";
}