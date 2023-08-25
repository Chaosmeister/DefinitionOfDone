<details class="accordion-section dod-main" open>
    <summary class="accordion-title"><?= t('Definition Of Done') ?>
        <?= $this->helper->url->icon('upload', '', 'DefinitionOfDoneController', 'import', array('task_id' => $task['id'], 'plugin' => 'DefinitionOfDone'), false, 'dod-import') ?>
        <?= $this->helper->url->icon('download', '', 'DefinitionOfDoneController', 'export', array('task_id' => $task['id'], 'plugin' => 'DefinitionOfDone'), false, 'dod-export') ?>
        <?= $this->app->definitionOfDoneController->getTemplates($task['id']) ?>
    </summary>
    <div class="accordion-content">
        <table class="dod-table table-striped table-scrolling" data-save-position-url="<?= $this->url->href('DefinitionOfDoneController', 'move', array('task_id' => $task['id'], "plugin" => "DefinitionOfDone")) ?>">
            <thead>
                <tr>
                    <th><?= t('Options') ?></th>
                    <th><?= t('Done') ?></th>
                    <th><?= t('Title') ?></th>
                    <th><?= t('Description') ?></th>
                </tr>
            </thead>
            <tbody>
                <?= $this->app->definitionOfDoneController->rows($task['id']); ?>
            </tbody>
        </table>
    </div>
</details>