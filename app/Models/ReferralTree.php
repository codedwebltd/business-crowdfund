<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTree extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'parent_id', 'level', 'root_ancestor_id',
        'path', 'left_boundary', 'right_boundary',
    ];

    protected $casts = [
        'level' => 'integer',
        'left_boundary' => 'integer',
        'right_boundary' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user() { return $this->belongsTo(User::class); }
    public function parent() { return $this->belongsTo(User::class, 'parent_id'); }
    public function rootAncestor() { return $this->belongsTo(User::class, 'root_ancestor_id'); }

    // ==================== SCOPES ====================

    /**
     * Get all descendants (downline) of a user
     */
    public function scopeDescendantsOf($query, $userId)
    {
        $node = self::where('user_id', $userId)->first();
        if (!$node) return $query->whereRaw('1 = 0');

        return $query->where('left_boundary', '>', $node->left_boundary)
                     ->where('right_boundary', '<', $node->right_boundary)
                     ->orderBy('level');
    }

    /**
     * Get all ancestors (upline) of a user
     */
    public function scopeAncestorsOf($query, $userId)
    {
        $node = self::where('user_id', $userId)->first();
        if (!$node) return $query->whereRaw('1 = 0');

        return $query->where('left_boundary', '<', $node->left_boundary)
                     ->where('right_boundary', '>', $node->right_boundary)
                     ->orderBy('level');
    }

    /**
     * Get direct children (level 1 referrals)
     */
    public function scopeDirectChildrenOf($query, $userId)
    {
        return $query->where('parent_id', $userId);
    }

    /**
     * Get by level
     */
    public function scopeAtLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get max referral depth from global settings
     */
    public static function getMaxDepth(): int
    {
        return GlobalSetting::first()->referral_levels_depth ?? 40;
    }

    /**
     * Build referral tree for new user
     * Called when user registers with referrer
     */
    public static function buildForUser(User $user, ?User $referrer = null): self
    {
        if (!$referrer) {
            // Root user (no referrer)
            return self::create([
                'user_id' => $user->id,
                'parent_id' => null,
                'level' => 0,
                'root_ancestor_id' => $user->id,
                'path' => $user->id,
                'left_boundary' => 1,
                'right_boundary' => 2,
            ]);
        }

        // User has referrer
        $referrerNode = self::where('user_id', $referrer->id)->first();

        if (!$referrerNode) {
            throw new \Exception("Referrer node not found in tree");
        }

        // Calculate level
        $level = $referrerNode->level + 1;
        $maxDepth = self::getMaxDepth();

        if ($level > $maxDepth) {
            throw new \Exception("Maximum referral depth ({$maxDepth}) exceeded");
        }

        // Build path
        $path = $referrerNode->path . '.' . $user->id;

        // Update nested set boundaries (simplified - needs proper implementation)
        self::where('right_boundary', '>=', $referrerNode->right_boundary)
            ->increment('right_boundary', 2);
        self::where('left_boundary', '>', $referrerNode->right_boundary)
            ->increment('left_boundary', 2);

        // Create new node
        return self::create([
            'user_id' => $user->id,
            'parent_id' => $referrer->id,
            'level' => $level,
            'root_ancestor_id' => $referrerNode->root_ancestor_id,
            'path' => $path,
            'left_boundary' => $referrerNode->right_boundary,
            'right_boundary' => $referrerNode->right_boundary + 1,
        ]);
    }

    /**
     * Get all upline users (for commission distribution)
     * Returns array of user_ids with their levels
     */
    public function getUplineForCommissions(): array
    {
        $maxDepth = self::getMaxDepth();

        $ancestors = self::ancestorsOf($this->user_id)
            ->where('level', '<=', $maxDepth)
            ->get(['user_id', 'level'])
            ->map(fn($node) => [
                'user_id' => $node->user_id,
                'level' => $this->level - $node->level, // Relative level from this user
            ])
            ->toArray();

        return $ancestors;
    }

    /**
     * Get team size at each level
     */
    public function getTeamSizeByLevel(): array
    {
        $descendants = self::descendantsOf($this->user_id)
            ->selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->pluck('count', 'level')
            ->toArray();

        return $descendants;
    }

    /**
     * Get total team size (all downlines)
     */
    public function getTotalTeamSize(): int
    {
        $node = self::where('user_id', $this->user_id)->first();
        if (!$node) return 0;

        return self::where('left_boundary', '>', $node->left_boundary)
                   ->where('right_boundary', '<', $node->right_boundary)
                   ->count();
    }

    /**
     * Get direct referrals count
     */
    public function getDirectReferralsCount(): int
    {
        return self::where('parent_id', $this->user_id)->count();
    }
}
