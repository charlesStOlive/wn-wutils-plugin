<?php

namespace Waka\Wutils\Widgets;

use Backend\Classes\WidgetBase;
use Winter\Storm\Support\Collection;
use Event;

class Btns extends WidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'production';

    public $config;
    public $workflowConfigState;
    public $model;
    public $fields;
    public $format;
    public $context;
    public $modelClass;
    public $user;

    public function prepareComonVars($context)
    {
        
        $this->vars['context'] = $this->context = $context;
        $this->vars['modelClass'] = $this->modelClass = str_replace('\\', '\\\\', $this->config->modelClass);
        $this->vars['user'] = $this->user = \BackendAuth::getUser();
        $this->model = $this->controller->formGetModel();
    }

    public function renderBar($context = 'update', $mode = 'update', $modelId = null)
    {
        $this->prepareComonVars($context);
        $this->vars['mode'] = $mode;
        //Est ce qu'il y a des partials à ajouter à la barre ?
        $this->vars['partials'] = $this->config->action_bar['partials'] ?? null;
        $model = $this->controller->formGetModel();
        $this->vars['modelId'] = $model->id;
        return $this->makePartial('action_bar');
    }

    public function renderActionBtn($context = null)
    {
        if ($context == 'preview') {
            return null;
        }
        $this->prepareComonVars($context);

        $modifier = Event::until('waka.wutils.btns.replace_action_btn', [$this->model]);
        if($modifier) {
            return $modifier;
        } else {
            return $this->makePartial('sub/base_buttons');
            // return $this->makePartial('sub/base_buttons');
        }
        
        
    }

    public function renderBreadcrump($context = null)
    {
        $this->prepareComonVars($context);
        $model = $this->controller->formGetModel();
        if (!$model) {
            return;
        }
        if ($this->config->breadcrump) {
            $configBreadCrump = $this->config->breadcrump;
            foreach ($configBreadCrump as $key => $config) {
                $splitUrl = explode(':', $config);
                $varInUrl = $splitUrl[1] ?? false;
                if ($varInUrl) {
                    $configBreadCrump[$key] = $splitUrl[0] . $model->{$varInUrl};
                }
            }
            $this->vars['breadcrump'] = $configBreadCrump;
            return $this->makePartial('breadcrump');
        } else {
            return '';
        }
    }

    public function renderToolBar($secondaryLabel = false)
    {
        $this->prepareComonVars(null);
        $toolBar = null;
        $toolBar = $this->config->tool_bar;
        $base = $toolBar['base'] ?? false;
        if ($base) {
            $base = $this->getPermissions($base);
        }
        $this->vars['base'] = $base;
        $this->vars['partials'] = $toolBar['partials'] ?? null;
        return $this->makePartial('tool_bar');
    }
    private function getPermissions($btns)
    {
        //trace_log("getPermissions");
        $btnWithPermission = [];
        foreach ($btns as $key => $btn) {
            $permissionGranted = false;

            $permission = $btn['permissions'] ?? null;
            //trace_log($permission);
            if (!$permission) {
                $permissionGranted = true;
            } else {
                $permissionGranted = $this->user->hasAccess($permission,false);
            }
            //trace_log($btn);
            $btn['permissions']  = $permissionGranted;
            $btnWithPermission[$key] = $btn;
        }
        return $btnWithPermission;
    }
}
