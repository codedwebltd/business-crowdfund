<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'type',
        'category',
        'faq_items',
        'featured_image',
        'excerpt',
        'author_id',
        'published_at',
        'is_published',
        'is_featured',
        'show_in_menu',
        'menu_location',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'meta_data',
    ];

    protected $casts = [
        'faq_items' => 'json',
        'meta_data' => 'json',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_menu' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = self::generateSlug($page->title);
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = self::generateSlug($page->title, $page->id);
            }
        });
    }

    /**
     * Generate unique slug
     */
    public static function generateSlug($title, $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (self::where('slug', $slug)->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get available types
     */
    public static function getTypes(): array
    {
        return [
            'faq' => 'FAQ',
            'blog' => 'Blog Post',
            'terms' => 'Terms & Conditions',
            'privacy' => 'Privacy Policy',
            'about' => 'About Us',
            'help' => 'Help Article',
            'support' => 'Support Article',
            'custom' => 'Custom Page',
        ];
    }

    /**
     * Get FAQ categories
     */
    public static function getFaqCategories(): array
    {
        return [
            'general' => 'General',
            'account' => 'Account & Profile',
            'tasks' => 'Tasks & Earnings',
            'withdrawals' => 'Withdrawals & Payments',
            'referrals' => 'Referrals & Commissions',
            'security' => 'Security & Privacy',
            'plans' => 'Plans & Subscriptions',
            'kyc' => 'KYC & Verification',
        ];
    }

    /**
     * Get the author
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: Published pages
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: By type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: FAQs
     */
    public function scopeFaqs($query)
    {
        return $query->where('type', 'faq');
    }

    /**
     * Scope: Blogs
     */
    public function scopeBlogs($query)
    {
        return $query->where('type', 'blog');
    }

    /**
     * Scope: By category
     */
    public function scopeInCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Featured
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: For menu
     */
    public function scopeForMenu($query, $location = null)
    {
        return $query->where('show_in_menu', true)
            ->when($location, fn($q) => $q->where('menu_location', $location));
    }

    /**
     * Scope: Ordered
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Increment view count
     */
    public function incrementViews()
    {
        $this->increment('views_count');
        return $this;
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::getFaqCategories()[$this->category] ?? ucfirst($this->category ?? 'Uncategorized');
    }

    /**
     * Check if this is an FAQ page
     */
    public function isFaq(): bool
    {
        return $this->type === 'faq';
    }

    /**
     * Check if this is a blog post
     */
    public function isBlog(): bool
    {
        return $this->type === 'blog';
    }

    /**
     * Get FAQ items count
     */
    public function getFaqCountAttribute(): int
    {
        if (!$this->isFaq() || empty($this->faq_items)) {
            return 0;
        }
        return count($this->faq_items);
    }

    /**
     * Publish the page
     */
    public function publish()
    {
        $this->update([
            'is_published' => true,
            'published_at' => $this->published_at ?? now(),
        ]);
        return $this;
    }

    /**
     * Unpublish the page
     */
    public function unpublish()
    {
        $this->update(['is_published' => false]);
        return $this;
    }
}
