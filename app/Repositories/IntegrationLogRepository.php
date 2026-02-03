<?php

namespace App\Repositories;

use App\Models\IntegrationLog;
use App\Models\IntegrationToken;
use App\Repositories\Interfaces\IntegrationLogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class IntegrationLogRepository implements IntegrationLogRepositoryInterface
{
    public function __construct(
        protected IntegrationLog $model
    ) {}

    public function create(array $data): IntegrationLog
    {
        return $this->model->create($data);
    }

    public function getByIntegration(IntegrationToken $integration, int $limit = 100): Collection
    {
        return $this->model
            ->where('integration_token_id', $integration->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function getRecentByIntegration(IntegrationToken $integration, int $hours = 24): Collection
    {
        return $this->model
            ->where('integration_token_id', $integration->id)
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc('created_at')
            ->get();
    }

    public function countByIntegration(IntegrationToken $integration): int
    {
        return $this->model
            ->where('integration_token_id', $integration->id)
            ->count();
    }

    public function countTodayByIntegration(IntegrationToken $integration): int
    {
        return $this->model
            ->where('integration_token_id', $integration->id)
            ->whereDate('created_at', today())
            ->count();
    }

    public function getAverageResponseTime(IntegrationToken $integration): ?float
    {
        $avg = $this->model
            ->where('integration_token_id', $integration->id)
            ->avg('response_time_ms');

        return $avg ? round($avg, 2) : null;
    }

    public function getErrorRate(IntegrationToken $integration): float
    {
        $total = $this->countByIntegration($integration);

        if ($total === 0) {
            return 0.0;
        }

        $errors = $this->model
            ->where('integration_token_id', $integration->id)
            ->where('response_status', '>=', 400)
            ->count();

        return round(($errors / $total) * 100, 2);
    }

    public function cleanOldLogs(int $daysToKeep = 30): int
    {
        return $this->model
            ->where('created_at', '<', now()->subDays($daysToKeep))
            ->delete();
    }
}
