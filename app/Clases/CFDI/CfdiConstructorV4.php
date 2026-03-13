<?php

namespace App\Clases\CFDI;

// use DOMdoument;

class CfdiConstructorV4
{
    private $rfcEmisor = '';

    private $nombreEmisor = '';

    private $xml = null;

    private $cuentaPredial = null;

    private $atributos = [];

    private $concepto = [];

    private $conceptos = [];

    private $cfdi_relacionado = [];

    private $tipo_relacion_facturas = '';
    private $cfdis_relacionados = [];

    function __construct($complemento = false)
    {

        $this->xml = new \DOMdocument("1.0", "UTF-8");

        $this->cuentaPredial = '4011800201';

        $this->atributos = [
            'xmlns:cfdi' => "http://www.sat.gob.mx/cfd/4",
            'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
            'xsi:schemaLocation' => "http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd",
            'Serie' => 'CFDI4.0',
            'Folio' => 111222,
            'Fecha' => date("Y-m-d") . "T" . date("H:i:s"),//"2018-02-09T15:54:23",
            'LugarExpedicion' => "77600",
            'TipoDeComprobante' => "I",

            'SubTotal' => "2475.00",
            'Total' => "2871.00",

            'Moneda' => "USD",
            'TipoCambio' => "1",

            'FormaPago' => "01",
            'MetodoPago' => "PUE",
            'Version' => "4.0",

            'TasaIVA' => "0.160000",

            'Exportacion' => '01'

//            'Exportacion' => "01",
//            "Certificado" => "",
//            "NoCertificado" => "",
//            "Sello" => ""
        ];

        $this->concepto = [
            'NoIdentificacion' => "S436061",
            'ClaveProdServ' => "90121501",

            'ObjetoImp' => '02',

            'ClaveUnidad' => "E48",
            'Unidad' => "UNIDAD DE SERVICIO",
            'Cantidad' => "1",
            'Descripcion' => "INGRESOS VENTA TOURS SHOREX P.M.",

            'ValorUnitario' => "2475.00",
            'Importe' => "2475.000000",
            'Base' => "2475.00", //

            'IVA' => "396.00",
        ];

        $this->cfdi_relacionado = [
            'UUID' => ''
        ];

        $this->receptor = [
            'Rfc' => 'XAXX010101000',
            'Nombre' => 'PUBLICO EN GENERAL',
            'UsoCFDI' => "S01", //Gastos en general,
            'RegimenFiscalReceptor' => "616",
            'DomicilioFiscalReceptor' => '77600'/*'01000'*/
        ];

        $this->informacionGlobal = [
            'Periodicidad' => '',
            'Meses' => '',
            'Año' => ''
        ];


        $this->modoPruebas();
    }

    public function setAtributoReceptor($key, $value)
    {
        if (isset($this->receptor[$key])) {
            $this->receptor[$key] = $value;
        } else {
            throw new \Exception("No existe el indice $key en receptor");
        }

        return $this;
    }

    public function setAtributoInformacionGlobal($key, $value)
    {
        if (isset($this->informacionGlobal[$key])) {
            $this->informacionGlobal[$key] = $value;
        } else {
            throw new \Exception("No existe el indice $key en informacionGlobal");
        }

        return $this;
    }

    public function setAtributoFactura($key, $value)
    {

        if (isset($this->atributos[$key])) {
            $this->atributos[$key] = $value;
        } else {
            throw new \Exception("No existe el indice $key en atributos");
        }

        return $this;
    }

    public function setAtributoConcepto($key, $value)
    {

        if (isset($this->concepto[$key])) {
            $this->concepto[$key] = $value;
        } else {
            throw new \Exception("No existe el indice $key en concepto");
        }

        return $this;
    }

    public function removeAtributoConcepto($key)
    {

        if (isset($this->concepto[$key])) {
            array_forget($this->concepto, $key);
        } else {
            throw new \Exception("No existe el indice $key en concepto");
        }

        return $this;
    }

    public function addConceptoToConceptos()
    {
        $this->conceptos[] = $this->concepto;
        return $this;
    }

    public function addCfdiRelacionadoToCfdiRelacionados()
    {
        $this->cfdis_relacionados[] = $this->cfdi_relacionado;
        return $this;
    }

    public function setAtributoCfdiRelacionado($key, $value)
    {

        if (isset($this->cfdi_relacionado[$key])) {
            $this->cfdi_relacionado[$key] = $value;
        } else {
            throw new \Exception("No existe el indice $key en cfdi relacionado");
        }

        return $this;
    }

    public function setTipoRelacionFacuras($value)
    {
        $this->tipo_relacion_facturas = $value;
        return $this;
    }

    public function modoProductivo()
    {
        $this->rfcEmisor = settings()->get('cfdi_rfc_emisor');//'CCT880315856';
        $this->nombreEmisor = settings()->get('cfdi_nombre_emisor'); //'COZUMEL CRUISE TERMINAL';
        return $this;
    }

    public function modoPruebas()
    {
        $this->rfcEmisor = 'IIA040805DZ4';
        $this->nombreEmisor = 'INDISTRIA ILUMINADORA DE ALMACENES';
        return $this;
    }

    public function fnumero($numero)
    {
        return number_format($numero, 2, '.', '');
    }

    private function limpiatexto($cadena)
    {
        $cadena = str_replace("Ñ", "N", strtoupper(trim($cadena)));
        return preg_replace("/[^A-Za-z0-9?![:space:]]/", " ", trim($cadena));
    }

