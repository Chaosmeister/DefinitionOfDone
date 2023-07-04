<?php

namespace Kanboard\Plugin\DefinitionOfDone\Model;

use Kanboard\Core\Base;

class DefinitionOfDoneModel extends Base
{
    protected function getTable()
    {
        return "definition_of_done";
    }

    public function getById($Id)
    {
        $result = $this->db->table($this->getTable())->eq('id', $Id)->findOne();
        if (isset($result['text'])) {
            return $result;
        }
    }

    public function getAllById($taskId)
    {
        return $this->db->table($this->getTable())->eq('task_id', $taskId)->orderBy("position")->findAll();
    }

    public function saveMultiple($entries)
    {
        $this->db->startTransaction();

        foreach ($entries as $entry) {
            $this->save($entry);
        }

        $this->db->closeTransaction();
    }

    public function save($entry)
    {
        if ($this->GetById($entry['id']) == "") {
            $this->db->table($this->getTable())->insert(
                array(
                    'id' => $entry['id'],
                    'title' => $entry['title'],
                    'status' => $entry['status'],
                    'task_id' => $entry['task_id'],
                    'user_id' => $entry['user_id'],
                    'text' => $entry['text'],
                    'position' => $entry['position'],
                )
            );
        } else {
            $this->db->table($this->getTable())->eq('id', $entry['id'])->update(
                array(
                    'text' => $entry['text'],
                    'title' => $entry['title'],
                    'status' => $entry['status'],
                    'task_id' => $entry['task_id'],
                    'user_id' => $entry['user_id'],
                    'text' => $entry['text'],
                    'position' => $entry['position'],
                )
            );
        }
    }

    public function copy($sourceId, $destinationId)
    {
        $text = $this->getById($sourceId);
        if (isset($text)) {
            $this->save($destinationId, $text);
        }
    }

    public function delete($entries)
    {
        foreach ($entries as $entry) {
            $this->db->table($this->getTable())->eq('id', $entry)->remove();
        }
    }

    public function move($task_id, $dod_id, $position)
    {
        if ($position < 1 || $position > $this->db->table($this->getTable())->eq('task_id', $task_id)->count()) {
            return false;
        }

        $dod_ids = $this->db->table($this->getTable())->eq('task_id', $task_id)->neq('id', $dod_id)->asc('position')->findAllByColumn('id');
        $offset = 1;
        $results = array();

        foreach ($dod_ids as $current_subtask_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->db->table($this->getTable())->eq('id', $current_subtask_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->db->table($this->getTable())->eq('id', $dod_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }
}
