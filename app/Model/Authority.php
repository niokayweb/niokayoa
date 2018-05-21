<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Authority extends Model
{
    protected $table='authority';

    protected $guarded = ['id'];

    public $timestamps = false;
}
