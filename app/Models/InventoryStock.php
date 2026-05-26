<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'department_id',
        'unit_id',
        'current_qty',
        'min_qty',
        'max_qty',
        'location',
        'is_active'
    ];

    protected $casts = [
        'current_qty' => 'decimal:4',
        'min_qty' => 'decimal:4',
        'max_qty' => 'decimal:4',
        'is_active' => 'boolean'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'stock_id');
    }

    public function scopeLowStock($query)
    {
        return $query->where('is_active', true)->whereColumn('current_qty', '<=', 'min_qty');
    }

    public function recalculateQty()
    {
        $total = 0;
        // Fetch all movements directly from database to avoid stale relations
        $movements = $this->movements()->get();
        foreach ($movements as $m) {
            $type = strtoupper($m->type);
            if (in_array($type, ['IN', 'RETURN'])) {
                $total += $m->quantity;
            } elseif (in_array($type, ['OUT', 'TRANSFER'])) {
                $total -= $m->quantity;
            } elseif ($type === 'ADJUST') {
                $total += $m->quantity;
            }
        }
        $this->current_qty = $total;
        $this->save();
        return $total;
    }
}
