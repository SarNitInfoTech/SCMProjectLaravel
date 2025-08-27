<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepartmentHead extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'department_head',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
