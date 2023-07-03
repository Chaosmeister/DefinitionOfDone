<details class="accordion-section" open>
    <summary class="accordion-title"><?= t('Definition Of Done') ?></summary>
    <div class="accordion-content">
        <table class="DoD-table table-striped table-scrolling" data-save-position-url="<?= $this->url->href('DefinitionOfDoneController', 'movePosition', array('task_id' => $task['id'])) ?>">
            <thead>
                <tr>
                    <th class="column-10"><?= t('Options') ?></th>
                    <th class="column-1"><?= t('Status') ?></th>
                    <th><?= t('Title') ?></th>
                    <th class="column-10"><?= t('Assignee') ?></th>
                    <th class="column-30"><?= t('Description') ?></th>
                    <th class="column-30"><?= t('Time tracking') ?></th>
                </tr>
            </thead>
            <tbody>
                <?= $this->app->definitionOfDoneController->rows($task['id']); ?>
                <tr>
                    <td class="DoDOptions">
                        <?= $this->app->helper->url->icon('plus', '', 'DefinitionOfDoneController', 'newrow', array('task_id' => $task['id'], 'plugin' => 'DefinitionOfDone'), false, 'dodNew'); ?>
                    </td>
                    <td class="DoDStatus">
                    </td>
                    <td class="DoDTitle">
                    </td>
                    <td class="DoDAssignee">
                    </td>
                    <td class="DoDdescription">
                    </td>
                    <td class="DoDTimer">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</details>