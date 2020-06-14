<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysUserGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_user_group';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'group'
    ];

    public $timestamps = false;
}
