<?php

namespace Kanboard\Plugin\DefinitionOfDone\Model;

use Kanboard\Core\Base;
use Kanboard\Plugin\DefinitionOfDoneEventJob;

class DefinitionOfDoneModel extends Base
{
    protected function table()
    {
        return $this->db->table("definition_of_done");
    }

    protected function CreateEvent($event)
    {
        $table = $this->db->table("project_activities");

        $table->insert($event);
    }

    public function getById($Id)
    {
        return $this->table()->eq('id', $Id)->findOne();
    }

    public function getAll($taskId)
    {
        return $this->table()->eq('task_id', $taskId)->orderBy("position")->findAll();
    }

    public function saveMultiple($entries)
    {
        $this->db->startTransaction();

        foreach ($entries as $entry) {
            $this->save($entry);
        }

        $this->db->closeTransaction();
    }

    public function copyAll($source, $target)
    {
        $this->db->startTransaction();
        $entries = $this->getAll($source);

        foreach ($entries as $entry) {
            $entry['task_id'] = $target;
            $this->save($entry);
        }

        $this->db->closeTransaction();
    }

    public function save($entry)
    {
        if (isset($entry['id'])) {
            $dbEntry = $this->table()->eq('id', $entry['id']);
            if ($dbEntry->exists()) {
                $dbEntry->update($entry);
                return;
            }
        }

        $this->table()->insert($entry);

        $projectId = $this->taskFinderModel->getProjectId($entry['task_id']);
        $userId = $this->userSession->getId();
        $this->CreateEvent(array(
            'event_name' => 'DefinitionOfDone.create',
            'date_creation' => time(),
            'creator_id' => $userId,
            'task_id' => $entry['task_id'],
            'project_id' => $projectId,
            'data' => json_encode(array('title' => $entry['title'], 'text' => $entry['text']))
        ));
    }

    public function copy($sourceId, $destinationId)
    {
        $text = $this->getById($sourceId);
        if (isset($text)) {
            $this->save($destinationId, $text);
        }
    }

    public function delete($dodIds)
    {
        $this->db->startTransaction();

        foreach ($dodIds["ids"] as $dodId) {
            $Entry = $this->table()->eq('id', $dodId);

            $values = $Entry->findOne();
            $size = $this->table()->eq('task_id', $values["task_id"])->count();

            $this->move($values["task_id"], $dodId, $size);

            if (!$Entry->remove()) {
                $this->db->cancelTransaction();
                return;
            }
        }

        $this->db->closeTransaction();
    }

    public function move($task_id, int $dod_id, $position)
    {
        if ($position < 1 || $position > $this->table()->eq('task_id', $task_id)->count()) {
            return false;
        }

        $dod_ids = $this->table()->eq('task_id', $task_id)->neq('id', $dod_id)->asc('position')->findAllByColumn('id');
        $offset = 1;
        $results = array();

        foreach ($dod_ids as $current_id) {
            if ($offset == $position) {
                $offset++;
            }

            $results[] = $this->table()->eq('id', $current_id)->update(array('position' => $offset));
            $offset++;
        }

        $results[] = $this->table()->eq('id', $dod_id)->update(array('position' => $position));

        return !in_array(false, $results, true);
    }
}
