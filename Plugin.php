<?php namespace Waka\Pdfer;

use App;
use Backend;
use Config;
use Illuminate\Foundation\AliasLoader;
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
        $this->bootPackages();

        // \Event::listen('backend.update.prod', function ($controller) {
        //     if (get_class($controller) == 'Waka\Pdfer\Controllers\WakaPdf') {
        //         return;
        //     }

        //     if (in_array('Waka.Pdfer.Behaviors.PdfBehavior', $controller->implement)) {
        //         $data = [
        //             'model' => $modelClass = str_replace('\\', '\\\\', get_class($controller->formGetModel())),
        //             'modelId' => $controller->formGetModel()->id,
        //         ];
        //         return \View::make('waka.pdfer::publishPdf')->withData($data);;
        //     }
        // });
        // \Event::listen('popup.actions.prod', function ($controller, $model, $id) {
        //     if (get_class($controller) == 'Waka\Pdfer\Controllers\WakaPdf') {
        //         return;
        //     }

        //     if (in_array('Waka.Pdfer.Behaviors.PdfBehavior', $controller->implement)) {
        //         //trace_log("Laligne 1 est ici");
        //         $data = [
        //             'model' => str_replace('\\', '\\\\', $model),
        //             'modelId' => $id,
        //         ];
        //         return \View::make('waka.pdfer::publishPdfContent')->withData($data);;
        //     }
        // });
    }

    public function bootPackages()
    {
        // Get the namespace of the current plugin to use in accessing the Config of the plugin
        $pluginNamespace = str_replace('\\', '.', strtolower(__NAMESPACE__));

        // Instantiate the AliasLoader for any aliases that will be loaded
        $aliasLoader = AliasLoader::getInstance();

        // Get the packages to boot
        $packages = Config::get($pluginNamespace . '::packages');

        // Boot each package
        foreach ($packages as $name => $options) {
            // Setup the configuration for the package, pulling from this plugin's config
            if (!empty($options['config']) && !empty($options['config_namespace'])) {
                Config::set($options['config_namespace'], $options['config']);
            }
            // Register any Service Providers for the package
            if (!empty($options['providers'])) {
                foreach ($options['providers'] as $provider) {
                    App::register($provider);
                }
            }
            // Register any Aliases for the package
            if (!empty($options['aliases'])) {
                foreach ($options['aliases'] as $alias => $path) {
                    $aliasLoader->alias($alias, $path);
                }
            }
        }
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'pdfPartial' => function ($twig, $data, $dataKey2 = null, $data2 = null) {
                    $bloc = \Waka\Pdfer\Models\Bloc::where('slug', $twig)->first();
                    //trace_log($bloc->contenu);
                    if ($dataKey2) {
                        $data[$dataKey2] = $data2;
                        $test = compact('data');
                    }
                    if ($bloc) {
                        $bloc_html = \Twig::parse($bloc->contenu, compact('data'));
                        return $bloc_html;
                    } else {
                        return null;
                    }
                    return null;
                },
            ],
        ];
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
                'url' => \Backend::url('waka/pdfer/wakapdfs/index/wakapdfs'),
                'permissions' => ['waka.pdfer.admin.*'],
                'order' => 20,
            ],
        ];
    }
}
