<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'gst_number', 'pan_number',
        'account_name', 'account_number', 'bank_name', 'branch_name', 'ifsc_code', 'is_active'
    ];
}
