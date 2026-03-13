<?php

namespace App\Reports;

use App\DisennoGafetePaquete;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\SolicitudGafete;
use App\Reports\BaseReport;

class GafeteBaseReport extends BaseReport
{

    private $gafete = null;

    private $view = "gafetes.base";

    private $qr_text = "";

    private $tag_image = null;

    private $annio_impresion = null;

    private $es_vertical = true;


    public function setSolicitud(SolicitudGafete $gafete)
    {

        $this->gafete = $gafete;
//        $carbon_solicitud = Carbon::createFromFormat('Y-m-d',$gafete->sgft_fecha);
//
//        if($carbon_solicitud->format('Y') == '2022'){
//
//        }
        $this->tag_image = $this->buildTagImage();
        $this->annio_impresion = settings()->get('anio_impresion');
        if ((string)$this->annio_impresion == "2022") {
            $this->view = "gafetes.2022.base";
        } else {
            $paquete = DisennoGafetePaquete::PaqueteSeleccionado();
            $this->es_vertical = stripos(strtolower($paquete->dgp_nombre), 'vertical') !== false;
            if ($this->es_vertical)
                if (in_array($this->annio_impresion, ["2025", "2026"]))
                    $this->view = "gafetes.dinamico.base-$this->annio_impresion";
                else
                    $this->view = "gafetes.dinamico.base";
            else
                $this->view = "gafetes.dinamico.base_horizontal";
        }
        $this->qr_text = "GA_" . $this->gafete->sgft_id;


    }

    private function buildTagImage()
    {
        // Crear una imagen
        $font_size = 100;
        $max_chars = 22;
        $char_width = $font_size * .53;
        $char_height = $font_size * .85;
        $texto = strtoupper($this->gafete->Local->lcal_nombre_comercial);
        $texto .= ' ';
//        $texto = 'AGENCIA CONSIGNATARIASSS';
        $texto = substr($texto, 0, $max_chars);
        $ancho = ($char_width * strlen($texto)) + 10;
        $alto = $char_height + 10;

        //$font='arial.ttf';
        $font = 'inconsolata-condensed-bold.ttf';

//        $img = imagecreatetruecolor(800, 180);
        $img = imagecreatetruecolor($ancho, $alto);
        imagesavealpha($img, true);

        // Fondo transparente y texto blanco
        $trans_colour = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $trans_colour);
        $negro = imagecolorallocate($img, 0, 0, 0);
        $blanco = imagecolorallocate($img, 255, 255, 255);

        // Escribir la cadena en la parte superior izquierda
        $fuente = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR .
            'bg_gafetes' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . $font);
        imagettftext($img, $font_size, 0, 5, $alto - 5, -$blanco, $fuente, $texto);

        //rotamos la imagen
        $rotation = imagerotate($img, 90, $trans_colour);
        imagealphablending($rotation, false);
        imagesavealpha($rotation, true);

        //guardamos la imagen en archivo
        $file_name = 'bg_gafetes' . DIRECTORY_SEPARATOR . 'tags' . DIRECTORY_SEPARATOR . $this->gafete->sgft_id . '.png';
        $file = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $file_name);
        @unlink($file);
        imagepng($rotation, $file);

        //limpiamos memoria
        imagedestroy($img);
        imagedestroy($rotation);

        return $file_name;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "GF" . $this->gafete->sgft_id;

        setlocale(LC_TIME, 'Spanish');

        if (!$this->es_vertical)
            $this->setOrientation('landscape');

        $data = [];
        $data['gafete'] = $this->gafete;
        $data['tag_image'] = $this->tag_image;
        $data['qr_text'] = $this->qr_text;
        $data['anio_impresion'] = $this->annio_impresion;
        $data['es_vertical'] = $this->es_vertical;

        $view = View($this->view, $data);
        return $this->output($view);
    }


}
