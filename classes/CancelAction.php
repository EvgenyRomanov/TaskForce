<?php

namespace taskforce;

class CancelAction extends AbstractAction
{
    public static function getLabel()
    {
        return Task::getMapAction()[static::getInternalName()];
    }

    public static function getInternalName()
    {
        return Task::ACTION_CANCEL;
    }

    public static function checkRights($userId, $performerId, $clientId)
    {
        return $userId == $clientId;
    }
}