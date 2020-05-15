<?php

namespace NFePHP\NFSeDSF;

/**
 * Class for RPS construction and validation of data
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeDSF
 * @copyright NFePHP Copyright (c) 2008-2018
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-dsf for the canonical source repository
 */

use stdClass;
use NFePHP\Common\Certificate;
use NFePHP\NFSeDSF\RpsInterface;
use NFePHP\NFSeDSF\Common\Factory;
use JsonSchema\Validator as JsonValid;

class Rps implements RpsInterface
{
    /**
     * @var stdClass
     */
    public $std;
    /**
     * @var string
     */
    protected $ver;
    /**
     * @var string
     */
    protected $jsonschema;
    
    protected $assinatura;


    /**
     * Constructor
     * @param stdClass $rps
     */
    public function __construct(stdClass $rps)
    {
        $this->init($rps);
    }
    
    /**
     * Convert Rps::class data in XML
     * @param Certificate $certificate
     * @return string
     */
    public function render(Certificate $certificate = null)
    {
        $fac = new Factory($this->std);
        return $fac->render($certificate);
    }
    
    /**
     * Inicialize properties and valid input
     * @param stdClass $rps
     */
    private function init(stdClass $rps)
    {
        $this->std = $this->propertiesToLower($rps);
        $this->jsonschema = \Safe\realpath("../storage/jsonSchemes/rps.schema");
        $this->validInputData($this->std);
    }
    
    /**
     * Change properties names of stdClass to lower case
     * @param stdClass $data
     * @return stdClass
     */
    public static function propertiesToLower(stdClass $data)
    {
        $properties = get_object_vars($data);
        $clone = new stdClass();
        foreach ($properties as $key => $value) {
            if ($value instanceof stdClass) {
                $value = self::propertiesToLower($value);
            }
            $nk = strtolower($key);
            $clone->{$nk} = $value;
        }
        return $clone;
    }

    /**
     * Validation json data from json Schema
     * @param stdClass $data
     * @return boolean
     * @throws \RuntimeException
     */
    protected function validInputData($data)
    {
        if (!is_file($this->jsonschema)) {
            return true;
        }
        $validator = new JsonValid();
        $validator->check($data, (object)['$ref' => 'file://' . $this->jsonschema]);
        if (!$validator->isValid()) {
            $msg = "";
            foreach ($validator->getErrors() as $error) {
                $msg .= \Safe\sprintf("[%s] %s\n", $error['property'], $error['message']);
            }
            throw new \InvalidArgumentException($msg);
        }
        return true;
    }
}
