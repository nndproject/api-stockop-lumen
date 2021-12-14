<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStockOpname extends Model
{
    protected $table='tmp_stockop';
    protected $fillable = [
		'itemno',
		'itemdesc',
		'unit',
        'qty',
        'id_wh',
		'warehouse',
		'periode',
		'year',
		'stockop',
		'desc',
		'post_by',
    ];
}
