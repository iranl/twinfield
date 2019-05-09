<?php
namespace PhpTwinfield\Request\Read;

/**
 * Used to request a specific Article from a certain office and code.
 *
 * @package PhpTwinfield
 * @subpackage Request\Read
 * @author Willem van de Sande <W.vandeSande@MailCoupon.nl>
 * @version 0.0.1
 */
class Article extends Read
{
    /**
     * Sets office and code if they are present.
     *
     * @access public
     */
    public function __construct($office = null, $code = null)
    {
        parent::__construct();

        $this->add('type', 'article');

        if (null !== $office) {
            $this->setOffice($office);
        }

        if (null !== $code) {
            $this->setCode($code);
        }
    }
}
