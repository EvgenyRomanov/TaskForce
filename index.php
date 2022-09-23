<?php

require_once 'vendor/autoload.php';

use taskforce\Task;

ini_set('assert.exception', 1);

$task = new Task(3,1);
assert($task->getNextStatus('cancel') === Task::STATUS_СANCELLED);
//assert($task->getNextStatus('cancel') === Task::STATUS_DONE);
