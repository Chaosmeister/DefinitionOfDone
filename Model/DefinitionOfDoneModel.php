<?php

namespace Kanboard\Plugin\DefinitionOfDone\Model;

use Kanboard\Core\Base;

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

    public function getById($id)
    {
        return $this->table()->eq('id', $id)->findOne();
    }

    public function getAll($task_id)
    {
        return $this->table()->eq('task_id', $task_id)->orderBy("position")->findAll();
    }

    public function saveMultiple($entries)
    {
        $this->db->startTransaction();

        foreach ($entries as $entry) {
            $this->save($entry, false);
        }

        $this->db->closeTransaction();
    }

    public function copyAll($source_id, $destination_id)
    {
        $this->db->startTransaction();
        $entries = $this->getAll($source_id);

        foreach ($entries as $entry) {
            $entry['task_id'] = $destination_id;
            unset($entry['id']);
            $this->save($entry, false);
        }

        $this->db->closeTransaction();
    }

    public function save($entry, $toggle = false)
    {
        $project_id = $this->taskFinderModel->getProjectId($entry['task_id']);
        $user_id = $this->userSession->getId();

        // update an existing entry
        if (isset($entry['id'])) {
            $dbEntry = $this->table()->eq('id', $entry['id']);
            if ($dbEntry->exists()) {
                $dbEntry->update($entry);

                if ($toggle) {
                    $this->CreateEvent(array(
                        'event_name' => 'DefinitionOfDone.toggle',
                        'date_creation' => time(),
                        'creator_id' => $user_id,
                        'task_id' => $entry['task_id'],
                        'project_id' => $project_id,
                        'data' => json_encode(array(
                            'title' => $entry['title'],
                            'status' => $entry['status'],
                        )),
                    ));
                } else if (
                    isset($entry['title']) &&
                    isset($entry['text'])
                ) {
                    $this->CreateEvent(array(
                        'event_name' => 'DefinitionOfDone.update',
                        'date_creation' => time(),
                        'creator_id' => $user_id,
                        'task_id' => $entry['task_id'],
                        'project_id' => $project_id,
                        'data' => json_encode(array(
                            'title' => $entry['title'],
                            'text' => $entry['text'],
                        )),
                    ));
                }
                return;
            }
        }

        // create a new entry
        $this->table()->insert($entry);

        $this->CreateEvent(array(
            'event_name' => 'DefinitionOfDone.create',
            'date_creation' => time(),
            'creator_id' => $user_id,
            'task_id' => $entry['task_id'],
            'project_id' => $project_id,
            'data' => json_encode(array(
                'title' => $entry['title'],
                'text' => $entry['text'],
            )),
        ));
    }

    public function copy($source_id, $destination_id)
    {
        $text = $this->getById($source_id);
        if (isset($text)) {
            $this->save($destination_id, $text);
        }
    }

    public function clear($task_id)
    {
        $this->table()->eq('task_id', $task_id)->remove();
    }

    public function delete($entries)
    {
        $this->db->startTransaction();

        $task_id = $entries['task_id'];
        $size = $this->table()->eq('task_id', $task_id)->count();

        $deleted_Entries = array();

        foreach ($entries['ids'] as $dod_id) {
            $Entry = $this->table()->eq('id', $dod_id);

            // move it to the end of the table
            $this->move($task_id, $dod_id, $size);
            $size--;

            // finally remove it.
            if (!$Entry->remove()) {
                $this->db->cancelTransaction();
                return;
            }
        }

        $this->db->closeTransaction();

        $project_id = $this->taskFinderModel->getProjectId($task_id);
        $user_id = $this->userSession->getId();
        $this->CreateEvent(array(
            'event_name' => 'DefinitionOfDone.delete',
            'date_creation' => time(),
            'creator_id' => $user_id,
            'task_id' => $task_id,
            'project_id' => $project_id,
            'data' => json_encode(array('deleted_entries' => $deleted_Entries)),
        ));
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

    public function collectReleaseNotes()
    {
        $result = array();

        $tasks = $this->db->table("tasks")->eq('column_id', 8)->asc('position')->findAllByColumn('id');

        $counter = 0;
        foreach ($tasks as $task) {
            $dod = $this->getAll($task);
            array_push($result, array('task' => $task, 'text' => 'NA'));

            foreach ($dod as $entry) {
                if ($entry['title'] == 'Release Note verfassen (Englisch)') {
                    $result[$counter]['text'] = $entry['text'];
                    break;
                }
            }
            $counter++;
        }

        return array_values($result);
    }
}
