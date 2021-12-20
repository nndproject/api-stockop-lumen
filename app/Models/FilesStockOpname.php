<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilesStockOpname extends Model
{
    protected $table    = 'stockop_files';
    public $timestamps  = true;
    protected $fillable = [
		'itemno',
		'id_stockop',
		'files',
    ];
}
