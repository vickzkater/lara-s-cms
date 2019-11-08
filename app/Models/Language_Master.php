<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language_Master extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phrase', 'updated_at', 'deleted_at', 'status'
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
