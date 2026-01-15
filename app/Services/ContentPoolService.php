<?php

namespace App\Services;

use App\Models\TaskContentPool;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ContentPoolService
{
    /**
     * Add content to pool (duplicate-safe)
     */
    public function addToPool($type, $title, $content, $source = 'api')
    {
        try {
            return TaskContentPool::updateOrCreate(
                [
                    'type' => $type,
                    'title' => $title,
                ],
                [
                    'content' => $content,
                    'source' => $source,
                    'is_active' => true,
                ]
            );
        } catch (\Exception $e) {
            Log::error('Failed to add content to pool', [
                'type' => $type,
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get content from pool for task generation
     *
     * @param string $type Type of content (VIDEO, SURVEY, REVIEW, SYNC)
     * @param int $count Number of items to retrieve
     * @param int $daysOld Don't reuse content used in last X days
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFromPool($type, $count, $daysOld = 30)
    {
        return TaskContentPool::where('type', $type)
            ->where('is_active', true)
            ->where(function ($query) use ($daysOld) {
                $query->whereNull('last_used_at')
                    ->orWhere('last_used_at', '<', now()->subDays($daysOld));
            })
            ->orderBy('times_used', 'asc') // Prefer less-used content
            ->orderBy('created_at', 'desc') // Then newest first
            ->limit($count)
            ->get();
    }

    /**
     * Get fresh content from pool (not used recently) - ALIAS
     */
    public function getFreshContent($type, $count, $daysOld = 30)
    {
        return $this->getFromPool($type, $count, $daysOld);
    }

    /**
     * Mark content as used
     */
    public function markAsUsed($contentId)
    {
        $content = TaskContentPool::find($contentId);
        if ($content) {
            $content->markAsUsed();
        }
    }

    /**
     * Get pool statistics
     */
    public function getStats()
    {
        return [
            'total' => TaskContentPool::count(),
            'by_type' => TaskContentPool::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'unused' => TaskContentPool::unused()->count(),
            'active' => TaskContentPool::active()->count(),
        ];
    }

    /**
     * Clean old/overused content
     */
    public function cleanup($maxAge = 90, $maxUsage = 50)
    {
        $deleted = TaskContentPool::where('created_at', '<', now()->subDays($maxAge))
            ->where('times_used', '>', $maxUsage)
            ->delete();

        Log::info("Cleaned up old pool content", ['deleted' => $deleted]);
        return $deleted;
    }
}
