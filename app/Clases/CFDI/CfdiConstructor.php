<?php
namespace App\Clases\CFDI;

// use DOMdoument;


class CfdiConstructor
{
    private $rfcEmisor='';

    private $nombreEmisor='';

    private $xml = null;

    private $cuentaPredial = null;

    private $atributos = [];

    private $concepto = [];

    private $conceptos = [];

    function __construct() {

        $this->xml = new \DOMdocument("1.0","UTF-8");

        $this->cuentaPredial = '4011800201';

        $this->atributos = [
            'xmlns:cfdi'=>"http://www.sat.gob.mx/cfd/3",
            'xmlns:xsi'=>"http://www.w3.org/2001/XMLSchema-instance",
            'xsi:schemaLocation'=>"http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd",
            'Serie'=>'A',
            'Folio'=>111222,
            'Fecha'=>date("Y-m-d")."T".date("H:i:s"),//"2018-02-09T15:54:23",
            'LugarExpedicion'=>"77600",
            'TipoDeComprobante'=>"I",

            'SubTotal'=>"2475.00",
            'Total'=> "2871.00",

            'Moneda'=>"MXN",
            'TipoCambio'=>"1",

            'FormaPago'=>"01",
            'MetodoPago'=>"PUE",
            'Version'=>"3.3",

            'TasaIVA' => "0.160000",
        ];

        $this->concepto = [
//            'NoIdentificacion' => "S436061",
            'ClaveProdServ'=> "90121501",

            'ClaveUnidad'  => "E48",
            'Unidad'       => "UNIDAD DE SERVICIO",
            'Cantidad'     => "1",
            'Descripcion'      => "INGRESOS VENTA TOURS SHOREX P.M.",

            'ValorUnitario'    => "2475.00",
            'Importe'          => "2475.000000",
            'Base'          => "2475.00", //

            'IVA'     => "396.00",
        ];

        $this->receptor = [
            'Rfc'=>'XAXX010101000',
            'Nombre'=>'COMPROBANTE GLOBAL DE OPERACIONES CON PUBLICO EN GENERAL',
            'UsoCFDI'=>"G03" //Gastos en general
        ];


        return $this->modoPruebas();
    }

    public function setAtributoReceptor($key,$value){
        if(isset($this->receptor[$key])){
            $this->receptor[$key] = $value;
        }else{
            throw new \Exception("No existe el indice $key en receptor");
        }

        return $this;
    }

    public function setAtributoFactura($key,$value){

        if(isset($this->atributos[$key])){
            $this->atributos[$key] = $value;
        }else{
            throw new \Exception("No existe el indice $key en atributos");
        }

        return $this;
    }

    public function setAtributoConcepto($key,$value){

        if(isset($this->concepto[$key])){
            $this->concepto[$key] = $value;
        }else{
            throw new \Exception("No existe el indice $key en concepto");
        }

        return $this;
    }

    public function addConcepto(array $concepto){
        $this->conceptos[] = $concepto;
    }

    public function modoProductivo(){
        $this->rfcEmisor = 'CCT880315856';
        $this->nombreEmisor = 'COZUMEL CRUISE TERMINAL';
        return $this;
    }

    public function modoPruebas(){
        $this->rfcEmisor = 'AAA010101AAA';
        $this->nombreEmisor = 'DEKONOX';
        return $this;
    }

    public function fnumero($numero){
        return number_format($numero,2,'.','');
    }

    private function limpiatexto($cadena){
        $cadena = str_replace("Ñ","N",strtoupper(trim($cadena)));
        return preg_replace("/[^A-Za-z0-9?![:space:]]/"," ",trim($cadena));
    }

