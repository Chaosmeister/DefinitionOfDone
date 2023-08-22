<?php

namespace Kanboard\Plugin\DefinitionOfDone\Controller;

use Kanboard\Controller\BaseController;

function isEmpty($variable)
{
    $empty = empty($variable);
    if ($empty) {
        return $variable == "";
    }
    return false;
}

class DefinitionOfDoneController extends BaseController
{
    public function save()
    {
        $values = $this->request->getJson();
        $user = $this->getUser();

        $position = 1;
        if (empty($values)) {
            $this->response->status(422);
        }

        $task_id = $this->request->getIntegerParam('task_id');
        if (!empty($values['task_id'])) {

            $task_id = $values['task_id'];
        }

        foreach ($values["entries"] as $entry) {

            if (sizeof($entry) > 1 && isEmpty($entry['title'])) {
                // invalid entry, skip
                continue;
            }

            $entry["task_id"] = $task_id;

            if (isset($entry['title']) || isset($entry['text'])) {
                $entry['user_id'] = $user['id'];
            }

            $entry['position'] = $position;
            $position++;
            $this->definitionOfDoneModel->save($entry, false);
        }

        $this->response->html($this->rows($task_id));
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
        $style = '';
        $class = '';
        if ($dod['text'] == "=====") { // separator
            $style = ' style="background-color: rgb(0,0,0,0.1);"';
            $class = ' dod-separator';
        }

        $html = '<tr class="dod' . $class . '" dodId="' . $dod['id'] . '"' . $style . '>';
        $html .= '<td>';
        $html .= '<div class="dod-options">';
        $html .= '<div class="fa fa-arrows-alt dod-draggable-row-handle" title="' . t('Change position') . '" role="button" title="' . t('Change position') . '"></div>';
        $html .= '<div class="fa fa-fw fa-square-o button dodSelect" title="' . t('Select row for deletion') . '"></div>';
        $html .= '<div class="fa fa-fw fa-trash button dodTrash" taskid="' . $task_id . '" title="' . t('Delete selected rows') . '"></div>';
        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew', t('Add row'));
        $html .= $this->helper->url->icon('edit', '', 'DefinitionOfDoneController', 'edit', array('task_id' => $task_id, 'dod_id' => $dod['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodEdit', t('Edit row'));
        $html .= '</div>';
        $html .= '</td>';

        if ($dod['text'] == "=====") { // separator
            $html .= '<td colspan=5 class="button dod-separator-button"><div style="display: flex; align-items: center;"><div class="fa fa-fw fa-compress dod-separator-icon "></div><h1 style="padding-left: 20px">';
            $html .= $dod['title'];
            $html .= '</h1></div></td>';
        } else { // normal line
            $html .= '<td class="dodStatus">';
            $status = 'square-o';
            if ($dod['status'] != 0) {
                $status = 'check-' . $status;
            }
            $html .= $this->helper->url->icon($status, '', 'DefinitionOfDoneController', 'toggle', array('dod_id' => $dod['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodStateToggle', t('Toggle state'));
            $html .= '</td>';
            $html .= '<td class="dodTitle">';
            $html .= $dod['title'];
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
        $html .= '<td>';
        $html .= '<div class="dod-options">';
        $html .= '<div class="fa fa-arrows-alt dod-draggable-row-handle" title="' . t('Change position') . '" role="button" aria-label="' . t('Change position') . '"></div>';
        $html .= '<div class="fa fa-fw fa-save button dodSave" taskid="' . $task_id . '"></div>';

        if (isset($dod)) {
            $html .= '<div class="fa fa-fw fa-times button editdodTrash" title="' . t('Close row') . '"></div>';
        } else {
            $html .= '<div class="fa fa-fw fa-times button newdodTrash" title="' . t('Close row') . '"></div>';
        }

        $html .= $this->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'getnewrow', array('task_id' => $task_id, 'plugin' => 'DefinitionOfDone'), false, 'dodNew', 'Add row');
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
        $html .= '</div>';
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

    public function toggle()
    {
        $dod_id = $this->request->getIntegerParam('dod_id');
        $user = $this->getUser();

        $entry = $this->definitionOfDoneModel->getById($dod_id);

        if ($entry['status'] == 0) {
            $entry['status'] = 1;
        } else {
            $entry['status'] = 0;
        }

        $entry['user_id'] = $user['id'];

        $this->definitionOfDoneModel->save($entry, true);
    }

    public function export()
    {
        $task_id = $this->request->getIntegerParam('task_id');

        $export = array('entries' => array());

        $dods = $this->definitionOfDoneModel->getAll($task_id);

        foreach ($dods as $dod) {
            array_push($export['entries'], array('title' => $dod['title'], 'text' => $dod['text']));
        }

        if (empty($export['entries'])) {
            $this->response->status(422);
        } else {
            $this->response->json($export);
        }
    }
}
