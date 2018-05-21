<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table='role';

    protected $guarded = ['id'];

    public $timestamps = false;

}
