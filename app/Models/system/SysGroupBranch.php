<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysGroupBranch extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_group_branch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group', 'branch'
    ];

    public $timestamps = false;
}
