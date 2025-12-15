<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $created_at
 * @property mixed $stock
 * @property mixed $price
 * @property mixed $name
 * @property mixed $id
 */
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
            'name' => $this->name,
            'price' => (float)$this->price,
            'stock' => (int)$this->stock,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
