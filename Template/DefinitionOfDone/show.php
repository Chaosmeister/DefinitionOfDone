<?php if ($this->app->definitionOfDoneController->access()) : ?>
    <details class="accordion-section dodmain" open>
        <summary class="accordion-title"><?= t('Definition Of Done') ?>
            <?= $this->helper->url->icon('upload', '', 'DefinitionOfDoneController', 'save', array('task_id' => $task['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodImport') ?>
            <?= $this->helper->url->icon('download', '', 'DefinitionOfDoneController', 'export', array('task_id' => $task['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodExport') ?>
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
<?php endif ?>