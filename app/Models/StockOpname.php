<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $table='stockop';
    protected $fillable = [
		'id',
		'periode',
		'year',
        'file',
        'id_direktur',
		'date_approve_direktur',
		'status',
		'post_by',
    ];

    public function hasPostBy()
    {
      return $this->hasOne(User::class,'id','post_by'); 
    }

}
