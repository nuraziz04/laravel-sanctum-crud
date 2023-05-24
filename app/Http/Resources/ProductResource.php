<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'id' => $this->id,
          'nmae' => $this->name,
          'detail' => $this->detail,
          'createdAt' => $this->created_at->format('d/m/Y'),
          'updatedAt' => $this->updated_at->format('d/m/Y')
        ];
    }
}
