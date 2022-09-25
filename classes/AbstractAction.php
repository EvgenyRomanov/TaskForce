<?php

namespace taskforce;

abstract class AbstractAction
{
    /**
     * Метод для возврата названия
     */ 
    abstract public static function getLabel();

    /**
     * Метод для возврата внутреннего имени
     */
    abstract public static function getInternalName();

    
    /**
     * Метод для проверки прав
     */
    abstract public static function checkRights($userId, $performerId, $clientId);
}