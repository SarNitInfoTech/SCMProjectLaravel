<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id',
        'type',
        'quantity',
        'qty_before',
        'qty_after',
        'vendor_id',
        'po_register_id',
        'user_id',
        'reference_no',
        'remarks',
        'metadata',
        'movement_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'qty_before' => 'decimal:4',
        'qty_after' => 'decimal:4',
        'metadata' => 'array',
        'movement_date' => 'date'
    ];

    public function stock()
    {
        return $this->belongsTo(InventoryStock::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function poRegister()
    {
        return $this->belongsTo(PORegister::class, 'po_register_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::creating(function ($movement) {
            $stock = $movement->stock;
            if ($stock) {
                $movement->qty_before = $stock->current_qty;
                $type = strtoupper($movement->type);

                if (in_array($type, ['IN', 'RETURN'])) {
                    $movement->qty_after = $movement->qty_before + $movement->quantity;
                } elseif (in_array($type, ['OUT', 'TRANSFER'])) {
                    $movement->qty_after = $movement->qty_before - $movement->quantity;
                } elseif ($type === 'ADJUST') {
                    $movement->qty_after = $movement->qty_before + $movement->quantity;
                } else {
                    $movement->qty_after = $movement->qty_before;
                }

                if (empty($movement->user_id) && auth()->check()) {
                    $movement->user_id = auth()->id();
                }
            }
        });

        static::created(function ($movement) {
            $stock = $movement->stock;
            if ($stock) {
                $stock->current_qty = $movement->qty_after;
                $stock->save();
            }
        });

        static::updated(function ($movement) {
            $movement->stock?->recalculateQty();
        });

        static::deleted(function ($movement) {
            $movement->stock?->recalculateQty();
        });
    }
}
