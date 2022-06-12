<?php

namespace App\Traits\Uuid;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasUuid;

class UuidPivot extends Pivot
{
    use HasUuid;
}
