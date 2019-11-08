<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppAccess extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'app_access';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usergroup_id', 'rule_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    // protected $guarded = [];
}
