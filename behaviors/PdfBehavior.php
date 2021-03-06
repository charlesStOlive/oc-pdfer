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
     * LOAD DES POPUPS ******************************************
     */

    /**
     * Popup seul
     */
    public function onLoadPdfBehaviorPopupForm()
    {
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getProductorOptions('Waka\Pdfer\Models\WakaPdf', $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;

        return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_popup.htm');
    }

    /**
     * Popup dans barre d'outil
     */
    public function onLoadPdfBehaviorContentForm()
    {
        $modelClass = post('modelClass');
        $modelId = post('modelId');

        $ds = new DataSource($modelClass, 'class');
        $options = $ds->getProductorOptions('Waka\Pdfer\Models\WakaPdf', $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;

        return ['#popupActionContent' => $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_content.htm')];
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

        $errors = $this->CheckValidation(\Input::all());
        if ($errors) {
            throw new \ValidationException(['error' => $errors]);
        }
        $productorId = post('productorId');
        $modelId = post('modelId');

        $inline = post('inline');

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
        $productorId = post('productorId');
        return Redirect::to('/backend/waka/pdfer/wakapdfs/makepdf/?wakaPdfId=' . $productorId . '&inline=' . $inline);
    }

    public function makepdf()
    {
        $productorId = post('productorId');
        $modelId = post('modelId');
        $inline = post('inline');
        return PdfCreator::find($productorId)->setModelId($modelId)->renderPdf($inline);
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
