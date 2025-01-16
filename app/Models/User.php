<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasPublicId;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasPublicId;
    use Notifiable;

    /**
     * {@inheritDoc}
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the current team of the user's context.
     *
     * @return BelongsTo<Organization, $this>
     */
    public function currentOrganization(): BelongsTo
    {
        if (is_null($this->current_organization_id) && ! is_null($organization = $this->organizations->first())) {
            $this->switchOrganization($organization);
        }

        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    /**
     * The organizations that belong to the user.
     *
     * @return BelongsToMany<Organization, $this>
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->as('membership')
            ->using(Membership::class)
            ->withTimestamps();
    }

    /**
     * Check if the user belongs to the given organization.
     */
    public function belongsToOrganization(?Organization $organization): bool
    {
        return ! is_null($organization) && $this->organizations->contains($organization);
    }

    /**
     * Switch the user's current organization.
     */
    public function switchOrganization(Organization $organization): bool
    {
        if (! $this->belongsToOrganization($organization)) {
            return false;
        }

        $this->update([
            'current_organization_id' => $organization->id,
        ]);

        $this->setRelation('currentOrganization', $organization);

        return true;
    }

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
