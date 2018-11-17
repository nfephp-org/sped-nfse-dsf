<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use JsonSchema\Constraints\Constraint;
use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

$version = '1_00';

$jsonSchema = '{
    "title": "RPS",
    "type": "object",
    "properties": {
        "inscricaomunicipalprestador": {
            "required": true,
            "type": "string",
            "pattern": "^.{5,15}$"
        },
        "razaosocialprestador": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,120}$"
        },
        "tiporps": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,20}$"
        },
        "serierps": {
            "required": true,
            "type": "string",
            "pattern": "^.{2}$"
        },
        "numerorps": {
            "required": true,
            "type": "integer",
            "minimum": 1,
            "maximum": 999999999999
        },
        "dataemissaorps": {
            "required": true,
            "type": "string",
            "pattern": "^([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T(2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9])$"
        },
        "situacaorps": {
            "required": true,
            "type": "string",
            "pattern": "N|C"
        },
        "serierpssubstituido": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^.{2, 10}$"
        },
        "numerorpssubstituido": {
            "required": false,
            "type": ["integer","null"],
            "minimum": 1,
            "maximum": 999999999999
        },
        "numeronfsesubstituida": {
            "required": false,
            "type": ["integer","null"],
            "minimum": 1,
            "maximum": 9999999999
        },
        "dataemissaonfsesubstituida": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^([0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]))$"
        },
        "serieprestacao": {
            "required": true,
            "type": "string",
            "pattern": "^[0-9]{2}$"
        },
        "inscricaomunicipaltomador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^.{5,15}"
        },
        "cpfcnpjtomador": {
            "required": true,
            "type": "string",
            "pattern": "^[0-9]{11,14}$"
        },
        "razaosocialtomador": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,120}$"
        },
        "tipologradourotomador": {
            "required": true,
            "type": "string",
            "pattern": "^(Avenida|Rua|Rodovia|Ruela|Rio|Sítio|Sup Quadra|Travessa|Vale|Via|Viaduto|Viela|Vila|Vargem)$"
        },
        "logradourotomador": {
            "required": true,
            "type": "string",
            "pattern": "^.{1,50}$"
        },
        "numeroenderecotomador": {
            "required": true,
            "type": "string",
            "pattern": "^.{1,9}$"
        },
        "complementoenderecotomador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^.{0,30}$"
        },
        "tipobairrotomador": {
            "required": true,
            "type": "string",
            "pattern": "^(Bairro|Bosque|Chácara|Conjunto|Desmembramento|Distrito|Favela|Fazenda|Gleba|Horto|Jardim|Loteamento|Núcleo|Parque|Residencial|Sítio|Tropical|Vila|Zona)$"
        },
        "bairrotomador": {
            "required": true,
            "type": "string",
            "pattern": "^.{1,50}$"
        },
        "cidadetomador": {
            "required": true,
            "type": "string",
            "pattern": "^[0-9]{5,10}$"
        },
        "cidadetomadordescricao": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,50}$"
        },
        "ceptomador": {
            "required": true,
            "type": "string",
            "pattern": "^[0-9]{8}$"
        },
        "emailtomador": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,60}$"
        },
        "codigoatividade": {
            "required": true,
            "type": "string",
            "pattern": "^.{1,9}$"
        },
        "aliquotaatividade": {
            "required": true,
            "type": "number"
        },
        "tiporecolhimento": {
            "required": true,
            "type": "string",
            "pattern": "^(A|R)$"
        },
        "municipioprestacao": {
            "required": true,
            "type": "string",
            "pattern": "^[0-9]{5,10}$"
        },
        "municipioprestacaodescricao": {
            "required": true,
            "type": "string",
            "pattern": "^.{3,50}$"
        },
        "operacao": {
            "required": true,
            "type": "string",
            "pattern": "^(A|B|C|D|J)$"
        },
        "tributacao": {
            "required": true,
            "type": "string",
            "pattern": "^(C|E|F|K|N|T|G|H|M)$"
        },
        "valorpis": {
            "required": true,
            "type": "number"
        },
        "valorcofins": {
            "required": true,
            "type": "number"
        },
        "valorinss": {
            "required": true,
            "type": "number"
        },
        "valorir": {
            "required": true,
            "type": "number"
        },
        "valorcsll": {
            "required": true,
            "type": "number"
        },
        "aliquotapis": {
            "required": true,
            "type": "number"
        },
        "aliquotacofins": {
            "required": true,
            "type": "number"
        },
        "aliquotainss": {
            "required": true,
            "type": "number"
        },
        "aliquotair": {
            "required": true,
            "type": "number"
        },
        "aliquotacsll": {
            "required": true,
            "type": "number"
        },
        "descricaorps": {
            "required": true,
            "type": "string",
            "pattern": "^.{1,1500}$"
        },
        "dddprestador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^[0-9]{3}$"
        },
        "telefoneprestador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^[0-9]{8}$"
        },
        "dddtomador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^[0-9]{3}$"
        },
        "telefonetomador": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^[0-9]{8}$"
        },
        "motcancelamento": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^.{1,80}$"
        },
        "cpfcnpjintermediario": {
            "required": false,
            "type": ["string","null"],
            "pattern": "^[0-9]{11,14}$"
        },
        "deducoes": {
            "required": false,
            "type": ["array","null"],
            "minItems": 0,
            "maxItems": 200,
            "items": {
                "type": "object",
                "properties": {
                    "deducaopor": {
                        "required": true,
                        "type": "string",
                        "pattern": "^(Percentual|Valor)$"
                    },
                    "tipodeducao": {
                        "required": true,
                        "type": "string",
                        "pattern": "^.{1,255}$"
                    },
                    "cpfcnpjreferencia": {
                        "required": true,
                        "type": "string",
                        "pattern": "^[0-9]{11,14}$"
                    },
                    "numeronfreferencia": {
                        "required": true,
                        "type": "string",
                        "pattern": "^[0-9]{1,10}$"
                    },
                    "valortotalreferencia": {
                        "required": true,
                        "type": "number"
                    },
                    "percentualdeduzir": {
                        "required": true,
                        "type": "number"
                    },
                    "valordeduzir": {
                        "required": true,
                        "type": "number"
                    },
                    "tributavel": {
                        "required": true,
                        "type": "string",
                        "pattern": "^(S|N)$"
                    }
                }
            }    
        },
        "itens": {
            "required": true,
            "type": "array",
            "minItems": 1,
            "maxItems": 25,
            "items": {
                "type": "object",
                "properties": {
                    "discriminacaoservico": {
                        "required": true,
                        "type": "string",
                        "pattern": "^.{1,80}$"
                    },
                    "quantidade": {
                        "required": true,
                        "type": "number"
                    },
                    "valorunitario": {
                        "required": true,
                        "type": "number"
                    },
                    "valortotal": {
                        "required": true,
                        "type": "number"
                    },
                    "tributavel": {
                        "required": true,
                        "type": "string",
                        "pattern": "^(S|N)$"
                    }
                }
            }
        }
    }
}';


