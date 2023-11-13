<?php

namespace Waka\Wutils\Widgets;

use Backend\Classes\WidgetBase;
use Winter\Storm\Support\Collection;
use Event;

class WakaController extends WidgetBase
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
    public $backendUrl;
    public $user;
    public $callout;

    public function prepareComonVars($context)
    {

        $this->vars['context'] = $this->context = $context;
        $this->vars['backendUrl'] = $this->config->backendUrl;
        $this->vars['modelClass'] = $this->modelClass = str_replace('\\', '\\\\', $this->config->modelClass);
        $this->vars['user'] = $this->user = \BackendAuth::getUser();
        $this->model = $this->controller->formGetModel();
    }

    public function renderBar($context = 'update', $mode = 'update', $modelId = null)
    {
        $this->prepareComonVars($context);
        $this->vars['mode'] = $mode;
        //Est ce qu'il y a des partials à ajouter à la barre ?
        $this->vars['partials'] = $this->config->controllerConfig['update']['partials'] ?? null;
        $model = $this->controller->formGetModel();
        $this->vars['modelId'] = $model->id;
        return $this->makePartial('action_bar', ['context' => $context]);
    }

    public function renderActionBtn($context = null)
    {
        //trace_log('renderActionBtn');
        if ($context == 'preview') {
            return null;
        }
        $this->prepareComonVars($context);

        $modifier = Event::until('waka.wutils.wakacontroller.replace_action_btn', [$this->model]);
        $hideDeleteBtn = Event::until('controller.wakacontroller.action_bar.hide_delete', [$this->model]);
        //trace_log('$hideDeleteBtn!!',$hideDeleteBtn);
        if ($context == 'create') {
            $hideDeleteBtn = true;
        }
        $this->vars['hideDeleteBtn'] = $hideDeleteBtn;
        if ($modifier) {
            return $modifier;
        } else {
            return $this->makePartial('sub/base_buttons');
            // return $this->makePartial('sub/base_buttons');
        }
    }

    public function renderCreate()
    {
        $this->prepareComonVars('create');
        return $this->makePartial('create');
    }
    public function renderPreview()
    {
        $this->prepareComonVars('preview');
        return $this->makePartial('preview');
    }
    public function renderUpdate($twoColumns = false, $showSecondaryTabs = false)
    {
        $this->prepareComonVars('update');
        $this->vars['showTabs'] = $showSecondaryTabs;
        if ($twoColumns) {
            return $this->makePartial('update_2col');
        } else {
            return $this->makePartial('update');
        }
    }

    public function renderBreadcrumb($context = null)
    {
        $this->prepareComonVars($context);
        $model = $this->controller->formGetModel();
        if (!$model) {
            return;
        }

        if ($breadcrumb = $this->config->controllerConfig['breadcrumb'] ?? false) {
            if (isset($breadcrumb['rows']) && is_array($breadcrumb['rows'])) {
                foreach ($breadcrumb['rows'] as $key => &$row) {
                    if (preg_match('/:(\w+)/', $row['url'], $matches)) {
                        $paramName = $matches[1]; // Extract the parameter name, e.g., "id", "projet_id", etc.
                        $row['url'] = str_replace(':' . $paramName, $model->$paramName, $row['url']);
                        //trace_log($row['url']);
                    }
                }
            }

            $this->vars['breadcrumb'] = $breadcrumb;
            //trace_log($breadcrumb);
            return $this->makePartial('breadcrumb');
        } else {
            return '';
        }
    }

    public function renderToolBar($secondaryLabel = false)
    {
        $this->prepareComonVars(null);
        $toolBar = null;
        $toolBar = $this->config->controllerConfig['index'] ?? false;
        if (!$toolBar) {
            return;
        }
        $base = $toolBar['base'] ?? false;
        if ($base) {
            $base = $this->getPermissions($base);
        }
        foreach ($base as $key => $btn) {
            $base[$key]['url'] = $base[$key]['url'] ?? $this->config->backendUrl . '/' . $key;
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
                $permissionGranted = $this->user->hasAccess($permission, false);
            }
            //trace_log($btn);
            $btn['permissions']  = $permissionGranted;
            $btnWithPermission[$key] = $btn;
        }
        return $btnWithPermission;
    }

    public function renderCallOut()
    {
        $hints = Event::fire('controller.wakacontroller.get_call_out', [$this->model]);
        //C'est la reception d'un evenement on peut potentiellement en recevoir plusieurs, je prend le premier. 
        if ($hint = $hints[0] ?? false) {
            $this->vars['hint_title'] = $hint['title'] ?? 'Info';
            $this->vars['hint_content'] = \Lang::get($hint['content']);
            $this->vars['hint_type'] = $hint['type'] ?? 'info';
            return $this->makePartial('callout');
        } else {
            return null;
        }
    }
}
