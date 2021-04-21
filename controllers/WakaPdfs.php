<?php namespace Waka\Pdfer\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Waka Pdf Back-end Controller
 */
class WakaPdfs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
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
    public $duplicateConfig = 'config_duplicate.yaml';
    public $reorderConfig = 'config_reorder.yaml';
    public $sidebarAttributesConfig = 'config_attributes.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Pdfer', 'WakaPdfs');

        $blocsWidget = new \Waka\Pdfer\Widgets\SidebarBlocs($this);
        $blocsWidget->alias = 'blocsWidget';
        $blocsWidget->bindToController();
    }

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
}
