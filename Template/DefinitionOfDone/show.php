<details class="accordion-section" open>
    <summary class="accordion-title"><?= t('Definition Of Done') ?></summary>
    <div class="accordion-content">
        <table class="dod-table table-striped table-scrolling" data-save-position-url="<?= $this->url->href('DefinitionOfDoneController', 'move', array('task_id' => $task['id'], "plugin" => "DefinitionOfDone")) ?>">
            <thead>
                <tr>
                    <th class=""><?= t('Options') ?></th>
                    <th class=""><?= t('Status') ?></th>
                    <th><?= t('Title') ?></th>
                    <th class=""><?= t('Assignee') ?></th>
                    <th class=""><?= t('Description') ?></th>
                    <th class=""><?= t('Time tracking') ?></th>
                </tr>
            </thead>
            <tbody>
                <?= $this->app->definitionOfDoneController->rows($task['id']); ?>
            </tbody>
        </table>
    </div>
</details>