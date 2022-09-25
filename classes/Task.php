<?php

namespace taskforce;

class Task 
{
    const STATUS_NEW = 'new';                 // новое
    const STATUS_СANCELLED = 'cancelled';     // отменено
    const STATUS_IN_PROGRESS = 'in_progress'; // в работе
    const STATUS_DONE = 'done';               // выполнено
    const STATUS_FAILED = 'failed';           // провалено

    const ACTION_CANCEL = 'cancel';       // отменить
    const ACTION_RESPOND = 'respond';     // откликнуться
    const ACTION_DONE = 'done';           // выполнено
    const ACTION_REFUSE = 'refuse';       // отказаться

    private ?int $performerId;    // исполнитель
    private int $clientId;        // заказчик

    private $status;  

    /**
     * @param null|int $performerId
     * @param int $clientId
     */
    public function __construct(int $clientId, ?int $performerId = null)
    {
        $this->performerId = $performerId;
        $this->clientId = $clientId;
        $this->status = self::STATUS_NEW;
    }

    /** 
     * Возвращает карту статусов
     * 
     * @return array  
     */ 
    public static function getMapStatus(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_СANCELLED => 'Отменено',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_DONE => 'Выполнено',
            self::STATUS_FAILED => 'Провалено'
        ];
    }

    /**
     * Возвращает карту действий
     * 
     * @return array  
     */ 
    public static function getMapAction(): array
    {
        return [
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_RESPOND => 'Откликнуться',
            self::ACTION_DONE => 'Выполнено',
            self::ACTION_REFUSE => 'Отказаться'
        ];
    }

    /**
     * Получение статуса, в который перейдёт задача после выполнения указанного действия
     * 
     * @param string $action
     * @return null|string
     */
    public function getNextStatus(string $action): ?string
    {
        $map = [
            self::ACTION_CANCEL => self::STATUS_СANCELLED, 
            self::ACTION_RESPOND => self::STATUS_IN_PROGRESS,
            self::ACTION_DONE => self::STATUS_DONE,
            self::ACTION_REFUSE => self::STATUS_FAILED    // ??            
        ];

        return $map[$action] ?? null;
    }

    private function setStatus(string $newStatus): void
    {
        if (static::getMapStatus()[$newStatus] ?? false){
            $this->status = $newStatus;
        }
    }

    /**
     * Возвращает действия, доступные для указанного статуса
     * 
     * @param string $status
     * @return null|array
     */
    public function getAllowedActions(string $status): ?array
    {
        $map = [
            self::STATUS_IN_PROGRESS => [self::ACTION_complete, self::ACTION_REFUSE],
            self::STATUS_NEW => [CancelAction::class, self::ACTION_RESPOND]
        ]; 
        
        return $map[$status] ?? null;
    }

    public function getAvailableActions(string $role, int $id)
    {
        $statusActions = $this->statusAllowedAction()[$this->status];
        $roleActions = $this->roleAllowedActions()[$role];

        $allowedActions = array_uintersect($statusActions, $roleActions);

        $allowedActions = array_filter($allowedActions, function ($action) use ($id) {
            return $action::checkRights($id, $this->performerId, $this->clientId);
        });

        return array_values($allowedActions);
    }

    public function roleAllowedActions()
    {
        $map = [
            self::ROLE_CLIENT => [CancelAction::class, DoneAction::class],
            self::ROLE_PERFORMER => [респонсе, дени]
        ];

        return $map;
    }
}