    public function generarXML($datos = array())
    {

        $xml = &$this->xml;

        $atributos = $this->atributos;


        $emisor = array(
            'Rfc' => $this->rfcEmisor,
            'Nombre' => $this->nombreEmisor,
            'RegimenFiscal' => "601"
        );

        $receptor = $this->receptor;
        $informacionGlobal = $this->informacionGlobal;

        $conceptos = $this->conceptos;

        // Crea el nodo Xml Raiz
        $xml_root = $xml->createElement("cfdi:Comprobante");
        $xml->appendChild($xml_root);
        foreach (array_keys($atributos) as $elem) {
            if (!in_array($elem, ['TasaIVA', 'IVA'])) {
                $xml_root->setAttribute($elem, $atributos[$elem]);
            }
        }

        if($receptor['Rfc'] === 'XAXX010101000'){
            // Crea el nodo del InformacionGlobal
            $xml_informacionGlobal = $xml->createElement("cfdi:InformacionGlobal");
            $xml_root->appendChild($xml_informacionGlobal);
            foreach (array_keys($informacionGlobal) as $elem) {

                $xml_informacionGlobal->setAttribute($elem, $informacionGlobal[$elem]);

            }

        }

        // Crea el nodo del emisor
        $xml_emisor = $xml->createElement("cfdi:Emisor");
        $xml_root->appendChild($xml_emisor);
        foreach (array_keys($emisor) as $elem) {
            $xml_emisor->setAttribute($elem, $emisor[$elem]);
        }

        // Crea el nodo del receptor
        $xml_receptor = $xml->createElement("cfdi:Receptor");
        $xml_root->appendChild($xml_receptor);
        foreach (array_keys($receptor) as $elem) {

            $xml_receptor->setAttribute($elem, $receptor[$elem]);


        }

        ///////////////////////////////////////////////////////////
        //// C O N C E P T O S
        ///////////////////////////////////////////////////////////


        // Crea el nodo de conceptos
        $iva = 0;
        $xml_conceptos = $xml->createElement("cfdi:Conceptos");
        foreach ($conceptos as $concepto) {
            // Crea el nodo del concepto dentro de los conceptos
            $xml_concepto = $xml->createElement("cfdi:Concepto");
            foreach (array_keys($concepto) as $key) {
                if (!in_array($key, ['TasaIVA', 'IVA', 'Base'])) {
                    $xml_concepto->setAttribute($key, $concepto[$key]);
                }
            }
            if(isset($concepto['ObjetoImp']) && $concepto['ObjetoImp'] !== "01"){
                //crea el nodo de impuestos
                $xml_con_impuestos = $xml->createElement("cfdi:Impuestos");
                $xml_con_traslados = $xml->createElement("cfdi:Traslados");
                $xml_con_traslado = $xml->createElement("cfdi:Traslado");
                // var_dump($elem);
                $xml_con_traslado->setAttribute("Base", $concepto['Base']);

                $xml_con_traslado->setAttribute("Impuesto", "002");
                $xml_con_traslado->setAttribute("TipoFactor", "Tasa");
                $xml_con_traslado->setAttribute("TasaOCuota", $atributos['TasaIVA']);
                $xml_con_traslado->setAttribute("Importe", $this->fnumero($concepto['IVA']));
                $iva += $concepto['IVA'];

                $xml_con_traslados->appendChild($xml_con_traslado);
                $xml_con_impuestos->appendChild($xml_con_traslados);
                $xml_concepto->appendChild($xml_con_impuestos);
            }
            $xml_conceptos->appendChild($xml_concepto);
        }

        $xml_root->appendChild($xml_conceptos);

        ///////////////////////////////////////////////////////////
        //// END OF CONCEPTOS
        ///////////////////////////////////////////////////////////

        //impuestos totales
        if($iva > 0){
            $xml_total_impuestos = $xml->createElement("cfdi:Impuestos");
            $xml_total_impuestos->setAttribute('TotalImpuestosTrasladados', $this->fnumero($iva));
//        $xml_total_impuestos->setAttribute('TotalImpuestosRetenidos', "0.00");


            $base = 0;
            foreach ($conceptos as $concepto) {
                if(isset($concepto['ObjetoImp']) && $concepto['ObjetoImp'] !== "01") {
                    $base += $concepto['Base'];
                }
            }

            $xml_total_traslados = $xml->createElement("cfdi:Traslados");
            $xml_traslado = $xml->createElement("cfdi:Traslado");

            $xml_traslado->setAttribute('Impuesto', '002');
            $xml_traslado->setAttribute('TipoFactor', 'Tasa');
            $xml_traslado->setAttribute('TasaOCuota', $atributos['TasaIVA']);
            $xml_traslado->setAttribute('Importe', $this->fnumero($iva));
            $xml_traslado->setAttribute('Base', $base);
            $xml_total_traslados->appendChild($xml_traslado);

//        $xml_total_retenciones = $xml->createElement("cfdi:Retenciones");
//
//        $xml_retencion = $xml->createElement("cfdi:Retencion");
//        $xml_retencion->setAttribute('Importe', '0.00');
//        $xml_retencion->setAttribute('TipoFactor', 'Tasa');
//        $xml_retencion->setAttribute('TasaOCuota', "0.100000");
//        $xml_retencion->setAttribute('Impuesto', "001");
//        $xml_retencion->setAttribute('Base', "1");
//        $xml_total_retenciones->appendChild($xml_retencion);

            $xml_total_impuestos->appendChild($xml_total_traslados);
//        $xml_total_impuestos->appendChild($xml_total_retenciones);
            $xml_root->appendChild($xml_total_impuestos);
        }

        // General el XML
        return $xml->saveXML();
    }
}
