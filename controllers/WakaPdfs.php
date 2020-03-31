<?php namespace Waka\Pdfer\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Waka Pdfs Back-end Controller
 */
class WakaPdfs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Waka.Pdfer', 'pdfer', 'wakapdfs');
    }

    public function show()
    {
        //return \Response::view('waka.compilator::pdf.coin');
        $pdf = \PDF::loadView('waka.compilator::pdf.coin');

        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('viewport-size', '1280x1024');
        // $pdf->setOption('enable-javascript', true);
        // $pdf->setOption('javascript-delay', 5000);
        $pdf->setOption('enable-smart-shrinking', true);
        // $pdf->setOption('no-stop-slow-scripts', true);

        return $pdf->inline('test2.pdf');
        // $templateCode = "cv_1";
        // $data = $this->preparePdf($user_key);
        // /**
        //  * Construction du pdf
        //  */
        // try {
        //     /** @var PDFWrapper $pdf */
        //     $pdf = app('dynamicpdf');

        //     $options = [
        //         'logOutputFile' => storage_path('temp/log.htm'),
        //         'isRemoteEnabled' => true,
        //     ];

        //     $data->visits()->add(new Visit(['type' => 'pdf']));

        //     return $pdf
        //         ->loadTemplate($templateCode, compact('data'))
        //         ->setOptions($options)
        //         ->save(storage_path('app/media/cv/'.$data->cv_name.'.pdf'))
        //         ->stream();

        // } catch (Exception $e) {
        //     throw new ApplicationException($e->getMessage());
        // }
    }

}
