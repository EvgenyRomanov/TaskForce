<?php

namespace taskforce;

class Task 
{
    const STATUS_NEW = 'new';             // новое
    const STATUS_СANCELLED = 'cancelled'; // отменено
    const STATUS_WORK = 'work';           // в работе
    const STATUS_DONE = 'done';           // выполнено
    const STATUS_FAILED = 'Failed';       // провалено

    const ACTION_CANCEL = 'cancel';       // отменить
    const ACTION_RESPOND = 'respond';     // откликнуться
    const ACTION_DONE = 'done';           // выполнено
    const ACTION_REFUSE = 'refuse';       // отказаться

    private ?int $contractorId;    // исполнитель
    private int $customerId;       // заказчик

    private $status;  // ??

    /**
     * @param null|int $contractorId
     * @param int $customerId
     */
    public function __construct(int $customerId, ?int $contractorId = null)
    {
        $this->contractorId = $contractorId;
        $this->customerId = $customerId;
    }

    /** 
     * Возвращает карту статусов
     * 
     * @return array  
     */ 
    public function getMapStatus(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_СANCELLED => 'Отменено',
            self::STATUS_WORK => 'В работе',
            self::STATUS_DONE => 'Выполнено',
            self::STATUS_FAILED => 'Провалено'
        ];
    }

    /**
     * Возвращает карту действий
     * 
     * @return array  
     */ 
    public function getMapAction(): array
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
            self::ACTION_RESPOND => self::STATUS_WORK,    // ??
            self::ACTION_DONE => self::STATUS_DONE,
            self::ACTION_REFUSE => self::STATUS_FAILED    // ??            
        ];

        return $map[$action] ?? null;
    }

    /**
     * Возвращает действия, доступные для указанного статуса
     * 
     * @param string $status
     * @return null|array
     */
    public function getActions(string $status): ?array
    {
        $map = [
            self::STATUS_WORK => [self::ACTION_DONE, self::ACTION_REFUSE],
            self::STATUS_NEW => [self::ACTION_CANCEL, self::ACTION_RESPOND]
        ]; 
        
        return $map[$status] ?? null;
    }


}