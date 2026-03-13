<?php

namespace App\Clases;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoSaver
{


//    public static function savePhoto($upload_str,$fileName,$fileThumb){
//
//        if (preg_match('/^data:image\/(\w+);base64,/', $upload_str, $type)) {
//            $image_64 = $upload_str;
//
//            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
//
//            if ($extension !== 'jpeg' && $extension !== 'jpg') {
//                throw new \Exception('Invalid image type');
//            }
//
//            $replace = substr($image_64, 0, strpos($image_64, ',')+1);
//
//            $image = str_replace(array($replace, ' '), array('', '+'), $image_64);
//
////            $imageName = $fileName.'.'.$extension;
//
//            Storage::disk('empleados')->put($fileName, base64_decode($image));
//
//        } else {
//            throw new \Exception('did not match data URI with image data');
//        }
//
////        file_put_contents($fileName, $data);
//
//        // ----------------------------------------------------
//        $file = Storage::disk('empleados')->readStream($fileName);
//        $file_url = Storage::disk('empleados')->url($fileName);
//        list($ancho, $alto) = getimagesize($file_url);
//        $nuevo_alto = 66;
//        $nuevo_ancho = 55;
//        $thumb  = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
//        $origen = imagecreatefromstring($image);
//        // dd($fileName,$origen);
//
//        imagecopyresized($file, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
//        imagejpeg($thumb, $fileThumb);
//        Storage::disk('empleados')->put($fileThumb, base64_decode($thumb));
//
//    }

    public static function savePhoto($upload_str,$fileName,$fileThumb){

        if (preg_match('/^data:image\/(\w+);base64,/', $upload_str, $type)) {

            $data = substr($upload_str, strpos($upload_str, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            if (!in_array($type, [ 'jpeg' ])) {
                throw new \Exception('Invalid image type');
            }

            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception('base64_decode failed');
            }

        } else {
            throw new \Exception('did not match data URI with image data');
        }

        file_put_contents($fileName, $data);

        // ----------------------------------------------------
        list($ancho, $alto) = getimagesize($fileName);
        $nuevo_alto = 66;
        $nuevo_ancho = 55;
        $thumb  = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        $origen = imagecreatefromstring($data);
        // dd($fileName,$origen);

        imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
        imagejpeg($thumb, $fileThumb);

    }


}
