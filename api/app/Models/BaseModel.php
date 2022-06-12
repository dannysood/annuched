<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
/**
 * BaseModel that suports model level caching
 * Do not use with User model directly as it conflicts with its special imports
 * https://github.com/GeneaLabs/laravel-model-caching#exception-user-model
 */
class BaseModel extends Model
{
    use Cachable;
}
