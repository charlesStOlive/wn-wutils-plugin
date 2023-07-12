<?php namespace Waka\Wutils;

use Backend;
use Backend\Models\UserRole;
use System\Classes\PluginBase;
use Illuminate\Foundation\AliasLoader;
use App;
use Waka\Wutils\Models\Settings;
use Lang;

/**
 * wutils Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails(): array
    {
        return [
            'name'        => 'waka.wutils::lang.plugin.name',
            'description' => 'waka.wutils::lang.plugin.description',
            'author'      => 'waka',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register(): void
    {
        $aliasLoader = AliasLoader::getInstance();

        //BOOT laravel PALETTE
        $aliasLoader->alias('ColorPalette', \NikKanetiya\LaravelColorPalette\ColorPaletteFacade::class);
        App::register(\NikKanetiya\LaravelColorPalette\ColorPaletteServiceProvider::class);


        //BOOT laravel Excel
        $aliasLoader->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);
        App::register(\Maatwebsite\Excel\ExcelServiceProvider::class);
        $registeredAppPathConfig = require __DIR__ . '/config/excel.php';
        \Config::set('excel', $registeredAppPathConfig);


        // // BOOT laravel Google Translate
        // $aliasLoader->alias('GoogleTranslate', \JoggApp\GoogleTranslate\GoogleTranslateFacade::class);
        // App::register(\JoggApp\GoogleTranslate\GoogleTranslateServiceProvider::class);
        // $registeredAppPathConfig = require __DIR__ . '/config/googletranslate.php';
        // \Config::set('googletranslate', $registeredAppPathConfig);


        \Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addJs('/plugins/waka/wutils/assets/js/froala.js');
            $controller->addJs('/plugins/waka/wutils/assets/js/clipboard.min.js');
            /**NODS-C*/$controller->addCss('/plugins/wcli/wconfig/assets/css/waka.css');
            $env = \Config::get("waka.wutils::env");
            //trace_log('env : '.$env);
            if ($env == 'local') {
                //trace_log('local');
                $controller->addCss('/plugins/waka/wutils/assets/css/menu_env_local_2.css');
            } else if ($env == 'dev') {
                $controller->addCss('/plugins/waka/wutils/assets/css/menu_env_dev_2.css');
            }
        });

        $this->registerConsoleCommand('waka:cleanModels', 'Waka\Wutils\Console\CleanModels');
        $this->registerConsoleCommand('waka:cleanFiles', 'Waka\Wutils\Console\CleanFiles');
        $this->registerConsoleCommand('waka:ReduceImages', 'Waka\Wutils\Console\ReduceImages');
        
        //

    }


    

    /**
     * Boot method, called right before the request route.
     */
    public function boot(): void
    {

        /**
         * Desactivation si nécessaire des boutons
         */

        \Event::listen('backend.menu.extendItems', function ($navigationManager) {
            //trace_log($navigationManager->getActiveMainMenuItem());
            if (!Settings::get('activate_dashboard')) {
                $navigationManager->removeMainMenuItem('October.Backend', 'dashboard');
            }
            if (!Settings::get('activate_cms')) {
                $navigationManager->removeMainMenuItem('October.Cms', 'cms');
            }
            if (!Settings::get('activate_builder')) {
                $navigationManager->removeMainMenuItem('RainLab.Builder', 'builder');
            }
            if (!Settings::get('activate_user_btn')) {
                $navigationManager->removeMainMenuItem('RainLab.User', 'user');
            }
            if (!Settings::get('activate_builder')) {
                $navigationManager->removeMainMenuItem('RainLab.Builder', 'builder');
            }
            if (!Settings::get('activate_media_btn')) {
                $navigationManager->removeMainMenuItem('October.Backend', 'media');
            }
        });

         \System\Controllers\Settings::extend(function ($controller) {
            if (url()->current() === Backend\Facades\Backend::url('system/settings')) {
                return;
            }
            if ($controller->formGetWidget()->model instanceof \Waka\Wutils\Models\Settings) {
                $user = \BackendAuth::getUser();
                if (!$user->isSuperUser()) {
                    return;
                }
                $controller->addDynamicMethod('onWconfigImport', function () use ($controller) {
                    $startFile = \Waka\Wutils\Models\Settings::get('start_file');
                    if ($startFile) {
                        \Excel::import(new \Waka\ImportExport\Classes\Imports\SheetsImport, storage_path('app/media/' . $startFile));
                    } else {
                        throw new \ApplicationException('Le fichier n a pas été trouvé');
                    }
                    return \Redirect::refresh();
                });
                //trace_log('classe existe ? '.class_exists('\Wcli\Wconfig\Classes\Tests'));
                if (class_exists('\Wcli\Wconfig\Classes\Tests')) {
                    //trace_log('classe existe');
                    $controller->addDynamicMethod('onWconfigTest1', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test1();
                    });
                    $controller->addDynamicMethod('onWconfigTest2', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test2();
                    });
                    $controller->addDynamicMethod('onWconfigTest3', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test3();
                    });
                    $controller->addDynamicMethod('onWconfigTest4', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test4();
                    });
                    $controller->addDynamicMethod('onWconfigTest5', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test5();
                    });
                    $controller->addDynamicMethod('onWconfigTest6', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test6();
                    });
                    $controller->addDynamicMethod('onWconfigTest7', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test7();
                    });
                    $controller->addDynamicMethod('onWconfigTest8', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test8();
                    });
                    $controller->addDynamicMethod('onWconfigTest9', function () use ($controller) {
                        $test = \Wcli\Wconfig\Classes\Tests::test9();
                    });
                }
            }
        });

    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents(): array
    {
        return []; // Remove this line to activate

        return [
            'Waka\Wutils\Components\MyComponent' => 'myComponent',
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'reTwig' => function ($twig, $drowDta, $rowDs) {
                    return \Twig::parse($twig, ['row' => $drowDta, 'ds' => $rowDs]);
                },
                'mailto' => function ($twig) {
                    $text = '';
                    if (preg_match_all('/[\p{L}0-9_.-]+@[0-9\p{L}.-]+\.[a-z.]{2,6}\b/u', $twig, $mails)) {
                        foreach ($mails[0] as $mail) {
                            $text = str_replace($mail, '<a href="mailto:' . $mail . '">' . $mail . '</a>', $text);
                        }
                        return $text;
                    } else {
                        return '';
                    }
                },
                'localeDate' => [new \Waka\Wutils\Classes\WakaDate, 'localeDate'],
                'uppercase' => function ($string) {
                    return mb_convert_case($string, MB_CASE_UPPER, "UTF-8");
                },
                'lowercase' => function ($string) {
                    return mb_convert_case($string, MB_CASE_LOWER, "UTF-8");
                },
                'ucfirst' => function ($string) {
                    return ucfirst($string);
                },
                'lcfirst' => function ($string) {
                    return lcfirst($string);
                },
                'toJson' => function ($twig) {
                    return json_encode($twig);
                },
                'workflow' => function ($twig) {
                    return $twig->wfPlaceLabel();
                },
                'camelCase' => function ($twig) {
                    return camel_case($twig);
                },
                'snakeCase' => function ($twig) {
                    return snake_case($twig);
                },
                'studly' => function ($twig) {
                    return \Str::studly($twig);
                },
                // TODO A SUPPRIMER ? 
                'colorArray' => function ($twig, $color1) {
                    $colorArray = [];
                    return $colorArray;
                },
                // TODO A SUPPRIMER ? 
                'ident' => function ($string, $number) {
                    $number = $number * 4;
                    $spaces = str_repeat(' ', $number);
                    return rtrim(preg_replace('#^(.+)$#m', sprintf('%1$s$1', $spaces), $string));
                },
                'getContent' => function ($twig, $code, $column) {
                    $content = $twig->getContent($code);
                    return  $content[$column] ?? null;
                },
                'getRecursiveContent' => function ($twig, $code) {
                    if (!$twig) {
                        return null;
                    }
                    //trace_log("twig getRecursiveContent");
                    //trace_log($code);
                    $content = $twig->getThisParentValue($code);
                    //trace_log('content');
                    //trace_log($content);
                    return  $content;
                },
                'getFileByTitleFromMany' => function ($twig, $code, $with, $height) {
                    if (!$twig) {
                        \Log::error(sprintf('le code twig %s renvoie une valeur null dans getFileByTitleFromMany', $code));
                        return null;
                    }
                    //trace_log('getFileFromMany');
                    $image = $twig->filter(function ($item, $key) use ($code) {
                        //trace_log($item->toArray());
                        return $item->title == $code;
                    });
                    if ($image->first() ?? false) {
                        return $image->first()->getThumb($with, $height);
                    } else {
                        return null;
                    }
                }


            ],
            'functions' => [
                // Using an inline closure
                'getColor' => function ($color, $mode = "rgba", $transform = null, $factor = 0.1) {
                    if (!$color) {
                        $color =  "#ff0000";
                    }
                    $color = new Color($color);
                    switch ($transform) {
                        case 'makeGradient':
                            $factor = $factor * 10;
                            $colors = $color->makeGradient($factor);
                            return  $colors;
                        case 'complementary':
                            $color = $color->complementary();
                            break;
                        case 'lighten':
                            $color = $color->lighten($factor);
                            break;
                        case 'darken':
                            $color = $color->darken($factor);
                            break;
                    }
                    $finalColor = $color;
                    if (is_string($color)) {
                        $finalColor = new Color($color);
                    }
                    switch ($mode) {
                        case 'rgba':
                            return $finalColor->getRgb();
                        case 'string':
                            return '#' . $finalColor->getHex();
                    }
                },
                'stubCreator' => function ($template, $allData, $secificData, $dataName = null) {
                    $allData['specific'] = $secificData;
                    $allData['dataName'] = $dataName;
                    $templatePath = plugins_path('waka/wutils/console/' . $template);
                    $templateContent = \File::get($templatePath);
                    $content = \Twig::parse($templateContent, $allData);
                    return $content;
                },
                'var_dump' => function ($expression) {
                    ob_start();
                    var_dump($expression);
                    $result = ob_get_clean();

                    return $result;
                },

            ],


        ];
    }

    public function registerListColumnTypes()
    {
        return [
            'waka-calcul' => [\Waka\Wutils\Columns\CalculColumn::class, 'render'],
            'euro' => function ($value) {
                return number_format($value, 2, ',', ' ') . ' €';
            },
            'euro-int' => function ($value) {
                return number_format($value, 0, ',', ' ') . ' €';
            },
            'raw' => function ($value) {
                return $value;
            },
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions(): array
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerSettings(): array
    {
        return [
            'utils_settings' => [
                'label' => Lang::get('waka.wutils::lang.settings.label'),
                'description' => Lang::get('waka.wutils::lang.settings.description'),
                'category' => Lang::get('waka.wutils::lang.menu.settings_category'),
                'icon' => 'icon-wrench',
                'class' => 'Waka\Wutils\Models\Settings',
                'order' => 150,
                'permissions' => ['wcli.wconfig.admin'],
            ],


        ];
    }
}
