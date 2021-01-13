<?php

function task_curia_scheduler(array $task): void
{
    global $lang;

    $lang->load('curia');

    if (function_exists(('\curia\runScheduler'))) {
        \curia\runScheduler();

        add_task_log($task, $lang->curia_scheduler_task_ran);
    }
}
