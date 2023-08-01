<?php

namespace Kanboard\Plugin\DefinitionOfDone;

use Kanboard\Core\Plugin\Base;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach("template:task:show:before-subtasks", "DefinitionOfDone:DefinitionOfDone/show");

        $this->hook->on('template:layout:js', array('template' => 'plugins/DefinitionOfDone/Assets/js/functions.js'));
        $this->hook->on('template:layout:css', array('template' => 'plugins/DefinitionOfDone/Assets/css/result.css'));

        $this->hook->on('model:task:project_duplication:aftersave', function ($hook_values) {
            $this->definitionOfDoneModel->copyAll($hook_values['source_task_id'], $hook_values['destination_task_id']);
        });
    }

    public function getPluginName()
    {
        return 'DefinitionOfDone';
    }

    public function getClasses()
    {
        return array(
            'Plugin\DefinitionOfDone\Model' => array(
                'DefinitionOfDoneModel'
            ),
            'Plugin\DefinitionOfDone\Controller' => array(
                'DefinitionOfDoneController'
            )
        );
    }

    public function getPluginDescription()
    {
        return 'Add a Definition-Of-Done system - extended subtasks';
    }

    public function getPluginAuthor()
    {
        return 'Tomas Dittmann';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/Chaosmeister/DefinitionOfDone';
    }
}
