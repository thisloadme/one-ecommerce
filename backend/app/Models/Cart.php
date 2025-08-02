<?php

namespace App\Models;

use Config;
use DB;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $connection = 'owner';
    protected $table = 'cart';

    protected $fillable = [
        'user_id',
        'database',
        'product_id',
        'quantity',
        'subtotal',
        'is_purchased',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'is_purchased' => 'boolean',
        'quantity' => 'integer',
    ];

    public function scopeNotPurchased($query)
    {
        return $query->where('is_purchased', false);
    }

    public function configure()
    {
        Config::set('database.connections.tenant', Config::get('database.connections.owner'));
        Config::set('database.connections.tenant.database', $this->database);

        DB::purge('tenant');
        DB::reconnect('tenant');
    }
}