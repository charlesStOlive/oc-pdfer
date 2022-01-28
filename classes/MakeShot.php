<?php namespace Waka\Pdfer\Classes;

use Spatie\Browsershot\Browsershot;
use Waka\Utils\Classes\TmpFiles;


class MakeShot
{

    public static function htm($htm, $width, $height, $name = 'temp.jpeg',$retain = 'week', $returnPath = false, $showBackground = true, $emulateMedia = 'screen') {
        
        $browsershot = Browsershot::html($htm);
        return self::makeAndReturn($browsershot,$width, $height,$retain, $name, $returnPath, $showBackground, $emulateMedia);
        
    }

    public static function view($view, $data, $width, $height, $retain, $name, $returnPath = false, $showBackground = true, $emulateMedia = 'screen') {
        $html = \View::make($view, $data);
        $browsershot = Browsershot::html($htm);
        return self::makeAndReturn($browsershot,$width, $height,$retain, $name, $returnPath, $showBackground, $emulateMedia);

        
    }

    public static function makeAndReturn($browsershot, $width, $height,$retain, $name, $returnPath = false, $showBackground = true, $emulateMedia = 'screen') {
        //trace_log('makeAndReturn');
        $tmpfile =  TmpFiles::createDirectory($retain)->emptyFile($name);
        //trace_log($tmpfile->getFilePath());
        if($showBackground)
        {
            $browsershot->showBackground();
        }  
        $browsershot->emulateMedia($emulateMedia)          
            ->windowSize($width,  $height)
            ->save($tmpfile->getFilePath());
        if($returnPath) {
            return $tmpfile->getFilePath();
        } else {
            return $tmpfile->getFileUrl();
        }
    }

}