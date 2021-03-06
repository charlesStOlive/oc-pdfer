<?php namespace Waka\Pdfer\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;
use Waka\Pdfer\Models\WakaPdf;

/**
 * Waka Pdf Back-end Controller
 */
class WakaPdfs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Utils.Behaviors.BtnsBehavior',
        'waka.Utils.Behaviors.SideBarAttributesBehavior',
        'Waka.Pdfer.Behaviors.PdfBehavior',
        'Backend.Behaviors.ReorderController',
        'Waka.Utils.Behaviors.DuplicateModel',

    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = [
        'wakapdfs' => 'config_list.yaml',
        'layouts' => 'config_list_layouts.yaml',
        'blocs' => 'config_list_blocs.yaml',
    ];
    public $btnsConfig = 'config_btns.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $sidebarAttributesConfig = 'config_attributes.yaml';    

    public $requiredPermissions = ['waka.pdfer.*'];
    //FIN DE LA CONFIG AUTO

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Pdfer', 'WakaPdfs');

        $blocsWidget = new \Waka\Pdfer\Widgets\SidebarBlocs($this);
        $blocsWidget->alias = 'blocsWidget';
        $blocsWidget->bindToController();
    }

    //startKeep/

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function update_onSave($recordId = null)
    {
        $this->asExtension('FormController')->update_onSave($recordId);
        return [
            '#sidebar_attributes' => $this->attributesRender($this->params[0]),
        ];
    }
    public function index($tab = null)
    {
        $this->asExtension('ListController')->index();
        $this->bodyClass = 'compact-container';
        $this->vars['activeTab'] = $tab ?: 'templates';
    }

    public function formExtendFieldsBefore($form) {
        if(!$this->user->hasAccess(['waka.pdfer.admin.super'])) {
            //Le blocage du champs code de ask est fait dans le model wakaMail
            $model =  WakaPdf::find($this->params[0]);
            $countAsks = 0;
            if($model->asks) {
                $countAsks = count($model->asks);
                $form->tabs['fields']['asks']['maxItems'] = $countAsks;
                $form->tabs['fields']['asks']['minItems'] = $countAsks;
            }
        }
    }

    //endKeep/
}
