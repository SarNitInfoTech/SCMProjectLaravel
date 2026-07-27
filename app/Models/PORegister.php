<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\POStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PORegister extends Model
{
    protected $table = 'po_registers';

    protected $fillable = [
        'indent_id',
        'department_id',
        'status',
        'closed_at',
        'closed_by',
        'reopened_at',
        'reopened_by',
        'close_reason',
        'is_mandatory',
        'invoice',
        'po_date',
        'party_name',
        'po_wo_no',
        'item_description',
        'po_amount',
        'debit_head',
        'expected_days',
        'expected_date',
        'invoice_date',
        'receiving_date',
        'delay_in_days',
        'remarks',
        'store_indent_no',
    ];

    protected $casts = [
        'status'      => POStatus::class,
        'closed_at'   => 'datetime',
        'reopened_at' => 'datetime',
    ];

    public function auditLogs(): HasMany
    {
        return $this->hasMany(POAuditLog::class, 'po_id')->orderBy('created_at', 'desc');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function reopenedByUser()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    /**
     * Check if goods receiving is allowed for this PO.
     */
    public function canReceiveGoods(): bool
    {
        $st = is_object($this->status) ? $this->status->value : (string)$this->status;
        $normalized = mb_strtolower(trim($st));
        return !in_array($normalized, ['closed', 'close', 'cancel', 'cancelled', 'completed']);
    }
}
