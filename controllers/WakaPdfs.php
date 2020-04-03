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
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $duplicateConfig = 'config_duplicate.yaml';

    public function __construct()
    {
        parent::__construct();

        \BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Waka.Pdfer', 'wakapdfs');
    }

}
