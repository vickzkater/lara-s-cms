<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysGroupRule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_group_rule';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id', 'rule_id'
    ];

    public $timestamps = false;
}
