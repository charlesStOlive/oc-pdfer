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
        'Backend.Behaviors.RelationController',
        'Waka.Utils.Behaviors.SideBarUpdate',
        'Waka.Pdfer.Behaviors.PdfBehavior',
        'Backend.Behaviors.ReorderController',
        'Waka.Utils.Behaviors.DuplicateModel',
    ];
    public $formConfig = 'config_form.yaml';
    public $listConfig = [
        'wakapdfs' => 'config_list.yaml',
        'layouts' => 'config_list_layouts.yaml',
    ];
    public $btnsConfig = 'config_btns.yaml';
    public $relationConfig = 'config_relation.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $sideBarUpdateConfig = 'config_side_bar_update.yaml';

    public $requiredPermissions = ['waka.pdfer.*'];
    //FIN DE LA CONFIG AUTO
    //startKeep/

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Pdfer', 'WakaPdfs');
    }

    public function update($id)
    {
        $this->bodyClass = 'compact-container';
        return $this->asExtension('FormController')->update($id);
    }

    public function index($tab = null)
    {
        $this->asExtension('ListController')->index();
        $this->bodyClass = 'compact-container';
        $this->vars['activeTab'] = $tab ?: 'templates';
    }

    public function update_onSave($recordId = null)
    {
        $this->asExtension('FormController')->update_onSave($recordId);
        // return [
        //     '#sidebar_attributes' => $this->attributesRender($this->params[0]),
        // ];
        $fieldAttributs = $this->formGetWidget()->renderField('attributs', ['useContainer' => true]);
        $fieldInfos = $this->formGetWidget()->renderField('infos', ['useContainer' => true]);
        //trace_log($fieldInfos);

        return [
            '#Form-field-WakaPdf-attributs-group' => $fieldAttributs,
            '#Form-field-WakaPdf-infos-group' => $fieldInfos
        ];
    }

    //endKeep/
}

