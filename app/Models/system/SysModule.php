<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysModule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'status', 'updated_at'
    ];
}
