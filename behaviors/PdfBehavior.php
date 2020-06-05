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

    public function getDataSourceClassName(String $model)
    {
        $modelClassDecouped = explode('\\', $model);
        return array_pop($modelClassDecouped);

    }

    public function getDataSourceFromModel(String $model)
    {
        $modelClassName = $this->getDataSourceClassName($model);
        //On recherche le data Source depuis le nom du model
        return \Waka\Utils\Models\DataSource::where('model', '=', $modelClassName)->first();
    }

    public function getModel($model, $modelId)
    {
        $myModel = $model::find($modelId);
        return $myModel;
    }
    public function checkScopes($myModel, $scopes)
    {
        $result = false;

        $conditions = $scopes['conditions'] ?? null;
        $mode = $scopes['mode'] ?? 'all';

        //trace_log("'mode : " . $mode);

        if (!$conditions) {
            //si on ne retrouve pas les conditions on retourne true pour valider le model
            return true;
        }

        $nbConditions = count($conditions);
        $conditionsOk = [];

        foreach ($conditions as $condition) {
            $test = false;
            if (!$condition['self']) {
                $model = $this->getStringModelRelation($myModel, $condition['target']);
                $test = in_array($model->id, $condition['ids']);
            } else {
                //trace_log($condition['ids']);
                $test = in_array($myModel->id, $condition['ids']);

            }

            if ($test) {
                if ($mode == 'one') {
                    //si le test est bon et que le mode est 'one' a la première bonne valeur on retourne oui
                    return true;
                }
                //si le test est bon mais que toutes les conditions doivent être bonne  on le met dans le tableau des OK
                array_push($conditionsOk, $test);
            }
        }
        //trace_log("nbConditions : " . $nbConditions);
        //trace_log("count(conditionsOk) : " . count($conditionsOk));
        if ($nbConditions == count($conditionsOk)) {
            return true;
        } else {
            return false;
        }

    }

    public function getPartialOptions($model, $modelId)
    {
        $modelClassName = $this->getDataSourceClassName($model);

        $options = wakaPdf::whereHas('data_source', function ($query) use ($modelClassName) {
            $query->where('model', '=', $modelClassName);
        });

        $myModel = $this->getModel($model, $modelId);

        $optionsList = [];

        foreach ($options->get() as $option) {
            if ($option->scopes) {
                //Si il y a des limites (scopes ans pdf) verification des critères
                if ($this->checkScopes($myModel, $option->scopes)) {
                    $optionsList[$option->id] = $option->name;
                }
            } else {
                $optionsList[$option->id] = $option->name;
            }
        }
        return $optionsList;

    }
    /**
     * LOAD DES POPUPS
     */
    public function onLoadPdfBehaviorPopupForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $options = $this->getPartialOptions($model, $modelId);

        $this->vars['options'] = $options;
        $this->vars['modelId'] = $modelId;
        // $this->vars['dataSrcId'] = $dataSource->id;

        return $this->makePartial('$/waka/pdfer/behaviors/pdfbehavior/_popup.htm');
    }
    public function onLoadPdfBehaviorContentForm()
    {
        $model = post('model');
        $modelId = post('modelId');

        $options = $this->getPartialOptions($model, $modelId);

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
