<?php namespace Waka\Pdfer\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use System\Classes\SettingsManager;

/**
 * Waka Pdfs Back-end Controller
 */
class WakaPdfs extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController',
        'Waka.Informer.Behaviors.PopupInfo',
        'Waka.Pdfer.Behaviors.PdfBehavior',
        'Waka.Utils.Behaviors.DuplicateModel',
        'waka.Utils.Behaviors.SideBarAttributesBehavior',
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';
    public $sidebarInfoConfig = '$/waka/crsm/config/config_wakapdfs_attributes.yaml';

    public $sidebarAttributes;

    public function __construct()
    {
        parent::__construct();

        \BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Pdfer', 'wakapdfs');

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

}
