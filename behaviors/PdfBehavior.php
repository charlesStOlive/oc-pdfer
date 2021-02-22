<?php namespace Waka\Pdfer\Behaviors;

use Backend\Classes\ControllerBehavior;
use Lang;
use Redirect;
use Waka\Pdfer\Classes\PdfCreator;
use Waka\Pdfer\Models\WakaPdf;
use Waka\Utils\Classes\DataSource;

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
     * LOAD DES POPUPS
     */
    public function onLoadPdfBehaviorPopupForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $ds = new DataSource($model, 'class');
        $options = $ds->getPartialOptions($modelId, 'Waka\Pdfer\Models\WakaPdf');

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;

        return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_popup.htm');
    }
    public function onLoadPdfBehaviorContentForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $ds = new DataSource($model, 'class');
        $options = $ds->getPartialOptions($modelId, 'Waka\Pdfer\Models\WakaPdf');

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

        $inline = post('inline');

        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?wakaPdfId=' . $wakaPdfId . '&modelId=' . $modelId . '&inline=' . $inline);

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

        $messages = [
            'modelId.required' => Lang::get("waka.pdfer::lang.errors.modelId"),
            'wakaPdfId.wakaPdfId' => Lang::get("waka.pdfer::lang.errors.wakaPdfId"),
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
        $inline = post('inline');
        $wakaPdfId = post('wakaPdfId');
        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?wakaPdfId=' . $wakaPdfId . '&inline=' . $inline);
    }

    public function makepdf()
    {
        $wakaPdfId = post('wakaPdfId');
        $modelId = post('modelId');
        $inline = post('inline');
        return PdfCreator::find($wakaPdfId)->renderPdf($modelId, $inline);
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
