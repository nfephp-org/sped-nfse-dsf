<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeDSF\Tools;
use NFePHP\NFSeDSF\Common\Soap\SoapFake;
use NFePHP\NFSeDSF\Common\FakePretty;

try {
    
    $config = [
        'cnpj' => '99999999000191',
        'im' => '1733160024',
        'cmun' => '3170206', //ira determinar as urls e outros dados
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2 //1-producao, 2-homologacao
    ];

    $configJson = json_encode($config);

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);

    // remova as linhas abaixo para usar em modo real
    $soap = new SoapFake();
    $soap->disableCertValidation(true);
    // fim
    
    $tools = new Tools($configJson, $cert);
    
    // remova a linha abaixo para usar em modo real
    $tools->loadSoapClass($soap);

    $numero = '111';
    $motivo = 'Teste de cancelamento';
    $codigoverificacao = 'asdfg12345mnbvcx';
    
    $response = $tools->cancelar($numero, $motivo, $codigoverificacao);

    // remova a linha abaixo ara usar em modo real
    echo FakePretty::prettyPrint($response, '');
    
    // descomente as linhas abaixo para usar em mode real
    //header("Content-type: text/xml");
    //echo $response;
 
} catch (\Exception $e) {
    echo $e->getMessage();
}

