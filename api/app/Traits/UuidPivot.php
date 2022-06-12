<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasUuid;

class UuidPivot extends Pivot
{
    use HasUuid;
}
