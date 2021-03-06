<?php

namespace App\Models;

use App\Events\PostCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;
use App\Traits\PreventsUpdating;

/**
 * Blog Post
 */
class Post extends Model
{
    use HasFactory,HasUuid,PreventsUpdating;
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'owner_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'updated_at',
        'owner_id'
    ];

    /**
     * Events dispatched based on different model actions
     */
    protected $dispatchesEvents = [
        'created' => PostCreated::class
    ];

    /**
     * Relationships
     */
    public function owner()
    {
        return $this->belongsTo(User::class,'owner_id','id');
    }



}
