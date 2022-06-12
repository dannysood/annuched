<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Model;

/**
 * PreventsUpdating
 */
trait PreventsUpdating
{
    /**
     * Blocks updates to the model ensuring the posts are only write once
     */
    public static function bootPreventsUpdating()
    {
        static::updating(function (Model $model) {
            return false;
        });
    }
}

?>
