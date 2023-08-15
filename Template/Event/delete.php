<?php if (count($deleted_entries) > 1) : ?>
    <p class="activity-title">
        <?= e(
            '%s deleted multiple definition of done entries for the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task_id), 'TaskViewController', 'show', array('task_id' => $task_id))
        ) ?>
        <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
    </p>
    <div class="activity-description">
        <p class="activity-task-title"><strong>Entries:</strong></p>
        <ul>
            <?php foreach ($deleted_entries as $entry) : ?>
                <li>
                    <p><strong>Title: </strong><?= $this->text->e($deleted_entries[0]['title']) ?></p>
                    <p><strong>Text: </strong><?= $this->text->markdown($deleted_entries[0]['text']) ?></p>
                    <p><strong>Assignee: </strong><?= $deleted_entries[0]['user'] ?></p>
                    <p><strong>Finished: </strong><?= ($deleted_entries[0]['status'] != null) ? 'true' : 'false' ?></p>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
<?php else : ?>
    <p class="activity-title">
        <?= e(
            '%s deleted a definition of done for the task %s',
            $this->text->e($author),
            $this->url->link(t('#%d', $task_id), 'TaskViewController', 'show', array('task_id' => $task_id))
        ) ?>
        <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
    </p>
    <div class="activity-description">
        <p class="activity-task-title"><strong>Deleted Entry:</strong></p>
        <p><strong>Title: </strong><?= $this->text->e($deleted_entries[0]['title']) ?></p>
        <p><strong>Text: </strong><?= $this->text->markdown($deleted_entries[0]['text']) ?></p>
        <p><strong>Assignee: </strong><?= $deleted_entries[0]['user'] ?></p>
        <p><strong>Finished: </strong><?= ($deleted_entries[0]['status'] != null) ? 'true' : 'false' ?></p>
    </div>
<?php endif ?>