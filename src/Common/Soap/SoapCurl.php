<?php

namespace NFePHP\NFSeDSF\Common\Soap;

/**
 * SoapClient based in cURL class
 *
 * @category  NFePHP
 * @package   NFePHP\NFSeDSF
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfse-dsf for the canonical source repository
 */

use NFePHP\NFSeDSF\Common\Soap\SoapBase;
use NFePHP\NFSeDSF\Common\Soap\SoapInterface;
use NFePHP\Common\Exception\SoapException;
use NFePHP\Common\Certificate;
use Psr\Log\LoggerInterface;

class SoapCurl extends SoapBase implements SoapInterface
{
    /**
     * Constructor
     * @param Certificate $certificate
     * @param LoggerInterface $logger
     */
    public function __construct(Certificate $certificate = null, LoggerInterface $logger = null)
    {
        parent::__construct($certificate, $logger);
    }
    
    /**
     * Send soap message to url
     * @param string $operation
     * @param string $url
     * @param string $action
     * @param string $envelope
     * @param array $parameters
     * @return string
     * @throws \NFePHP\Common\Exception\SoapException
     */
    public function send(
        $operation,
        $url,
        $action,
        $envelope,
        $parameters
    ) {
        $response = '';
        $this->requestHead = implode("\n", $parameters);
        $this->requestBody = $envelope;
        
        try {
            $this->saveTemporarilyKeyFiles();
            $oCurl = \Safe\curl_init();
            $this->setCurlProxy($oCurl);
            \Safe\curl_setopt($oCurl, CURLOPT_URL, $url);
            \Safe\curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            \Safe\curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
            \Safe\curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
            \Safe\curl_setopt($oCurl, CURLOPT_HEADER, 1);
            \Safe\curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
            \Safe\curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
            if (!$this->disablesec) {
                \Safe\curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 2);
                if (is_file($this->casefaz)) {
                    \Safe\curl_setopt($oCurl, CURLOPT_CAINFO, $this->casefaz);
                }
            }
            \Safe\curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->soapprotocol);
            \Safe\curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
            \Safe\curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
            if (!empty($this->temppass)) {
                \Safe\curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
            }
            \Safe\curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
            if (! empty($envelope)) {
                \Safe\curl_setopt($oCurl, CURLOPT_POST, true);
                \Safe\curl_setopt($oCurl, CURLOPT_POSTFIELDS, $envelope);
                \Safe\curl_setopt($oCurl, CURLOPT_HTTPHEADER, $parameters);
            }
            $response = \Safe\curl_exec($oCurl);
            $this->soaperror = curl_error($oCurl);
            $ainfo = \Safe\curl_getinfo($oCurl);
            if (is_array($ainfo)) {
                $this->soapinfo = $ainfo;
            }
            $headsize = \Safe\curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
            $httpcode = \Safe\curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
            curl_close($oCurl);
            $this->responseHead = trim(\Safe\substr($response, 0, $headsize));
            $this->responseBody = trim(\Safe\substr($response, $headsize));
            $this->saveDebugFiles(
                $operation,
                $this->requestHead . "\n" . $this->requestBody,
                $this->responseHead . "\n" . $this->responseBody
            );
        } catch (\Exception $e) {
            throw SoapException::unableToLoadCurl($e->getMessage());
        }
        if ($this->soaperror != '') {
            throw SoapException::soapFault($this->soaperror . " [$url]");
        }
        if ($httpcode != 200) {
            throw SoapException::soapFault(
                " [$url] HTTP Error code: $httpcode - "
                . $this->getFaultString($this->responseBody)
            );
        }
        return $this->responseBody;
    }
    
    /**
     * Recover WSDL form given URL
     * @param string $url
     * @return string
     */
    public function wsdl($url)
    {
        $response = '';
        $this->saveTemporarilyKeyFiles();
        $url .= '?singleWsdl';
        $oCurl = \Safe\curl_init();
        \Safe\curl_setopt($oCurl, CURLOPT_URL, $url);
        \Safe\curl_setopt($oCurl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        \Safe\curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, $this->soaptimeout);
        \Safe\curl_setopt($oCurl, CURLOPT_TIMEOUT, $this->soaptimeout + 20);
        \Safe\curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, 0);
        \Safe\curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, 0);
        \Safe\curl_setopt($oCurl, CURLOPT_SSLVERSION, $this->soapprotocol);
        \Safe\curl_setopt($oCurl, CURLOPT_SSLCERT, $this->tempdir . $this->certfile);
        \Safe\curl_setopt($oCurl, CURLOPT_SSLKEY, $this->tempdir . $this->prifile);
        if (!empty($this->temppass)) {
            \Safe\curl_setopt($oCurl, CURLOPT_KEYPASSWD, $this->temppass);
        }
        \Safe\curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, true);
        $response = \Safe\curl_exec($oCurl);
        $soaperror = curl_error($oCurl);
        $ainfo = \Safe\curl_getinfo($oCurl);
        $headsize = \Safe\curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
        $httpcode = \Safe\curl_getinfo($oCurl, CURLINFO_HTTP_CODE);
        curl_close($oCurl);
        if ($httpcode != 200) {
            return '';
        }
        return $response;
    }
    
    /**
     * Set proxy into cURL parameters
     * @param resource $oCurl
     */
    private function setCurlProxy(&$oCurl)
    {
        if ($this->proxyIP != '') {
            \Safe\curl_setopt($oCurl, CURLOPT_HTTPPROXYTUNNEL, 1);
            \Safe\curl_setopt($oCurl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            \Safe\curl_setopt($oCurl, CURLOPT_PROXY, $this->proxyIP.':'.$this->proxyPort);
            if ($this->proxyUser != '') {
                \Safe\curl_setopt($oCurl, CURLOPT_PROXYUSERPWD, $this->proxyUser.':'.$this->proxyPass);
                \Safe\curl_setopt($oCurl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            }
        }
    }
    
    /**
     * Extract faultstring form response if exists
     * @param string $body
     * @return string
     */
    private function getFaultString($body)
    {
        if (empty($body)) {
            return '';
        }
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($body);
        $faultstring = '';
        $nodefault = !empty($dom->getElementsByTagName('faultstring')->item(0))
            ? $dom->getElementsByTagName('faultstring')->item(0)
            : '';
        if (!empty($nodefault)) {
            $faultstring = $nodefault->nodeValue;
        }
        return htmlentities($faultstring, ENT_QUOTES, 'UTF-8');
    }
}
