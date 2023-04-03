<?php namespace Waka\Pdfer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Lang;
use Redirect;
use Waka\Pdfer\Classes\PdfCreator;
use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;
use Session;

class PdfBehavior extends ControllerBehavior
{
    protected $pdfBehaviorWidget;
    protected $askDataWidget;
    public $errors;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->pdfBehaviorWidget = $this->createPdfBehaviorWidget();
        $this->errors = [];
        \Event::listen('waka.utils::conditions.error', function ($error) {
            array_push($this->errors, $error);
        });
    }

    /**
     * LOAD DES POPUPS ******************************************
     */

    /**
     * Popup seul
     */
    public function onLoadPdfBehaviorPopupForm()
    {
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = \DataSources::findByClass($modelClass);
        $options = $ds->getProductorOptions('Waka\Pdfer\Models\WakaPdf', $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;
        $this->vars['errors'] = $this->errors;
        $this->vars['modelClass'] = $modelClass;

        if($options) {
            return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_popup.htm');
        } else {
            return $this->makePartial('$/waka/utils/views/_popup_no_model.htm');
        }
    }

    /**
     * Popup dans barre d'outil
     */
    public function onLoadPdfBehaviorContentForm()
    {
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = \DataSources::findByClass($modelClass);
        $options = $ds->getProductorOptions('Waka\Pdfer\Models\WakaPdf', $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;
        $this->vars['errors'] = $this->errors;
        $this->vars['modelClass'] = $modelClass;

        if($options) {
            return ['#popupActionContent' => $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_content.htm')];
        } else {
            return ['#popupActionContent' => $this->makePartial('$/waka/utils/views/_content_no_model.htm')];
        }
    }

    public function onSelectWakaPdf() {
        $productorId = post('productorId');
        $modelId = post('modelId');
        $productor = PdfCreator::find($productorId)->setModelId($modelId);
        $askDataWidget = $this->createAskDataWidget();
        $asks = $productor->getProductorAsks();
        $askDataWidget->addFields($asks);
        $this->vars['askDataWidget'] = $askDataWidget;
        return [
            '#askDataWidget' => $this->makePartial('$/waka/utils/models/ask/_widget_ask_data.htm')
        ];
    }

    /**
     * Popup lot
     */

    










    /**
     * Confirmation de popup ******************************
     */

    /**
     * popup unique
     */

    public function onPdfBehaviorPopupValidation()
    {
        $datas = post();
        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $productorId = post('productorId');
        $modelId = post('modelId');
        $inline = post('inline');
        Session::put('pdf_asks_'.$modelId, $datas['asks_array'] ?? []);
        //
        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?productorId=' . $productorId . '&modelId=' . $modelId . '&inline=' . $inline);
    }

    /**
     * Validations
     */
    public function CheckValidation($inputs)
    {
        $rules = [
            'modelId' => 'required',
            'productorId' => 'required',
        ];

        $messages = [
            'modelId.required' => Lang::get('waka.pdfer::lang.errors.modelId'),
            'wakaPdfId.wakaPdfId' => Lang::get('waka.pdfer::lang.errors.wakaPdfId'),
        ];

        $validator = \Validator::make($inputs, $rules);

        if ($validator->fails()) {
            return $validator->messages()->first();
        } else {
            return false;
        }
    }

    public function makepdf()
    {
        $productorId = \Input::get('productorId');
        $modelId = \Input::get('modelId');
        $inline = \Input::get('inline');
        $asks = Session::pull('pdf_asks_'.$modelId);
        return PdfCreator::find($productorId)->setModelId($modelId)->setAsksResponse($asks)->renderPdf($inline);
    }

    /**
     * Cette fonction est utilisé lors du test depuis le controller wakapdf.
     */
    public function onLoadPdfTest()
    {
        $inline = \Input::get('inline');
        $productorId = \Input::get('productorId');
        $modelId = \Input::get('modelId');
        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdftest/?productorId=' . $productorId . '&inline=' . $inline);
    }
    public function makepdftest()
    {
        $productorId = \Input::get('productorId');
        $inline = \Input::get('inline');
        return PdfCreator::find($productorId)->setModelTest()->renderPdf($inline);
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

    public function createAskDataWidget()
    {
        $config = $this->makeConfig('$/waka/utils/models/ask/empty_fields.yaml');
        $config->alias = 'askDataformWidget';
        $config->arrayName = 'asks_array';
        $config->model = new \Waka\Utils\Models\RuleAsk();
        $widget = $this->makeWidget('Backend\Widgets\Form', $config);
        $widget->bindToController();
        return $widget;
    }
}
