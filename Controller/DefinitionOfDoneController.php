<?php

namespace Kanboard\Plugin\DefinitionOfDone\Controller;

use Kanboard\Controller\BaseController;

class DefinitionOfDoneController extends BaseController
{
    public function save()
    {
        $values = $this->request->getJson();
        $user = $this->getUser();

        $position = 1;

        if (!empty($values) && !empty($values['task_id'])) {
            $task_id = $values['task_id'];

            foreach ($values["entries"] as $entry) {

                if (empty($entry['title']) || !is_numeric($entry['title'])) {
                    continue;
                }

                $entry["task_id"] = $task_id;

                if (isset($entry['title']) || isset($entry['text'])) {
                    $entry['user_id'] = $user['id'];
                }

                $entry['position'] = $position;
                $position++;
                $this->definitionOfDoneModel->save($entry);
            }

            $this->response->html($this->rows($task_id));
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
        $dods = $this->definitionOfDoneModel->getAll($task_id);
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
        $html .= '</tr>';

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
                $html .= $this->helper->url->icon('square-o', '', 'DefinitionOfDoneController', 'start', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodStart');
            }
            if ($dod['status'] == 1) {
                $html .= $this->helper->url->icon('gears', '', 'DefinitionOfDoneController', 'stop', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodstop');
            }
            $html .= '</td>';
            $html .= '<td class="dodTitle">';
            $html .= $dod['title'];
            $html .= '</td>';
            $html .= '<td class="dodAssignee">';
            if ($dod['user_id']) {
                $user = $this->userModel->getById($dod['user_id']);
                $html .= $user['name'];
            }
            $html .= '</td>';
            $html .= '<td class="dodText">';
            $html .= $this->helper->text->markdown($dod['text']);
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

        $html = "";
        if (isset($dod['id'])) {
            $html = '<tr class="editdod" dodId="' . $dod['id'] . '">';
        } else {
            $html = '<tr class="newdod">';
        }
        $html .= '<td class="dodOptions">';
        $html .= '<i class="fa fa-arrows-alt dod-draggable-row-handle" title="' . t('Change position') . '" role="button" aria-label="' . t('Change position') . '"></i>';
        $html .= '<i class="fa fa-fw fa-save button dodSave" taskid="' . $task_id . '"></i>';

        if (isset($dod)) {
            $html .= '<i class="fa fa-fw fa-trash button editdodTrash"></i>';
        } else {
            $html .= '<i class="fa fa-fw fa-trash button newdodTrash"></i>';
        }

        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew');
        $html .= '</td>';
        $html .= '<td class="dodStatus">';
        $html .= '</td>';
        $html .= '<td class="dodTitle">';
        $html .= '<input class="dodInput newdodTitle"';
        if (isset($dod["title"])) {
            $html .= ' value="' . $dod["title"] . '">';
        } else {
            $html .= '>';
        }
        $html .= '</td>';
        $html .= '<td class="dodAssignee">';
        $html .= '</td>';
        $html .= '</td>';
        $html .= '<td class="doddescription">';
        $html .= '<textarea class="dodInput newdodDescription">';
        if (isset($dod["text"])) {
            $html .= $dod["text"];
        }
        $html .= '</textarea>';
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

        if (isset($user['is_admin']) || $user['id'] == 6 || $user['id'] == 2) {
            return true;
        }
        return false;
    }
}
