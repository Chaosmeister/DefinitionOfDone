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