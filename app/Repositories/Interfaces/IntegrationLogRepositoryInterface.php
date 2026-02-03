<?php

namespace App\Repositories\Interfaces;

use App\Models\IntegrationLog;
use App\Models\IntegrationToken;
use Illuminate\Database\Eloquent\Collection;

interface IntegrationLogRepositoryInterface
{
    public function create(array $data): IntegrationLog;

    public function getByIntegration(IntegrationToken $integration, int $limit = 100): Collection;

    public function getRecentByIntegration(IntegrationToken $integration, int $hours = 24): Collection;

    public function countByIntegration(IntegrationToken $integration): int;

    public function countTodayByIntegration(IntegrationToken $integration): int;

    public function getAverageResponseTime(IntegrationToken $integration): ?float;

    public function getErrorRate(IntegrationToken $integration): float;

    public function cleanOldLogs(int $daysToKeep = 30): int;
}
