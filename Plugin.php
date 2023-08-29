<?php

namespace Kanboard\Plugin\DefinitionOfDone;

use Kanboard\Core\Plugin\Base;
use Kanboard\Plugin\DefinitionOfDone\Controller\DefinitionOfDoneController;

class Plugin extends Base
{
    private $dodtemplate = "";

    public function initialize()
    {
        $this->template->setTemplateOverride('event/DefinitionOfDone_create', 'DefinitionOfDone:Event/create');
        $this->template->setTemplateOverride('event/DefinitionOfDone_delete', 'DefinitionOfDone:Event/delete');
        $this->template->setTemplateOverride('event/DefinitionOfDone_toggle', 'DefinitionOfDone:Event/toggle');
        $this->template->setTemplateOverride('event/DefinitionOfDone_update', 'DefinitionOfDone:Event/update');

        $this->template->hook->attach('template:task:show:before-subtasks', 'DefinitionOfDone:DefinitionOfDone/show');

        $this->template->hook->attach('template:task:form:first-column', 'DefinitionOfDone:DefinitionOfDone/creation');

        $container = $this->container;

        $this->hook->on('model:task:creation:prepare', function (&$values) use ($container) {
            if (isset($values['dod-templates'])) {
                $container->dodtemplate = $values['dod-templates'];
                unset($values['dod-templates']);
            }
        });

        $this->hook->on('model:task:creation:aftersave', function ($task_id) use ($container) {
            if ($container->dodtemplate == "") {
                return;
            }
            $controller = new DefinitionOfDoneController($container);
            $controller->loadTemplateInternal($container->dodtemplate, $task_id);
        });

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
        return '1.1.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/Chaosmeister/DefinitionOfDone';
    }
}
