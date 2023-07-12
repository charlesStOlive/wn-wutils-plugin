<?php namespace Waka\Wutils\Behaviors;

use Backend\Classes\ControllerBehavior;
use Session;

class BtnsBehavior extends ControllerBehavior
{
    /**
     * @var array Configuration values that must exist when applying the primary config file.
     */
    protected $requiredConfig = ['modelClass'];

    /**
     * @var mixed Configuration for this behaviour
     */
    public $btnsConfig = 'config_waka.yaml';

    public $config;
    public $btnsWidget;
    public $workflowWidget;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->btnsWidget = new \Waka\Wutils\Widgets\Btns($controller);
        $this->btnsWidget->alias = 'btnsWidget';
        $this->controller = $controller;
        $this->btnsWidget->model = $controller->formGetModel();
        trace_log($controller->btnsConfig ?: $this->btnsConfig);
        $this->btnsWidget->config = $this->makeConfig($controller->btnsConfig ?: $this->btnsConfig, $this->requiredConfig);
        $this->btnsWidget->bindToController();
    }
}
