<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TokenCollection extends ResourceCollection
{
    public $collects = TokenResource::class;

    public function toArray(Request $request): array
    {
        return $this->collection->toArray();
    }
}
