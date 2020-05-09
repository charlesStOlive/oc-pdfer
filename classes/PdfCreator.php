<?php namespace Waka\Pdfer\Classes;

use Waka\Pdfer\Models\WakaPdf;

class PdfCreator
{

    private $dataSourceModel;
    private $dataSourceId;
    private $additionalParams;
    private $dataSourceAdditionalParams;

    public function __construct($pdf_id)
    {
        //trace_log($pdf_id);
        $wakapdf = WakaPdf::find($pdf_id);
        $this->wakapdf = $wakapdf;

    }

    public function prepareCreatorVars($dataSourceId)
    {
        $this->dataSourceModel = $this->linkModelSource($dataSourceId);
        $this->dataSourceAdditionalParams = $this->dataSourceModel->hasRelationArray;
    }
    public function setAdditionalParams($additionalParams)
    {
        if ($additionalParams) {
            $this->additionalParams = $additionalParams;
        }
    }
    private function linkModelSource($dataSourceId)
    {
        $this->dataSourceId = $dataSourceId;
        // si vide on puise dans le test
        if (!$this->dataSourceId) {
            $this->dataSourceId = $this->wakapdf->data_source->test_id;
        }
        //on enregistre le modèle
        //trace_log($this->wakapdf->data_source->modelClass);
        return $this->wakapdf->data_source->modelClass::find($this->dataSourceId);
    }

    public function renderPdf($dataSourceId, $type = 'inline')
    {
        $this->prepareCreatorVars($dataSourceId);

        $varName = strtolower($this->wakapdf->data_source->model);

        $doted = $this->wakapdf->data_source->getValues($dataSourceId);
        $img = $this->wakapdf->data_source->getPicturesUrl($dataSourceId, $this->wakapdf->images);
        $fnc = $this->wakapdf->data_source->getFunctionsCollections($dataSourceId, $this->wakapdf->model_functions);
        $css = null;
        if ($this->wakapdf->pdf_layout) {
            $css = \File::get(plugins_path() . $this->wakapdf->pdf_layout->crsm_layout);
        }
        $model = [
            $varName => $doted,
            'IMG' => $img,
            'FNC' => $fnc,
            'css' => $css,
        ];

        $html = \Twig::parse($this->wakapdf->template, $model);

        //return $html;

        //trace_log($html);

        $pdf = \PDF::loadHtml($html);
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('viewport-size', '1280x1024');
        //$pdf->setOption('zoom', '1.5');
        // $pdf->setOption('enable-javascript', true);
        // $pdf->setOption('javascript-delay', 5000);
        //$pdf->setOption('enable-smart-shrinking', true);
        // $pdf->setOption('no-stop-slow-scripts', true);

        if (!$type || $type == "download") {
            return $pdf->download('test2.pdf');
        } else {
            return $pdf->inline('test2.pdf');
        }
    }

    public function getDotedValues()
    {
        $array = [];
        if ($this->additionalParams) {
            if (count($this->additionalParams)) {
                $rel = $this->wakapdf->data_source->getDotedRelationValues($this->dataSourceId, $this->additionalParams);
                //trace_log($rel);
                $array = array_merge($array, $rel);
                //trace_log($array);
            }
        }

        $rel = $this->wakapdf->data_source->getDotedValues($this->dataSourceId);
        //trace_log($rel);
        $array = array_merge($array, $rel);
        return $array;

    }

}
