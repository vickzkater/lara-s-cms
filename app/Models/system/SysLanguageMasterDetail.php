<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysLanguageMasterDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_language_master_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'language_id', 'language_master_id', 'translate', 'status', 'created_at', 'updated_at'
    ];
}
