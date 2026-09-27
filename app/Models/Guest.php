<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_checked_in' => 'boolean',
        'checked_in_at' => 'datetime',
        'pax' => 'integer',
        'rsvp_pax' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($guest) {
            if (empty($guest->ticket_code)) {
                $guest->ticket_code = 'TKT-' . strtoupper(substr(uniqid(), -6));
            }
            if (empty($guest->slug)) {
                $baseSlug = \Illuminate\Support\Str::slug($guest->name);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $guest->slug = $slug;
            }
        });
    }

    public function wishes()
    {
        return $this->hasMany(Wish::class);
    }

    public function getInvitationUrlAttribute()
    {
        return url('/to/' . $this->slug);
    }

    public function getTicketUrlAttribute()
    {
        return url('/ticket/' . $this->ticket_code);
    }
}
