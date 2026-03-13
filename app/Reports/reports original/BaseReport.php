<?php
namespace App\Reports;

use Illuminate\Http\Request;
use App\User;


class BaseReport
{

  protected $pdf = false;
  protected $store = false;
  protected $request = null;
  protected $storePath = "";
  protected $fileName = "";

  protected $dompdf = null;
  protected $prefijo = "RP";

  protected $pdfSize = 'custom'; //letter, legal, a4 , custom
  protected $custom_h = '85.60'; //mm
  protected $custom_w = '53.98'; //mm

  protected $pdfOrientation = 'portrait'; //portrait, landscape


    public function __construct( $request, bool $pdf = false, bool $store=false)
    {

        $this->request = $request;
        $this->pdf     = $pdf;
        $this->store   = $store;
    }

    public function getFileName() :string
    {
    return $this->fileName;
    }

    public function setSize(string $size){
      $this->pdfSize = $size;
    }

    public function setOrientation(string $orientation){
        $this->pdfOrientation = $orientation;

        if($this->pdfSize == 'custom' && $this->pdfOrientation == 'landscape'){
            $this->custom_h = '53.98';
            $this->custom_w = '85.60';
        }

    }

  public function output($view)
  {

    if($this -> pdf === true){

        $filename= $this -> prefijo.'_'.date('Ymd') . '.pdf';
        $this->fileName = $filename;


        $PDF =  \PDF::loadHTML($view->render());
            // ->setPaper($this->pdfSize)
            // ->setOrientation($this->pdfOrientation)


        if($this->pdfSize == 'custom'){
            $PDF->setOption('page-height',$this->custom_h);
            $PDF->setOption('page-width',$this->custom_w);
            $PDF->setOption('margin-left','0');
            $PDF->setOption('margin-right','0');
            $PDF->setOption('margin-top','0');
            $PDF->setOption('margin-bottom','0');
        }else{
            $PDF->setPaper( $this->pdfSize);
            $PDF->setOrientation( $this->pdfOrientation);
        }


      if($this->store === true ){

        // $filename.='.pdf';
        // $output = $this -> dompdf ->output();
        $output = $PDF->output();

        \Storage::disk('reports')->put($filename, $output);
        return $filename;
      }

      // Output the generated PDF to Browser
      return $PDF->inline($filename);
    //   $this->dompdf->stream($filename, array("Attachment" => 0));
      // return $this->dompdf->output();
    }

    return $view;
  }

  /**
   * Genera el reporte
   */
   public function exec(){

   }

}
