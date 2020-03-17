<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChildcategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
                        parent::toArray($request), 
                        ["subcategory" => new SubcategoryResource($this->subcategory)],
                        ["category" => new CategoryResource($this->subcategory->category)]
                    );
    }
}
