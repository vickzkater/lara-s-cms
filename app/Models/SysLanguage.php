<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SysLanguage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'alias', 'name', 'status', 'ordinal', 'created_at', 'updated_at'
    ];
}
