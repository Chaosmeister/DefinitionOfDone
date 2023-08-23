<?php

namespace Kanboard\Plugin\DefinitionOfDone\Schema;

use PDO;

const VERSION = 2;

function version_1(PDO $pdo)
{
    $pdo->exec("
    CREATE TABLE definition_of_done (
        id INTEGER NOT NULL,
        `title` TEXT,
        status INTEGER,
        task_id INTEGER,
        user_id INTEGER,
        `text` TEXT,
        position INTEGER,
        PRIMARY KEY (id),
        CONSTRAINT definition_of_done_task_id FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE ON UPDATE CASCADE,
	    CONSTRAINT definition_of_done_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
    );");
}

function version_2(PDO $pdo) // remove obsolete user_id
{
    $pdo->exec("
    CREATE TABLE definition_of_done_new 
    (
        id INTEGER NOT NULL,
        `title` TEXT,
        status INTEGER,
        task_id INTEGER,
        `text` TEXT,
        position INTEGER,
        PRIMARY KEY (id),
        CONSTRAINT definition_of_done_task_id FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE ON UPDATE CASCADE
    );");

    $pdo->exec("
        INSERT INTO definition_of_done_new (id, `title`, status, task_id, `text`, position)
        SELECT id, `title`, status, task_id, `text`, position FROM definition_of_done
        ;");

    $pdo->exec("DROP TABLE definition_of_done;");
    $pdo->exec("ALTER TABLE definition_of_done_new RENAME TO definition_of_done;");
}
