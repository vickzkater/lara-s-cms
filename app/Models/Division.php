<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'divisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'updated_at', 'deleted_at', 'status', 'isDeleted'
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
