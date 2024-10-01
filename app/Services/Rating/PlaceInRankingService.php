<?php

namespace App\Services\Rating;

use Illuminate\Support\Facades\DB;

class PlaceInRankingService
{
    public function __invoke(int $userId): ?int
    {
        $result = DB::select("
            SELECT
                TMP2.row_num
            FROM (
                     SELECT
                         ROW_NUMBER() OVER (
                             ORDER BY TMP1.rating DESC
                         ) row_num,
                         TMP1.user_id,
                         TMP1.rating
                     FROM (
                              SELECT
                                  users.id AS user_id,
                                  IF(
                                    (COUNT(*) - users.cnt_failed_tasks) = 0,
                                    0,
                                    SUM(feedback.rating) / (COUNT(*) - users.cnt_failed_tasks)
                                  ) AS rating
                              FROM
                                  users
                                  JOIN roles ON users.role_id = roles.id
                                  LEFT JOIN feedback ON users.id = feedback.executor_id
                              WHERE
                                  roles.name = 'executor'
                              GROUP BY
                                  users.id
                          ) AS TMP1
            ) AS TMP2
            WHERE
                TMP2.user_id = ?
        ", [$userId]);

        return $result[0]->row_num ?? null;
    }
}
