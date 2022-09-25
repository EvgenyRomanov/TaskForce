<?php

namespace taskforce;

class RefuseAction extends AbstractAction
{
    public static function getLabel()
    {
        return Task::getMapAction()[static::getInternalName()];
    }

    public static function getInternalName()
    {
        return Task::ACTION_REFUSE;
    }

    public static function checkRights($userId, $performerId, $clientId)
    {
        return $performerId == $userId;
    }
}    