<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\NFSeDSF\Tools;
use NFePHP\NFSeDSF\Rps;
use NFePHP\NFSeDSF\Common\Soap\SoapFake;
use NFePHP\NFSeDSF\Common\FakePretty;

try {

    $config = [
        'cnpj'  => '04907793000188',
        'im'    => '15178100',
        'cmun'  => '3170206', //ira determinar as urls e outros dados
        'razao' => 'Empresa Test Ltda',
        'tpamb' => 2 //1-producao, 2-homologacao
    ];

    $configJson = json_encode($config);

    $content = file_get_contents('expired_certificate.pfx');
    $password = 'associacao';
    $cert = Certificate::readPfx($content, $password);

    $soap = new SoapFake();
    $soap->disableCertValidation(true);

    $tools = new Tools($configJson, $cert);
    $tools->loadSoapClass($soap);

    $arps = [];
    $std = new \stdClass();
    $std->inscricaomunicipalprestador = '15178100';
    $std->razaosocialprestador = 'Empresa Test Ltda';
    $std->tiporps = 'RPS';
    $std->serierps = 'NF';
    $std->numerorps = 84;
    $std->dataemissaorps = '2019-01-24T15:30:00';
    $std->situacaorps = 'N';
//$std->serierpssubstituido = '';
//$std->numerorpssubstituido = '0';
//$std->numeronfsesubstituida = '0';
//$std->dataemissaonfsesubstituida = '1900-01-01';
    $std->serieprestacao = '99';
    $std->inscricaomunicipaltomador = '0000000';
    $std->cpfcnpjtomador = '17606357000110';
    $std->razaosocialtomador = 'VIRTUX TECNOLOGIA';
    $std->tipologradourotomador = 'Rua';
    $std->logradourotomador = 'SETE DE SETEMBRO';
    $std->numeroenderecotomador = '335';
    $std->complementoenderecotomador = '';
    $std->tipobairrotomador = 'Bairro';
    $std->bairrotomador = 'Centro';
    $std->cidadetomador = '5403';
    $std->cidadetomadordescricao = 'UBERLANDIA';
    $std->ceptomador = '64001210';
    $std->emailtomador = 'res@bol.com.br';
    $std->codigoatividade = '381220001';
    $std->aliquotaatividade = 0.0000;
    $std->tiporecolhimento = 'A';
    $std->municipioprestacao = '5403';
    $std->municipioprestacaodescricao = 'UBERLANDIA';
    $std->operacao = 'A';
    $std->tributacao = 'T';
    $std->valorpis = 0.00;
    $std->valorcofins = 0.00;
    $std->valorinss = 0.00;
    $std->valorir = 0.00;
    $std->valorcsll = 0.00;
    $std->aliquotapis = 0.0000;
    $std->aliquotacofins = 0.0000;
    $std->aliquotainss = 0.0000;
    $std->aliquotair = 0.0000;
    $std->aliquotacsll = 0.0000;
    $std->descricaorps = "MES/ANO DE REFERENCIA DA PRESTACAO DE SERVICO:12-2009 .VENCIMENTO =08/01/2010 VALOR LIQUIDO A PAGAR  R$3669,38SERVICO DE PORTARIA -RPS enviado em teste";
//$std->dddprestador = '011';
//$std->telefoneprestador = '80804040';
//$std->dddtomador = '011';
//$std->telefonetomador = '20203030';
//$std->motcancelamento = '';
//$std->cpfcnpjintermediario = '';

/*
    $std->deducoes[0] = new stdClass();
    $std->deducoes[0]->deducaopor = 'Valor';
    $std->deducoes[0]->tipodeducao = 'Despesas com Materiais';
    $std->deducoes[0]->cpfcnpjreferencia = '00000000000';
    $std->deducoes[0]->numeronfreferencia = '10';
    $std->deducoes[0]->valortotalreferencia = 0.00;
    $std->deducoes[0]->percentualdeduzir = 0.00;
    $std->deducoes[0]->valordeduzir = 5.00;
    $std->deducoes[0]->tributavel = 'S';
*/
    $std->itens[0] = new stdClass();
    $std->itens[0]->discriminacaoservico = "Descricao do Servico ...";
    $std->itens[0]->quantidade = 1;
    $std->itens[0]->valorunitario = 100.0000;
    $std->itens[0]->valortotal = 100.00;
    $std->itens[0]->tributavel = 'S';

    $rps = new Rps($std);
    
    $arps[] = $rps;
    $lote = '123456';

    $response = $tools->enviar($arps, $lote);

    header('Content-Type: application/xml; charset=utf-8');
    echo $response;
    
    //echo FakePretty::prettyPrint($response, '');
} catch (\Exception $e) {
    echo $e->getMessage();
}