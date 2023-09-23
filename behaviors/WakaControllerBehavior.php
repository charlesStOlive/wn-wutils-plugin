<?php namespace Waka\Wutils\Behaviors;

use Backend\Classes\ControllerBehavior;
use Session;

class WakaControllerBehavior extends ControllerBehavior
{
    /**
     * @var array Configuration values that must exist when applying the primary config file.
     */
    protected $requiredConfig = ['modelClass', 'backendUrl', 'controllerConfig'];

    /**
     * @var mixed Configuration for this behaviour
     */
    public $wakaControllerConfig = 'config_waka.yaml';

    public $config;
    public $wakaController;

    public function __construct($controller)
    {
        parent::__construct($controller);
        $this->wakaController = new \Waka\Wutils\Widgets\WakaController($controller);
        $this->wakaController->alias = 'wakaController';
        $this->controller = $controller;
        $this->wakaController->model = $controller->formGetModel();
        //trace_log($controller->btnsConfig ?: $this->btnsConfig);
        $this->wakaController->config = $this->makeConfig($controller->wakaControllerConfig ?: $this->wakaControllerConfig, $this->requiredConfig);
        $this->wakaController->bindToController();
    }
}
