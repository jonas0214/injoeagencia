<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $fillable = [
        'team_member_id',
        'reference',
        'subtotal',
        'discount',
        'amount',
        'status',
        'notes',
        'task_ids',
        'billed_at'
    ];

    protected $casts = [
        'task_ids' => 'array',
        'billed_at' => 'datetime'
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}
