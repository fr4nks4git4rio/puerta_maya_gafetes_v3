<?php
namespace App\Reports;


use Illuminate\Http\Request;

//use App\PermisoMantenimiento;
use App\Reports\BaseReport;

class SaldoLocalesReport extends BaseReport
{

    private $records = null;

    private $view = "pdf-reports.saldo-locales";


    public function setRecords($records)
    {
        $this->records = $records;
    }

    /**
     * Genera el reporte de estado de cuenta corriente de una cuenta.
     *
     * @return void
     */
    public function exec()
    {
        $this->prefijo = "PTMP_".date('YmdHi');

        $this->pdfSize = 'letter';
        $this->pdfOrientation = 'landscape';
        //        $request = $this->request;
        // dd($request);
        setlocale(LC_TIME, 'Spanish');


        $data = [];
        $data['records'] = $this->records;

        $view = View($this->view,$data);
        //return $view->render();
        return $this -> output($view);
    }


}
