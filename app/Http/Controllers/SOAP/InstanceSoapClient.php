<?php
/**
 * Created by PhpStorm.
 * User: Frank
 * Date: 4/25/2019
 * Time: 10:00 PM
 */

namespace App\Http\Controllers\SOAP;

class InstanceSoapClient extends BaseSoapController
{
  public static function init()
  {
    try{
      $wsdlUrl = self::getWsdl();
      $soapClientOptions = [
        'stream_context' => self::generateContext(),
        'cache_wsdl' => 0,
        'trace' => true
      ];

      return new \SoapClient($wsdlUrl, $soapClientOptions);
    }catch (\SoapFault $e){
      return ['message' => $e->getMessage()];
    }

  }
}