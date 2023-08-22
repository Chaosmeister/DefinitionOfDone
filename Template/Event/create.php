<p class="activity-title">
    <?= e(
        '%s added a definition of done for the task %s',
        $this->text->e($author),
        $this->url->link(t('#%d', $task_id), 'TaskViewController', 'show', array('task_id' => $task_id))
    ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
<p class="activity-task-title"><strong>Title: </strong><?= $this->text->e($title) ?></p>
    <p>
        <?php if (empty($text) && $text == "") : ?>
            <strong>No Text</strong>
        <?php else : ?>
            <strong>Text: </strong> <?= $text ?>
        <?php endif ?>
    </p>
</div>