    public function generarXML($datos=array()){

        $xml = &$this->xml;

        $atributos = $this->atributos;


        $emisor = array(
            'Rfc'=>$this->rfcEmisor,
            'Nombre'=>$this->nombreEmisor,
            'RegimenFiscal'=>"601"
        );

        $receptor = $this->receptor;

        $concepto = $this->concepto;

        // Crea el nodo Xml Raiz
        $xml_root = $xml->createElement("cfdi:Comprobante");
        $xml->appendChild($xml_root);
        foreach(array_keys($atributos) as $elem){
            if(! in_array($elem,['TasaIVA','IVA'])){
                $xml_root->setAttribute($elem,$atributos[$elem]);
            }
        }

        // Crea el nodo del emisor
        $xml_emisor = $xml->createElement("cfdi:Emisor");
        $xml_root->appendChild($xml_emisor);
        foreach(array_keys($emisor) as $elem){
            $xml_emisor->setAttribute($elem,$emisor[$elem]);
        }

        // Crea el nodo del receptor
        $xml_receptor = $xml->createElement("cfdi:Receptor");
        $xml_root->appendChild($xml_receptor);
        foreach(array_keys($receptor) as $elem){

            $xml_receptor->setAttribute($elem,$receptor[$elem]);


        }

        ///////////////////////////////////////////////////////////
        //// C O N C E P T O S
        ///////////////////////////////////////////////////////////


        // Crea el nodo de conceptos
        $xml_conceptos = $xml->createElement("cfdi:Conceptos");
        $xml_root->appendChild($xml_conceptos);

        foreach($this->conceptos as $concepto):
            // Crea el nodo del concepto dentro de los conceptos
            $xml_concepto = $xml->createElement("cfdi:Concepto");
            $xml_conceptos->appendChild($xml_concepto);

            foreach(array_keys($concepto) as $key){
                if(! in_array($key,['TasaIVA','IVA','Base'])){
                    $xml_concepto->setAttribute($key,$concepto[$key]);
                }
            }

        //crea el nodo de impuestos
        $xml_con_impuestos  = $xml->createElement("cfdi:Impuestos");
        $xml_con_traslados  = $xml->createElement("cfdi:Traslados");
        $xml_con_traslado   = $xml->createElement("cfdi:Traslado");

        $xml_con_traslado->setAttribute("Base",$concepto['Base']);
//        $xml_con_traslado->setAttribute("Base",$conceptos->sum('Base'));

        $xml_con_traslado->setAttribute("Impuesto","002");
        $xml_con_traslado->setAttribute("TipoFactor","Tasa");
        $xml_con_traslado->setAttribute("TasaOCuota",$atributos['TasaIVA']);
        $xml_con_traslado->setAttribute("Importe",$concepto['IVA']);
//        $xml_con_traslado->setAttribute("Importe",$conceptos->sum('IVA'));


        $xml_con_traslados->appendChild($xml_con_traslado);
        $xml_con_impuestos->appendChild($xml_con_traslados);
        $xml_concepto->appendChild($xml_con_impuestos);

        endforeach;

        ///////////////////////////////////////////////////////////
        //// END OF CONCEPTOS
        ///////////////////////////////////////////////////////////

        $conceptos = collect($this->conceptos);



        //impuestos totales
        $xml_total_impuestos = $xml->createElement("cfdi:Impuestos");
        $xml_total_impuestos->setAttribute('TotalImpuestosTrasladados',$conceptos->sum('IVA'));

        $xml_total_traslados = $xml->createElement("cfdi:Traslados");
        $xml_traslado = $xml->createElement("cfdi:Traslado");
        $xml_traslado->setAttribute('Impuesto','002');
        $xml_traslado->setAttribute('TipoFactor','Tasa');

        $xml_traslado->setAttribute('TasaOCuota',$atributos['TasaIVA']);
        $xml_traslado->setAttribute('Importe',$conceptos->sum('IVA') );

        $xml_total_traslados->appendChild($xml_traslado);
        $xml_total_impuestos->appendChild($xml_total_traslados);
        $xml_root->appendChild($xml_total_impuestos);

        // General el XML
        return $xml->saveXML();
    }



}