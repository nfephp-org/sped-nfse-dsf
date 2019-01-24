<?php

namespace NFePHP\NFSeDSF\Common;

/**
 * Auxiar Tools Class for comunications with NFSe webserver in Nacional Standard
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

use NFePHP\Common\Certificate;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\NFSeDSF\RpsInterface;
use NFePHP\NFSeDSF\Common\Signer;
use NFePHP\NFSeDSF\Common\Soap\SoapInterface;
use NFePHP\NFSeDSF\Common\Soap\SoapCurl;

class Tools
{
    public $lastRequest;
    
    protected $config;
    protected $prestador;
    protected $certificate;
    protected $wsobj;
    protected $soap;
    protected $environment;
    
    protected $urls = [
        '150142' => [
            'municipio' => 'Belem',
            'uf' => 'PA',
            'siaf' => '0427',
            'homologacao' => 'http://www.issdigitalbel.com.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://www.issdigitalbel.com.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ],
        '3509502' => [
            'municipio' => 'Campinas',
            'uf' => 'SP',
            'siaf' => '6291',
            'homologacao' => 'http://issdigital.campinas.sp.gov.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://issdigital.campinas.sp.gov.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ],
        '5002704' => [
            'municipio' => 'Campo Grande',
            'uf' => 'MS',
            'siaf' => '9051',
            'homologacao' => 'http://issdigital.pmcg.ms.gov.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://issdigital.pmcg.ms.gov.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ],
        '3303500' => [
            'municipio' => 'Nova Iguacu',
            'uf' => 'RJ',
            'siaf' => '5869',
            'homologacao' => 'http://www.notamaisfacil.novaiguacu.rj.gov.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://www.notamaisfacil.novaiguacu.rj.gov.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://wsnfe2.dsfnet.com.br'
        ],
        '2111300' => [
            'municipio' => 'Sao Luiz',
            'uf' => 'MA',
            'siaf' => '0921',
            'homologacao' => 'http://homo.stm.semfaz.saoluis.ma.gov.br/WsNFe2/LoteRps',
            'producao' => 'http://stm.semfaz.saoluis.ma.gov.br/WsNFe2/LoteRps',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://sistemas.semfaz.saoluis.ma.gov.br/WsNFe2/LoteRps.jws'
        ],
        '3552205' => [
            'municipio' => 'Sorocaba',
            'uf' => 'SP',
            'siaf' => '7145',
            'homologacao' => 'http://www.issdigitalsod.com.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://www.issdigitalsod.com.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ],
        '2211001' => [
            'municipio' => 'Terezina',
            'uf' => 'PI',
            'siaf' => '1219',
            'homologacao' => 'http://www.issdigitalthe.com.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://www.issdigitalthe.com.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ],
        '3170206' => [
            'municipio' => 'Uberlandia',
            'uf' => 'MG',
            'siaf' => '5403',
            'homologacao' => 'http://udigital.uberlandia.mg.gov.br/WsNFe2/LoteRps.jws',
            'producao' => 'http://udigital.uberlandia.mg.gov.br/WsNFe2/LoteRps.jws',
            'version' => '1',
            'msgns' => '',
            'soapns' => 'http://proces.wsnfe2.dsfnet.com.br'
        ]
    ];
    
    /**
     * Constructor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        $this->config = \Safe\json_decode($config);
        $this->certificate = $cert;
        $this->buildPrestadorTag();
        $wsobj = $this->urls;
        $this->wsobj = \Safe\json_decode(\Safe\json_encode($this->urls[$this->config->cmun]));
        $this->environment = 'homologacao';
        if ($this->config->tpamb === 1) {
            $this->environment = 'producao';
        }
    }
    
    /**
     * SOAP communication dependency injection
     * @param SoapInterface $soap
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
    }
    
    /**
     * Build tag Prestador
     */
    protected function buildPrestadorTag()
    {
        $this->prestador = "<Prestador>"
            . "<Cnpj>" . $this->config->cnpj . "</Cnpj>"
            . "<InscricaoMunicipal>" . $this->config->im . "</InscricaoMunicipal>"
            . "</Prestador>";
    }

    /**
     * Sign XML passing in content
     * @param string $content
     * @param string $tagname
     * @param string $mark
     * @return string XML signed
     */
    public function sign($content, $tagname, $mark)
    {
        $xml = Signer::sign(
            $this->certificate,
            $content,
            $tagname,
            $mark
        );
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        return $dom->saveXML($dom->documentElement);
    }
    
    /**
     * Send message to webservice
     * @param string $message
     * @param string $operation
     * @return string XML response from webservice
     */
    public function send($message, $operation)
    {
        $action = "";
        $url = $this->wsobj->homologacao;
        if ($this->environment === 'producao') {
            $url = $this->wsobj->producao;
        }
        $request = $this->createSoapRequest($message, $operation);
        $this->lastRequest = $request;
        
        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
        $msgSize = strlen($request);
        $parameters = [
            "Content-Type: text/xml;charset=UTF-8",
            "SOAPAction: \"$action\"",
            "Content-length: $msgSize"
        ];
        $response = (string) $this->soap->send(
            $operation,
            $url,
            $action,
            $request,
            $parameters
        );
        return $response; //$this->extractContentFromResponse($response, $operation);
    }
    
    /**
     * Extract xml response from CDATA outputXML tag
     * @param string $response Return from webservice
     * @return string XML extracted from response
     */
    protected function extractContentFromResponse($response, $operation)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($response);
        if (!empty($dom->getElementsByTagName('outputXML')->item(0))) {
            $node = $dom->getElementsByTagName('outputXML')->item(0);
            return $node->textContent;
        }
        return $response;
    }

    /**
     * Build SOAP request
     * @param string $message
     * @param string $operation
     * @return string XML SOAP request
     */
    protected function createSoapRequest($message, $operation)
    {
        $env = "<soapenv:Envelope "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" "
            . "xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" "
            . "xmlns:dsf=\"{$this->wsobj->soapns}\">"
            . "<soapenv:Body>"
            . "<dsf:$operation soapenv:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">"
            . "<mensagemXml xsi:type=\"xsd:string\"></mensagemXml>"
            . "</dsf:$operation>"
            . "</soapenv:Body>"
            . "</soapenv:Envelope>";

        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($env);
        $node = $dom->getElementsByTagName('mensagemXml')->item(0);
        $cdata = $dom->createCDATASection($message);
        $node->appendChild($cdata);
        return $dom->saveXML($dom->documentElement);
    }

    /**
     * Create tag Prestador and insert into RPS xml
     * @param RpsInterface $rps
     * @return string RPS XML (not signed)
     */
    protected function putPrestadorInRps(RpsInterface $rps)
    {
        $dom = new Dom('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($rps->render());
        $referenceNode = $dom->getElementsByTagName('Servico')->item(0);
        $node = $dom->createElement('Prestador');
        $dom->addChild(
            $node,
            "Cnpj",
            $this->config->cnpj,
            true
        );
        $dom->addChild(
            $node,
            "InscricaoMunicipal",
            $this->config->im,
            true
        );
        $dom->insertAfter($node, $referenceNode);
        return $dom->saveXML($dom->documentElement);
    }
}