$std = new \stdClass();
$std->inscricaomunicipalprestador = '10517900';
$std->razaosocialprestador = 'EMPRESA MODELO';
$std->tiporps = 'RPS';
$std->serierps = 'NF';
$std->numerorps = 84;
$std->dataemissaorps = '2009-11-21T15:30:00';
$std->situacaorps = 'N';
//$std->serierpssubstituido = '';
//$std->numerorpssubstituido = '0';
//$std->numeronfsesubstituida = '0';
//$std->dataemissaonfsesubstituida = '1900-01-01';
$std->serieprestacao = '99';
$std->inscricaomunicipaltomador = '0000000';
$std->cpfcnpjtomador = '00000000191';
$std->razaosocialtomador = 'EMPRESA DE TESTES';
$std->tipologradourotomador = 'Rua';
$std->logradourotomador = 'SETE DE SETEMBRO';
$std->numeroenderecotomador = '335';
$std->complementoenderecotomador = '';
$std->tipobairrotomador = 'Bairro';
$std->bairrotomador = 'Centro';
$std->cidadetomador = '0001219';
$std->cidadetomadordescricao = 'TERESINA';
$std->ceptomador = '64001210';
$std->emailtomador = 'res@bol.com.br';
$std->codigoatividade = '412040000';
$std->aliquotaatividade = 5.00; 
$std->tiporecolhimento = 'A';
$std->municipioprestacao = '0001219';
$std->municipioprestacaodescricao = 'TERESINA';
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

$std->deducoes[0] = new stdClass();
$std->deducoes[0]->deducaopor = 'Valor';
$std->deducoes[0]->tipodeducao = 'Despesas com Materiais';
$std->deducoes[0]->cpfcnpjreferencia = '00000000000';
$std->deducoes[0]->numeronfreferencia = '10';
$std->deducoes[0]->valortotalreferencia = 0.00;
$std->deducoes[0]->percentualdeduzir = 0.00;
$std->deducoes[0]->valordeduzir = 5.00;
$std->deducoes[0]->tributavel = 'S';

$std->itens[0] = new stdClass();
$std->itens[0]->discriminacaoservico = "Descricao do Servico ...";
$std->itens[0]->quantidade = 1;
$std->itens[0]->valorunitario = 100.0000;
$std->itens[0]->valortotal = 100.00;
$std->itens[0]->tributavel = 'S';


// Schema must be decoded before it can be used for validation
$jsonSchemaObject = json_decode($jsonSchema);
if (empty($jsonSchemaObject)) {
    echo "<h2>Erro de digitação no schema ! Revise</h2>";
    echo "<pre>";
    print_r($jsonSchema);
    echo "</pre>";
    die();
}
// The SchemaStorage can resolve references, loading additional schemas from file as needed, etc.
$schemaStorage = new SchemaStorage();
// This does two things:
// 1) Mutates $jsonSchemaObject to normalize the references (to file://mySchema#/definitions/integerData, etc)
// 2) Tells $schemaStorage that references to file://mySchema... should be resolved by looking in $jsonSchemaObject
$schemaStorage->addSchema('file://mySchema', $jsonSchemaObject);
// Provide $schemaStorage to the Validator so that references can be resolved during validation
$jsonValidator = new Validator(new Factory($schemaStorage));
// Do validation (use isValid() and getErrors() to check the result)
$jsonValidator->validate(
    $std,
    $jsonSchemaObject,
    Constraint::CHECK_MODE_COERCE_TYPES  //tenta converter o dado no tipo indicado no schema
);

if ($jsonValidator->isValid()) {
    echo "The supplied JSON validates against the schema.<br/>";
} else {
    echo "Dados não validados. Violações:<br/>";
    foreach ($jsonValidator->getErrors() as $error) {
        echo sprintf("[%s] %s<br/>", $error['property'], $error['message']);
    }
    die;
}
//salva se sucesso
file_put_contents("../storage/jsonSchemes/rps.schema", $jsonSchema);