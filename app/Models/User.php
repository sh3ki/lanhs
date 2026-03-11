<?php

namespace App\Models;

use Eloquent;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Route;
use Storage;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property int $role_id
 * @property int $status
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $email_verified_at
 * @property-read Collection|Department[] $departments
 * @property-read int|null $departments_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @property-read UserRole $userRole
 * @method static Builder|User filter($input = [], $filter = null)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User paginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|User query()
 * @method static Builder|User simplePaginateFilter($perPage = null, $columns = [], $pageName = 'page', $page = null)
 * @method static Builder|User whereAvatar($value)
 * @method static Builder|User whereBeginsWith($column, $value, $boolean = 'and')
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereEndsWith($column, $value, $boolean = 'and')
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLike($column, $value, $boolean = 'and')
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoleId($value)
 * @method static Builder|User whereStatus($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, Filterable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_ticket_check' => 'datetime',
        'permissions' => 'json',
    ];

    /**
     * Return user data
     *
     * @return BelongsTo
     */
    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function getAvatar(): string
    {
        if (Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'gravatar';
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_departments');
    }

    public function getGravatar(): string
    {
        return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?s=80&d=retro';
    }

    /**
     * Get effective permissions (user-specific or role default)
     *
     * @return array|null
     */
    public function getEffectivePermissions(): ?array
    {
        // User-level custom permissions take priority
        if ($this->permissions !== null) {
            $perms = $this->permissions;
            if (is_string($perms)) {
                $perms = json_decode($perms, true);
            }
            return is_array($perms) ? $perms : null;
        }

        // Fall back to role permissions
        if ($this->userRole) {
            $rolePerms = $this->userRole->permissions;
            if (is_string($rolePerms)) {
                $rolePerms = json_decode($rolePerms, true);
            }
            return is_array($rolePerms) ? $rolePerms : null;
        }

        return null;
    }

    /**
     * Get effective permissions as a controller map
     *
     * @return array
     * @throws \Exception
     */
    public function getEffectivePermissionMap(): array
    {
        $controllers = [];
        $permissions = $this->getEffectivePermissions() ?? [];

        foreach (Route::getRoutes()->getIterator() as $route) {
            if (strpos($route->uri, 'api/dashboard') !== false) {
                $path = str_replace('\\', '.', explode('@', str_replace($route->action['controller'].'\\', '', $route->action['controller']))[0]);
                $controllers[$path] = $this->role_id === 1 ? true : in_array($path, $permissions, true);
            }
        }

        return $controllers;
    }

    /**
     * Check effective dashboard access (user-specific or role default)
     *
     * @return bool
     */
    public function checkEffectiveDashboardAccess(): bool
    {
        if ($this->role_id === 1) {
            return true;
        }

        if ($this->dashboard_access !== null) {
            return (bool) $this->dashboard_access;
        }

        return $this->userRole ? $this->userRole->checkDashboardAccess() : false;
    }

    /**
     * Check if user has permission to access a route
     *
     * @param string $route
     * @return bool
     */
    public function checkEffectivePermission(string $route): bool
    {
        if ($this->role_id === 1) {
            return true;
        }

        if (!$this->checkEffectiveDashboardAccess()) {
            return false;
        }

        $permissions = $this->getEffectivePermissions();

        return $permissions ? in_array($route, $permissions, true) : false;
    }
}
