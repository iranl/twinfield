<?php

namespace PhpTwinfield;

use PhpTwinfield\Fields\BehaviourField;
use PhpTwinfield\Fields\CodeField;
use PhpTwinfield\Fields\Dimensions\DimensionType\TypeField;
use PhpTwinfield\Fields\InUseField;
use PhpTwinfield\Fields\NameField;
use PhpTwinfield\Fields\OfficeField;
use PhpTwinfield\Fields\ShortNameField;
use PhpTwinfield\Fields\StatusField;
use PhpTwinfield\Fields\TouchedField;
use PhpTwinfield\Fields\UIDField;
use PhpTwinfield\Fields\VatCodeField;

/**
 * Class Activity
 *
 * @author Yannick Aerssens <y.r.aerssens@gmail.com>
 */
class Activity extends BaseObject
{
    use BehaviourField;
    use CodeField;
    use InUseField;
    use NameField;
    use OfficeField;
    use ShortNameField;
    use StatusField;
    use TouchedField;
    use TypeField;
    use UIDField;
    use VatCodeField;

    private $projects;

    public function __construct()
    {
        $this->setTypeFromString('ACT');
        $this->setProjects(new ActivityProjects);
    }

    public function getProjects(): ActivityProjects
    {
        return $this->projects;
    }

    public function setProjects(ActivityProjects $projects)
    {
        $this->projects = $projects;
        return $this;
    }
}