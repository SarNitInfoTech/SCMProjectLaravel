<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndentRegister extends Model
{
    protected $fillable = [
    'indent_id',
    'indent_date',
    'indent_department',
    'indent_project',
    'items_description',
    'status'
];
}
