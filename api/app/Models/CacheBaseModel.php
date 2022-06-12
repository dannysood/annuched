<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
/**
 * CacheBaseModel that suports model level caching to be reused
 * - Limitations
 * - - caching of lazy-loaded relationships, see #127. Currently lazy-loaded belongs-to relationships are cached. Caching of other relationships is in the works.
 * - - using select() clauses in Eloquent queries, see #238 (work-around discussed in the issue https://github.com/GeneaLabs/laravel-model-caching/issues/238)
 * - - using transactions. If you are using transactions, you will likely have to manually flush the cache, see [issue #305](https://github.com/GeneaLabs/laravel-model-caching/issues/305).
 * Do not use with User model directly as it conflicts with its special imports
 * Please refer to caching limitations
 * https://github.com/GeneaLabs/laravel-model-caching#things-that-dont-work-currently
 * https://github.com/GeneaLabs/laravel-model-caching#exception-user-model
 */
class CacheBaseModel extends Model
{
    /**
     *
     */
    use Cachable;
}
