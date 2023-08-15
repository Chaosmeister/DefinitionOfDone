<p class="activity-title">
    <?= e(
        '%s toggled a definition of done for the task %s',
        $this->text->e($author),
        $this->url->link(t('#%d', $task_id), 'TaskViewController', 'show', array('task_id' => $task_id))
    ) ?>
    <small class="activity-date"><?= $this->dt->datetime($date_creation) ?></small>
</p>
<div class="activity-description">
    <p class="activity-task-title"><strong>Title: </strong><?= $this->text->e($title) ?></p>
    <p>
        <strong>New status: </strong>
        <?php if ($status == null) : ?>
            not finished
        <?php else : ?>
            finished
        <?php endif ?>
    </p>
</div>