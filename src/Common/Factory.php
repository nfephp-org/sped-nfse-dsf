<?php

namespace NFePHP\NFSeDSF\Common;

/**
 * Class for RPS XML convertion
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
use stdClass;
use DOMNode;
use DOMElement;

class Factory
{

    /**
     * @var stdClass
     */
    protected $std;

    /**
     * @var Dom
     */
    protected $dom;

    /**
     * @var DOMNode
     */
    protected $rps;
   
    /**
     * Constructor
     * @param stdClass $std
     */
    public function __construct(stdClass $std)
    {
        $this->std = $std;
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
        $this->rps = $this->dom->createElement('RPS');
    }

    /**
     * Builder, converts sdtClass Rps in XML Rps
     * NOTE: without Prestador Tag
     * @return string RPS in XML string format
     */
    public function render(Certificate $cert = null): string
    {
        $att = $this->dom->createAttribute('Id');
        $att->value = 'rps:' . $this->std->numerorps;
        $this->rps->appendChild($att);
        
        $assinatura = $this->signstr($cert);
        
        $this->dom->addChild(
            $this->rps,
            "Assinatura",
            $assinatura,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "InscricaoMunicipalPrestador",
            $this->std->inscricaomunicipalprestador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "RazaoSocialPrestador",
            $this->std->razaosocialprestador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "TipoRPS",
            $this->std->tiporps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "SerieRPS",
            $this->std->serierps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "NumeroRPS",
            $this->std->numerorps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "DataEmissaoRPS",
            $this->std->dataemissaorps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "SituacaoRPS",
            $this->std->situacaorps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "SerieRPSSubstituido",
            isset($this->std->serierpssubstituido) ? $this->std->serierpssubstituido : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "NumeroRPSSubstituido",
            isset($this->std->numerorpssubstituido) ? $this->std->numerorpssubstituido : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "NumeroNFSeSubstituida",
            isset($this->std->numeronfsesubstituida) ? $this->std->numeronfsesubstituida : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "DataEmissaoNFSeSubstituida",
            isset($this->std->dataemissaonfsesubstituida) ? $this->std->dataemissaonfsesubstituida : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "SeriePrestacao",
            $this->std->serieprestacao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "InscricaoMunicipalTomador",
            isset($this->std->inscricaomunicipaltomador) ? $this->std->inscricaomunicipaltomador : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "CPFCNPJTomador",
            $this->std->cpfcnpjtomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "RazaoSocialTomador",
            $this->std->razaosocialtomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "TipoLogradouroTomador",
            $this->std->tipologradourotomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "LogradouroTomador",
            $this->std->logradourotomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "NumeroEnderecoTomador",
            $this->std->numeroenderecotomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ComplementoEnderecoTomador",
            isset($this->std->complementoenderecotomador) ? $this->std->complementoenderecotomador : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "TipoBairroTomador",
            $this->std->tipobairrotomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "BairroTomador",
            $this->std->bairrotomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "CidadeTomador",
            $this->std->cidadetomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "CidadeTomadorDescricao",
            $this->std->cidadetomadordescricao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "CEPTomador",
            $this->std->ceptomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "EmailTomador",
            $this->std->emailtomador,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "CodigoAtividade",
            $this->std->codigoatividade,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaAtividade",
            number_format($this->std->aliquotaatividade, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "TipoRecolhimento",
            $this->std->tiporecolhimento,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "MunicipioPrestacao",
            $this->std->municipioprestacao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "MunicipioPrestacaoDescricao",
            $this->std->municipioprestacaodescricao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "Operacao",
            $this->std->operacao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "Tributacao",
            $this->std->tributacao,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ValorPIS",
            number_format($this->std->valorpis, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ValorCOFINS",
            number_format($this->std->valorcofins, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ValorINSS",
            number_format($this->std->valorinss, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ValorIR",
            number_format($this->std->valorir, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "ValorCSLL",
            number_format($this->std->valorcsll, 2, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaPIS",
            number_format($this->std->aliquotapis, 4, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaCOFINS",
            number_format($this->std->aliquotacofins, 4, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaINSS",
            number_format($this->std->aliquotainss, 4, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaIR",
            number_format($this->std->aliquotair, 4, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "AliquotaCSLL",
            number_format($this->std->aliquotacsll, 4, '.', ''),
            true
        );
        $this->dom->addChild(
            $this->rps,
            "DescricaoRPS",
            $this->std->descricaorps,
            true
        );
        $this->dom->addChild(
            $this->rps,
            "DDDPrestador",
            isset($this->std->dddprestador) ? $this->std->dddprestador : '',
            true
        );
        $this->dom->addChild(
            $this->rps,
            "TelefonePrestador",
            isset($this->std->telefoneprestador) ? $this->std->telefoneprestador : '',
            true
        );
        $this->dom->addChild(
            $this->rps,
            "DDDTomador",
            isset($this->std->dddtomador) ? $this->std->dddtomador : '',
            true
        );
        $this->dom->addChild(
            $this->rps,
            "TelefoneTomador",
            isset($this->std->telefonetomador) ? $this->std->telefonetomador : '',
            true
        );
        $this->dom->addChild(
            $this->rps,
            "MotCancelamento",
            isset($this->std->motcancelamento) ? $this->std->motcancelamento : null,
            false
        );
        $this->dom->addChild(
            $this->rps,
            "CPFCNPJIntermediario",
            isset($this->std->cpfcnpjintermediario) ? $this->std->cpfcnpjintermediario : null,
            false
        );
        if (!empty($this->std->deducoes)) {
            $deduc = $this->dom->createElement('Deducoes');
            foreach ($this->std->deducoes as $ded) {
                $node = $this->dom->createElement('Deducao');
                $this->dom->addChild(
                    $node,
                    "DeducaoPor",
                    $ded->deducaopor,
                    true
                );
                $this->dom->addChild(
                    $node,
                    "TipoDeducao",
                    $ded->tipodeducao,
                    true
                );
                $this->dom->addChild(
                    $node,
                    "CPFCNPJReferencia",
                    $ded->cpfcnpjreferencia,
                    true
                );
                $this->dom->addChild(
                    $node,
                    "NumeroNFReferencia",
                    $ded->numeronfreferencia,
                    true
                );
                $this->dom->addChild(
                    $node,
                    "ValorTotalReferencia",
                    number_format($ded->valortotalreferencia, 2, '.', ''),
                    true
                );
                $this->dom->addChild(
                    $node,
                    "PercentualDeduzir",
                    number_format($ded->percentualdeduzir, 2, '.', ''),
                    true
                );
                $this->dom->addChild(
                    $node,
                    "ValorDeduzir",
                    number_format($ded->valordeduzir, 2, '.', ''),
                    true
                );
                $deduc->appendChild($node);
            }
            $this->rps->appendChild($deduc);
        }
        $itensnode = $this->dom->createElement('Itens');
        foreach ($this->std->itens as $item) {
            $node = $this->dom->createElement('Item');
            $this->dom->addChild(
                $node,
                "DiscriminacaoServico",
                $item->discriminacaoservico,
                true
            );
            $this->dom->addChild(
                $node,
                "Quantidade",
                number_format($item->quantidade, 2, '.', ''),
                true
            );
            $this->dom->addChild(
                $node,
                "ValorUnitario",
                number_format($item->valorunitario, 4, '.', ''),
                true
            );
            $this->dom->addChild(
                $node,
                "ValorTotal",
                number_format($item->valortotal, 2, '.', ''),
                true
            );
            $this->dom->addChild(
                $node,
                "Tributavel",
                $item->tributavel,
                true
            );
            $itensnode->appendChild($node);
        }
        $this->rps->appendChild($itensnode);

        $this->dom->appendChild($this->rps);
        return $this->dom->saveXML($this->rps);
    }
    
    /**
     * Cria a assinatura do RPS
     * @param Rps $rps
     * @return string
     */
    protected function signstr(Certificate $certificate = null): string
    {
        $signature = '';
        $content = str_pad($this->std->inscricaomunicipalprestador, 11, '0', STR_PAD_LEFT);
        $content .= str_pad($this->std->serierps, 5, ' ', STR_PAD_RIGHT);
        $content .= str_pad($this->std->numerorps, 12, '0', STR_PAD_LEFT);
        
        $dt = new \DateTime($this->std->dataemissaorps);
        $content .= $dt->format('Ymd');
        $content .= str_pad($this->std->tributacao, 2, ' ', STR_PAD_RIGHT);
        $content .= $this->std->situacaorps;
        $content .= ($this->std->tiporecolhimento == 'A') ? 'N' : 'S';
        
        $valores = $this->calcValor();
        $content .= str_pad(round($valores->valorFinal * 100, 0), 15, '0', STR_PAD_LEFT);
        $content .= str_pad(round($valores->valorDeducao * 100, 0), 15, '0', STR_PAD_LEFT);
        $content .= str_pad($this->std->codigoatividade, 10, '0', STR_PAD_LEFT);
        $content .= str_pad($this->std->cpfcnpjtomador, 14, '0', STR_PAD_LEFT);
        if (isset($certificate)) {
            $signature = base64_encode($certificate->sign($content, OPENSSL_ALGO_SHA1));
        }
        return $signature;
    }

    protected function calcValor(): stdClass
    {
        $std = new stdClass();
        $std->valorFinal = 0;
        $std->valorItens = 0;
        $std->valorDeducao = 0;
        
        foreach ($this->std->itens as $item) {
            $std->valorItens += $item->valortotal;
        }
        if (!empty($this->std->deducoes)) {
            foreach ($this->std->deducoes as $deducao) {
                $std->valorDeducao += $deducao->valordeduzir;
            }
        }
        $std->valorFinal = $std->valorItens - $std->valorDeducao;
        return $std;
    }
}
