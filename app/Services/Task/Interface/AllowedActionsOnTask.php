<?php

namespace App\Services\Task\Interface;

interface AllowedActionsOnTask
{
    public const RESPOND = 'respond';
    public const COMPLETE = 'complete';
    public const REFUSE = 'refuse';
    public const CANCEL = 'cancel';

}
