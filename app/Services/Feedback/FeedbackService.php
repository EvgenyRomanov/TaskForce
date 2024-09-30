<?php

namespace App\Services\Feedback;

use App\Models\Feedback;
use App\Services\Feedback\DTO\CreateFeedbackDTO;

class FeedbackService
{
    public function create(CreateFeedbackDTO $feedbackDTO)
    {
        return Feedback::query()->create([
            'feedback' => $feedbackDTO->comment,
            'rating' => $feedbackDTO->rating,
            'executor_id' => $feedbackDTO->executorId,
            'customer_id' => $feedbackDTO->customerId,
            'task_id' => $feedbackDTO->taskId,
        ]);
    }
}
