<?php

namespace Kanboard\Plugin\DefinitionOfDone\Controller;

use Kanboard\Controller\BaseController;

class DefinitionOfDoneController extends BaseController
{
    public function save()
    {
        $values = $this->request->getJson();

        if (!empty($values) && !empty($values['task_id'])) {
            $task_id = $values['task_id'];

            foreach ($values["entries"] as $entry) {
                $entry["task_id"] = $task_id;
                $this->definitionOfDoneModel->save($entry);
            }

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)));
        }
    }

    public function edit()
    {
        $dod_id = $this->request->getIntegerParam('dod_id');

        $this->response->html($this->newrow($this->definitionOfDoneModel->getById($dod_id)));
    }

    public function trash()
    {
        $ids = $this->request->getJson();

        $this->definitionOfDoneModel->delete($ids);

        $this->response->status(200);
    }

    public function rows($task_id)
    {
        $dods = $this->definitionOfDoneModel->getAllById($task_id);
        $html = "";
        foreach ($dods as $dod) {
            $html .= $this->row($dod, $task_id);
        }

        $hidden = true;
        if ($html == "") {
            $hidden = false;
        }

        $html .= '<tr class="newdodrow"';
        if ($hidden) {
            $html .= ' hidden';
        }
        $html .= '>';
        $html .= '<td colspan=99>';
        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew');
        $html .= '</td>';
        $html .= '<tr>';

        return $html;
    }

    private function row($dod, $task_id)
    {
        $html = '<tr class="dod" dodId="' . $dod['id'] . '">';
        $html .= '<td class="dodOptions">';
        $html .= '<i class="fa fa-arrows-alt dod-draggable-row-handle" title="' . t('Change position') . '" role="button" aria-label="' . t('Change position') . '"></i>';
        $html .= '<i class="fa fa-fw fa-square-o button dodSelect"></i>';
        $html .= '<i class="fa fa-fw fa-trash button dodTrash"></i>';
        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew');
        $html .= $this->helper->url->icon('edit', '', 'DefinitionOfDoneController', 'edit', array('task_id' => $task_id, 'dod_id' => $dod['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodEdit');
        $html .= '</td>';

        if ($dod['text'] == "=====") {
            $html .= '<td colspan=5>';
            $html .= $dod['title'];
            $html .= '</td>';
        } else {
            $html .= '<td class="dodStatus">';
            if ($dod['status'] == 0) {
                $html .= $this->helper->url->icon('play', '', 'DefinitionOfDoneController', 'start', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodStart');
            }
            if ($dod['status'] == 1) {
                $html .= $this->helper->url->icon('gears', '', 'DefinitionOfDoneController', 'stop', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodstop');
            }
            if ($dod['status'] == 1) {
                $html .= $this->helper->url->icon('stop', '', 'DefinitionOfDoneController', 'clear', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodstop');
            }
            $html .= '</td>';
            $html .= '<td class="dodTitle">';
            $html .= $dod['title'];
            $html .= '</td>';
            $html .= '<td class="dodAssignee">';
            $html .= '</td>';
            $html .= $this->userModel->getById($dod['user_id']);
            $html .= '</td>';
            $html .= '<td class="dodText">';
            $html .= $dod['text'];
            $html .= '</td>';
            $html .= '<td class="dodTimer">';
            $html .= '</td>';
            $html .= '</tr>';
        }
        return $html;
    }

    public function getnewrow()
    {
        $this->response->html($this->newrow(null));
    }

    private function newrow($dod)
    {
        $task_id = $this->request->getIntegerParam('task_id');

        $html = '<tr class="newdod">';
        $html .= '<td class="dodOptions">';
        $html .= '<i class="fa fa-arrows-alt dod-draggable-row-handle" title="' . t('Change position') . '" role="button" aria-label="' . t('Change position') . '"></i>';
        $html .= '<i class="fa fa-fw fa-save button dodSave" taskid="' . $task_id . '"></i>';
        $html .= '<i class="fa fa-fw fa-trash button newdodTrash"></i>';
        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew');
        $html .= '</td>';
        $html .= '<td class="dodStatus">';
        $html .= '</td>';
        $html .= '<td class="dodTitle">';
        if (isset($dod["title"])) {
            $html .= '<input class="dodInput newdodTitle" value="' . $dod["title"] . '">';
        } else {
            $html .= '<input class="dodInput newdodTitle">';
        }
        $html .= '</td>';
        $html .= '<td class="dodAssignee">';
        $html .= '</td>';
        $html .= '</td>';
        $html .= '<td class="doddescription">';
        if (isset($dod["text"])) {
            $html .= '<textarea class="dodInput newdodDescription" value="' . $dod["text"] . '">';
        } else {
            $html .= '<textarea class="dodInput newdodDescription">';
        }
        $html .= '</textarea>';
        $html .= '</td>';
        $html .= '<td class="dodTimer">';
        $html .= '</td>';
        $html .= '</tr>';

        return $html;
    }

    public function move()
    {
        $values = $this->request->getJson();
        $task_id = $this->request->getIntegerParam('task_id');

        if (!$this->definitionOfDoneModel->move($task_id, $values['dod_id'], $values['position'])) {
            $this->response->status(400);
        }
        $this->response->status(200);
    }

    public function Access()
    {
        $user = $this->getUser();

        if ($user['id'] == 6 || $user['id'] == 2)
        {
            return true;
        }
        return false;
    }
}
