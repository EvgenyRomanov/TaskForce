<?php

namespace taskforce;

class DoneAction extends AbstractAction
{
    public static function getLabel()
    {
        return Task::getMapAction()[static::getInternalName()];
    }

    public static function getInternalName()
    {
        return Task::ACTION_DONE;
    }

    public static function checkRights($userId, $performerId, $clientId)
    {
        return $performerId == $userId;
    }
}