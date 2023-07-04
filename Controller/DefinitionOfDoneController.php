<?php

namespace Kanboard\Plugin\DefinitionOfDone\Controller;

use Kanboard\Controller\BaseController;

class DefinitionOfDoneController extends BaseController
{
    public function get()
    {
        $dodId = $this->request->getIntegerParam("Id");
        if (isset($dodId)) {
            $this->response->text($this->definitionOfDoneModel->getById($dodId));
        }
    }

    public function save()
    {
        $values = $this->request->getJson();

        if (isset($values) && !empty($values['task_id'])) {
            $task_id = $values['task_id'];
            $entries = array();

            $newdods = $values['newdods'];
            if (isset($newdods)) {
                foreach ($newdods as $newdod)
                    array_push(
                        $entries,
                        array(
                            'id' => null,
                            'title' => $newdod['title'],
                            'status' => 0,
                            'task_id' => $task_id,
                            'user_id' => null,
                            'text' => $newdod['text'],
                            'position' => null,
                        )
                    );
            }

            $dodEdits = $values['dodEdits'];
            if (isset($dodEdits)) {

                foreach ($dodEdits as $dodEdit)
                    array_push(
                        $entries,
                        array(
                            'id' => $dodEdit['id'],
                            'title' => $dodEdit['title'],
                            'status' => 0,
                            'task_id' => $task_id,
                            'user_id' => null,
                            'text' => $dodEdit['text'],
                            'position' => null,
                        )
                    );
            }

            $this->definitionOfDoneModel->saveMultiple($entries);

            $this->response->redirect($this->helper->url->to('TaskViewController', 'show', array('task_id' => $task_id)));
        }
    }

    public function edit()
    {
        $dod_id = $this->request->getIntegerParam('dod_id');
    }

    public function trash()
    {
        $ids = $this->request->getJson();

        $this->definitionOfDoneModel->delete($ids);

        $this->response->html("");
    }

    public function rows($task_id)
    {
        $dods = $this->definitionOfDoneModel->getAllById($task_id);
        $html = "";
        foreach ($dods as $dod) {
            $html .= '<tr class="dod" dodId="' . $dod['id'] . '">';
            $html .= '<td class="dodOptions">';
            $html .= '<i class="fa fa-arrows-alt draggable-row-handle" title="' . t('Change position') . '" role="button" aria-label="' . t('Change position') . '"></i>';
            $html .= '<i class="fa fa-fw fa-square-o button dodSelect"></i>';
            $html .= '<i class="fa fa-fw fa-trash button dodTrash"></i>';
            $html .= '<i class="fa fa-fw fa-edit button dodEdit"></i>';
            $html .= '</td>';

            if ($dod['text'] == "=====") {
                $html .= '<td colspan=5>';
                $html .= $dod['title'];
                $html .= '</td>';
            } else {
                $html .= '<td class="dodStatus">';
                if ($dod['status'] == 0) {
                    $html .= '<i class="fa fa-play button dodStart"></i>';
                }
                $html .= '</td>';
                $html .= '<td class="dodTitle">';
                $html .= $dod['title'];
                $html .= '</td>';
                $html .= '<td class="dodAssignee">';
                $html .= '</td>';
                $html .= $this->userModel->getById($dod['user_id']);
                $html .= '</td>';
                $html .= '<td class="doddescription">';
                $html .= $dod['text'];
                $html .= '</td>';
                $html .= '<td class="dodTimer">';
                $html .= '</td>';
                $html .= '</tr>';
            }
        }
        return $html;
    }

    public function newrow()
    {
        $html = '<td class="dodOptions">';
        $html .= '<i class="fa fa-fw fa-save button dodSave" id="' . $this->request->getIntegerParam('task_id') . '"></i>';
        $html .= '<i class="fa fa-fw fa-trash button newdodTrash"></i>';
        $html .= '</td>';
        $html .= '<td class="dodStatus">';
        $html .= '</td>';
        $html .= '<td class="dodTitle">';
        $html .= '<input class="newdodTitle"></input>';
        $html .= '</td>';
        $html .= '<td class="dodAssignee">';
        $html .= '</td>';
        $html .= '</td>';
        $html .= '<td class="doddescription">';
        $html .= '<input class="dodInput newdodDescription"></input>';
        $html .= '</td>';
        $html .= '<td class="dodTimer">';
        $html .= '</td>';

        $this->response->html($html);
    }

    public function move()
    {
        $values = $this->request->getJson();
        $task_id = $this->request->getIntegerParam('task_id');

        $this->definitionOfDoneModel->move($task_id, $values['dod_id'], $values['position']);
    }
}
