<?php namespace Waka\Pdfer;

use Backend;
use System\Classes\PluginBase;

/**
 * Pdfer Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Pdfer',
            'description' => 'No description provided yet...',
            'author' => 'Waka',
            'icon' => 'icon-leaf',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \Event::listen('backend.update.prod', function ($controller) {
            if (get_class($controller) == 'Waka\Pdfer\Controllers\WakaPdf') {
                return;
            }

            if (in_array('Waka.Pdfer.Behaviors.PdfBehavior', $controller->implement)) {
                $data = [
                    'model' => $modelClass = str_replace('\\', '\\\\', get_class($controller->formGetModel())),
                    'modelId' => $controller->formGetModel()->id,
                ];
                return \View::make('waka.pdfer::publishPdf')->withData($data);;
            }
        });
        \Event::listen('popup.actions.prod', function ($controller, $model, $id) {
            if (get_class($controller) == 'Waka\Pdfer\Controllers\WakaPdf') {
                return;
            }

            if (in_array('Waka.Pdfer.Behaviors.PdfBehavior', $controller->implement)) {
                //trace_log("Laligne 1 est ici");
                $data = [
                    'model' => str_replace('\\', '\\\\', $model),
                    'modelId' => $id,
                ];
                return \View::make('waka.pdfer::publishPdfContent')->withData($data);;
            }
        });

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'Waka\Pdfer\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'waka.pdfer.admin.super' => [
                'tab' => 'Waka - Pdfer',
                'label' => 'Super administrateur de Pdfer',
            ],
            'waka.pdfer.admin.base' => [
                'tab' => 'Waka - Pdfer',
                'label' => 'Administrateur de Pdfer',
            ],
            'waka.pdfer.user' => [
                'tab' => 'Waka - Pdfer',
                'label' => 'Utilisateur de Pdfer',
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'wakapdfs' => [
                'label' => \Lang::get('waka.pdfer::lang.menu.wakapdfs'),
                'description' => \Lang::get('waka.pdfer::lang.menu.wakapdfs_description'),
                'category' => \Lang::get('waka.utils::lang.menu.settings_category_model'),
                'icon' => 'icon-file-pdf-o',
                'url' => \Backend::url('waka/pdfer/wakapdfs'),
                'permissions' => ['waka.pdfer.admin.*'],
                'order' => 20,
            ],
            'pdflayouts' => [
                'label' => \Lang::get('waka.pdfer::lang.menu.pdflayouts'),
                'description' => \Lang::get('waka.pdfer::lang.menu.pdflayouts_description'),
                'category' => \Lang::get('waka.utils::lang.menu.settings_category_model'),
                'icon' => 'icon-css3',
                'url' => \Backend::url('waka/pdfer/pdflayouts'),
                'permissions' => ['waka.pdfer.admin.*'],
                'order' => 21,
            ],
        ];
    }
}
