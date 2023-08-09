<?php

namespace Kanboard\Plugin\DefinitionOfDone\Schema;

use PDO;

const VERSION = 1;

function version_1(PDO $pdo)
{
    $pdo->exec("
    CREATE TABLE definition_of_done (
        id INT NOT NULL AUTO_INCREMENT,
        `title` TEXT,
        status INT,
        task_id INT,
        user_id INT,
        `text` TEXT,
        position INT,
        PRIMARY KEY (id),
        CONSTRAINT definition_of_done_task_id FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE ON UPDATE CASCADE,
	    CONSTRAINT definition_of_done_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    );");
}
