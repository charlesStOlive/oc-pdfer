<?php namespace Waka\Pdfer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Redirect;
use Waka\Pdfer\Classes\PdfCreator;
use Waka\Pdfer\Models\WakaPdf;

class PdfBehavior extends ControllerBehavior
{
    use \Waka\Utils\Classes\Traits\StringRelation;

    protected $pdfBehaviorWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->pdfBehaviorWidget = $this->createPdfBehaviorWidget();
    }

    /**
     * METHODES
     */

    public function getDataSourceFromModel(String $model)
    {
        $modelClassDecouped = explode('\\', $model);
        $modelClassName = array_pop($modelClassDecouped);
        return \Waka\Utils\Models\DataSource::where('model', '=', $modelClassName)->first();
    }

    public function getModel($model, $modelId)
    {
        $myModel = new $model;
        $myModel = $myModel::find($modelId);
        return $myModel;
    }

    /**
     * LOAD DES POPUPS
     */
    public function onLoadPdfBehaviorPopupForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $dataSource = $this->getDataSourceFromModel($model);
        $options = $dataSource->getPartialOptions($modelId, 'Waka\Pdfer\Models\WakaPdf');

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;

        return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_popup.htm');
    }
    public function onLoadPdfBehaviorContentForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $dataSource = $this->getDataSourceFromModel($model);
        $options = $dataSource->getPartialOptions($modelId, 'Waka\Pdfer\Models\WakaPdf');

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;

        return [
            '#popupActionContent' => $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_content.htm'),
        ];
    }

    public function onPdfBehaviorPopupValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $wakaPdfId = post('wakaPdfId');
        $modelId = post('modelId');

        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?wakaPdfId=' . $wakaPdfId . '&modelId=' . $modelId);

    }

    public function onCloudPdfValidation()
    {
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $wakaPdfId = post('wakaPdfId');
        $modelId = post('modelId');

        $wc = new PdfCreator($wakaPdfId);
        $wc->renderCloud($modelId);

    }

    /**
     * Validations
     */
    public function CheckValidation($inputs)
    {
        $rules = [
            'modelId' => 'required',
            'wakaPdfId' => 'required',
        ];

        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }
    /**
     * Cette fonction est utilisé lors du test depuis le controller wakapdf.
     */
    public function onLoadPdfTest()
    {
        $type = post('type');
        $wakaPdfId = post('wakaPdfId');
        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?wakaPdfId=' . $wakaPdfId . '&type=' . $type);
    }
    // public function onLoadPdfTestShow()
    // {
    //     $wakaPdfId = post('wakaPdfId');
    //     $modelId = post('modelId');
    //     //trace_log($modelId);
    //     $type = 'html';
    //     $pc = new PdfCreator($wakaPdfId);
    //     $this->vars['html'] = $pc->renderPdf($modelId, $type);
    //     return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_html.htm');
    // }
    public function makepdf()
    {
        $wakaPdfId = post('wakaPdfId');
        $modelId = post('modelId');
        //trace_log($modelId);
        $type = post('type');

        $wc = new PdfCreator($wakaPdfId);
        return $wc->renderPdf($modelId, $type);
    }

    public function createPdfBehaviorWidget()
    {

        $config = $this->makeConfig('$/waka/pdfer/models/wakapdf/fields_for_test.yaml');
        $config->alias = 'pdfBehaviorformWidget';
        $config->arrayName = 'pdfBehavior_array';
        $config->model = new WakaPdf();
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
