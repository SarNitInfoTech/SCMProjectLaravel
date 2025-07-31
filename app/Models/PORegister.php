<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\POStatus;

class PORegister extends Model
{
protected $table = 'po_registers';

protected $fillable = [
    'indent_id',
    'department_id',
    'status',
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
    'status' => POStatus::class,  // Only if using enum
];

}
