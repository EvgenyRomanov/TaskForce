<?php

namespace taskforce;

class RespondAction extends AbstractAction
{
    public static function getLabel()
    {
        return Task::getMapAction()[static::getInternalName()];
    }

    public static function getInternalName()
    {
        Task::ACTION_RESPOND;
    }

    public static function checkRights($userId, $performerId, $clientId)
    {
        return $userId == $clientId;
    }    
}