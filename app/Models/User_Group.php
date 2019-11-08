<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Group extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_group';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user', 'group'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    // protected $guarded = [];
}
