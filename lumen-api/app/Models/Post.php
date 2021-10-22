<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;

class Post extends Model
{
    use ModelEventLogger;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'content'
    ];
}
