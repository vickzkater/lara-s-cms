<?php

namespace App\Models\system;

use Illuminate\Database\Eloquent\Model;

class SysConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_config';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_name',
        'app_url_site',
        'app_url_main',
        'app_version',
        'app_favicon_type',
        'app_favicon',
        'app_logo',
        'app_logo_image',
        'help',
        'powered',
        'powered_url',
        'meta_keywords',
        'meta_title',
        'meta_description',
        'meta_author',
        'updated_at'
    ];
}
