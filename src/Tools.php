<?php

namespace NFePHP\NFSeDSF;

/**
 * Class for comunications with NFSe webserver in Nacional Standard
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

use NFePHP\NFSeDSF\Common\Tools as BaseTools;
use NFePHP\NFSeDSF\RpsInterface;
use NFePHP\Common\Certificate;
use NFePHP\Common\Validator;
use stdClass;
use DateTime;

class Tools extends BaseTools
{
       
    protected $xsdpath;
    
    /**
     * Construtor
     * @param string $config
     * @param Certificate $cert
     */
    public function __construct($config, Certificate $cert)
    {
        parent::__construct($config, $cert);
        $path = \Safe\realpath(__DIR__ . '/../storage/schemes');
        $this->xsdpath = $path;
    }
    
    /**
     * Cancela Nota
     * @param string $numero
     * @param string $motivo
     * @param string $codigoverificacao
     * @return string
     */
    public function cancelar($numero, $motivo, $codigoverificacao): string
    {
        $operation = "cancelar";
        $lote = date('ymdHis');
        $content = "<ns1:ReqCancelamentoNFSe "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ReqCancelamentoNFSe.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<transacao>true</transacao>"
            . "<Versao>{$this->wsobj->version}</Versao></Cabecalho>"
            . "<Lote Id=\"lote:$lote\">"
            . "<Nota Id=\"nota:$numero\">"
            . "<InscricaoMunicipalPrestador>{$this->config->im}</InscricaoMunicipalPrestador>"
            . "<NumeroNota>$numero</NumeroNota>"
            . "<CodigoVerificacao>$codigoverificacao</CodigoVerificacao>"
            . "<MotivoCancelamento>$motivo</MotivoCancelamento>"
            . "</Nota>"
            . "</Lote>"
            . "</ns1:ReqCancelamentoNFSe>";
        
        Validator::isValid($content, $this->xsdpath."/ReqCancelamentoNFSe.xsd");
        return $this->send($content, $operation);
    }
    
    /**
     * Consulta notas pelos seus numeros ou pelos numeros de RPS
     * @param array $notas
     * @param array $rps
     * @return string
     */
    public function consultarNFSeRps(array $notas = [], array $rps = []): string
    {
        $operation = "consultarNFSeRps";
        $lote = date('ymdHis');
        $content = "<ns1:ReqConsultaNFSeRPS "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ReqConsultaNFSeRPS.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<transacao>true</transacao>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "</Cabecalho>"
            . "<Lote Id=\"lote:$lote\">";
        
        if (!empty($notas)) {
            $content .= "<NotaConsulta>";
            foreach ($notas as $nota) {
                $n = \Safe\json_decode(\Safe\json_encode($nota));
                $content .= "<Nota Id=\"nota:{$n->numero}\">"
                    . "<InscricaoMunicipalPrestador>{$this->config->im}</InscricaoMunicipalPrestador>"
                    . "<NumeroNota>{$n->numero}</NumeroNota>"
                    . "<CodigoVerificacao>{$n->codigo}</CodigoVerificacao>"
                    . "</Nota>";
            }
            $content .= "</NotaConsulta>";
        }
            
        if (!empty($rps)) {
            $content .= "<RPSConsulta>";
            foreach ($rps as $rp) {
                $n = \Safe\json_decode(\Safe\json_encode($rp));
                $content .= "<RPS Id=\"rps:119\">"
                    . "<InscricaoMunicipalPrestador>{$this->config->im}</InscricaoMunicipalPrestador>"
                    . "<NumeroRPS>{$n->numero}</NumeroRPS>"
                    . "<SeriePrestacao>{$n->serie}</SeriePrestacao>"
                    . "</RPS>";
            }
            $content .= "</RPSConsulta>";
        }
        $content .= "</Lote>"
            . "</ns1:ReqConsultaNFSeRPS>";
        
        Validator::isValid($content, $this->xsdpath."/ReqConsultaNFSeRPS.xsd");
        return $this->send($content, $operation);
    }
    
    /**
     * Consulta ultimo numero sequencial de RPS
     * @return string
     */
    public function consultarSequencialRps(): string
    {
        $operation = "consultarSequencialRps";
        $content = "<ns1:ConsultaSeqRps "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ConsultaSeqRps.xsd\">"
            . "<Cabecalho>"
            . "<CodCid>{$this->wsobj->siaf}</CodCid>"
            . "<IMPrestador>{$this->config->im}</IMPrestador>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "</Cabecalho>"
            . "</ns1:ConsultaSeqRps>";
        
        Validator::isValid($content, $this->xsdpath."/ConsultaSeqRps.xsd");
        return $this->send($content, $operation);
    }
    
    /**
     * Consulta Notas pelo numero do lote
     * @param string $lote
     * @return string
     */
    public function consultarLote($lote): string
    {
        $operation = "consultarLote";
        $content = "<ns1:ReqConsultaLote "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote  "
            . "http://localhost:8080/WsNFe2/xsd/ReqConsultaLote.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "<NumeroLote>$lote</NumeroLote>"
            . "</Cabecalho>"
            . "</ns1:ReqConsultaLote>";
        
        Validator::isValid($content, $this->xsdpath."/ReqConsultaLote.xsd");
        return $this->send($content, $operation);
    }
    
    /**
     * Consulta Notas no intervalo das datas
     * @param string $dtInicial
     * @param string $dtFinal
     * @return string
     */
    public function consultarNota($dtInicial, $dtFinal): string
    {
        $operation = "consultarNota";
        
        $content = "<ns1_ReqConsultaNotas "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ReqConsultaNotas.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<InscricaoMunicipalPrestador>{$this->config->im}</InscricaoMunicipalPrestador>"
            . "<dtInicio>$dtInicial</dtInicio>"
            . "<dtFim>$dtFinal</dtFim>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "</Cabecalho>"
            . "</ns1_ReqConsultaNotas>";
        
        Validator::isValid($content, $this->xsdpath."/ReqConsultaNotas.xsd");
        return $this->send($content, $operation);
    }
    
    /**
     * Envia RSP em modo assincrono
     * @param array $arps
     * @param string $lote
     * @return string
     */
    public function enviar(array $arps, $lote): string
    {
        $operation = "enviar";
        
        $std = new \stdClass();
        $std->dtInicial = '';
        $std->dtFinal = '';
        $std->qtdade = 0;
        $std->vTotServ = 0;
        $std->vTotDeduc = 0;
        
        
        $rpsxmls = $this->buildRpsXml($arps, $std);
        
        $content = "<ns1:ReqEnvioLoteRPS "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<RazaoSocialRemetente>{$this->config->razao}</RazaoSocialRemetente>"
            . "<transacao>true</transacao>"
            . "<dtInicio>{$std->dtInicial}</dtInicio>"
            . "<dtFim>{$std->dtFinal}</dtFim>"
            . "<QtdRPS>{$std->qtdade}</QtdRPS>"
            . "<ValorTotalServicos>{$std->vTotServ}</ValorTotalServicos>"
            . "<ValorTotalDeducoes>{$std->vTotDeduc}</ValorTotalDeducoes>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "<MetodoEnvio>WS</MetodoEnvio>"
            . "</Cabecalho>"
            . "<Lote Id=\"$lote\">";
        
        foreach ($rpsxmls as $xml) {
            $content .= $xml;
        }
        
        $content .= "</Lote>"
            . "</ns1:ReqEnvioLoteRPS>";
        
        $xmlsigned = $this->sign($content, 'Lote', 'Id');
        Validator::isValid($content, $this->xsdpath."/ReqEnvioLoteRPS.xsd");
        
        return $this->send($xmlsigned, $operation);
    }
    
    /**
     * Envia RPS em modo sincrono
     * @param array $arps
     * @param string $lote
     * @return string
     */
    public function enviarSincrono(array $arps, $lote): string
    {
        $operation = "enviarSincrono";
        
        $std = new \stdClass();
        $std->dtInicial = '';
        $std->dtFinal = '';
        $std->qtdade = 0;
        $std->vTotServ = 0;
        $std->vTotDeduc = 0;

        $rpsxmls = $this->buildRpsXml($arps, $std);
        
        $content = "<ns1:ReqEnvioLoteRPS "
            . "xmlns:ns1=\"http://localhost:8080/WsNFe2/lote\" "
            . "xmlns:tipos=\"http://localhost:8080/WsNFe2/tp\" "
            . "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" "
            . "xsi:schemaLocation=\"http://localhost:8080/WsNFe2/lote "
            . "http://localhost:8080/WsNFe2/xsd/ReqEnvioLoteRPS.xsd\">"
            . "<Cabecalho>"
            . "<CodCidade>{$this->wsobj->siaf}</CodCidade>"
            . "<CPFCNPJRemetente>{$this->config->cnpj}</CPFCNPJRemetente>"
            . "<RazaoSocialRemetente>{$this->config->razao}</RazaoSocialRemetente>"
            . "<transacao>true</transacao>"
            . "<dtInicio>{$std->dtInicial}</dtInicio>"
            . "<dtFim>{$std->dtFinal}</dtFim>"
            . "<QtdRPS>{$std->qtdade}</QtdRPS>"
            . "<ValorTotalServicos>" . number_format($std->vTotServ, 2, '.', '') . "</ValorTotalServicos>"
            . "<ValorTotalDeducoes>" . number_format($std->vTotDeduc, 2, '.', '') . "</ValorTotalDeducoes>"
            . "<Versao>{$this->wsobj->version}</Versao>"
            . "<MetodoEnvio>WS</MetodoEnvio>"
            . "</Cabecalho>"
            . "<Lote Id=\"lote:$lote\">";
        
        foreach ($rpsxmls as $xml) {
            $content .= $xml;
        }
        
        $content .= "</Lote>"
            . "</ns1:ReqEnvioLoteRPS>";
        
        
        $xmlsigned = $this->sign($content, 'Lote', 'Id');
        Validator::isValid($content, $this->xsdpath."/ReqEnvioLoteRPS.xsd");
        
        return $this->send($xmlsigned, $operation);
    }
    
    /**
     * Constroi os RPS em XML e retorna em um array
     * os valores e datas dos RPS são retornados em
     * um stdClass passado por referencia
     * @param array $arps
     * @param stdClass $std
     * @return array
     */
    protected function buildRpsXml(array $arps, \stdClass &$std)
    {
        $std->dtInicial = '';
        $std->dtFinal = '';
        $std->qtdade = 0;
        $std->vTotServ = 0;
        $std->vTotDeduc = 0;
        $std->qtdade = count($arps);
        $dtIni = null;
        $dtFim = null;
        $rpsxmls = [];
        foreach ($arps as $rps) {
            if (empty($dtIni)) {
                $dtIni = new Datetime($rps->std->dataemissaorps);
                $dtFim = new Datetime($rps->std->dataemissaorps);
            } else {
                $dtIni = $this->smaller($dtIni, new Datetime($rps->std->dataemissaorps));
                $dtFim = $this->bigger($dtFim, new Datetime($rps->std->dataemissaorps));
            }
            //somar valor total dos serviços declarados
            foreach ($rps->std->itens as $item) {
                $std->vTotServ += $item->valortotal;
            }
            //somar valor total das deduções declaradas
            if (!empty($rps->std->deducoes)) {
                foreach ($rps->std->deducoes as $deducao) {
                    $std->vTotDeduc += $deducao->valordeduzir;
                }
            }
            //complementar a estrutura do RPS
            //montar e inserir a assinatura <Assinatura>a0e978b6e499375d916ba865bd2dc8af83fd2b28</Assinatura>
            //carregar o array de XML dos RPS
            $rpsxmls[] = $rps->render($this->certificate);
        }
        $std->dtInicial = $dtIni->format('Y-m-d');
        $std->dtFinal = $dtFim->format('Y-m-d');
        return $rpsxmls;
    }
    
    /**
     * Verifica qual é a maior data entre as passadas como parâmetro
     * @param \Datetime $dt1
     * @param \DateTime $dt2
     * @return \Datetime
     */
    protected function bigger(\Datetime $dt1, \DateTime $dt2)
    {
        if ($dt1 > $dt2) {
            return $dt1;
        } else {
            return $dt2;
        }
    }
    
    /**
     * Verifica qual é a menor data entre as passadas como parâmetro
     * @param \Datetime $dt1
     * @param \DateTime $dt2
     * @return \Datetime
     */
    protected function smaller(\Datetime $dt1, \DateTime $dt2)
    {
        if ($dt1 < $dt2) {
            return $dt1;
        } else {
            return $dt2;
        }
    }
}
