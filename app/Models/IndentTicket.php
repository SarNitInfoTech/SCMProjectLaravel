<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'indent_id',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
