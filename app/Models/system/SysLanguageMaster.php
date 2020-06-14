<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysLanguageMaster extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_language_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phrase', 'status', 'created_at', 'updated_at'
    ];
}
