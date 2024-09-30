<?php

namespace App\Services\Feedback\DTO;

readonly class CreateFeedbackDTO
{
    public function __construct(
        public int $customerId,
        public int $executorId,
        public int $taskId,
        public int $rating,
        public ?string $comment = null,
    ) {
    }
}
