<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsSystem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'logs_system';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subject', 'action', 'object', 'created_at', 'updated_at'
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
