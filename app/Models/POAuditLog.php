<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class POAuditLog extends Model
{
    protected $table = 'po_audit_logs';

    protected $fillable = [
        'po_id',
        'user_id',
        'user_name',
        'action',
        'previous_status',
        'new_status',
        'reason',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function po(): BelongsTo
    {
        return $this->belongsTo(PORegister::class, 'po_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
