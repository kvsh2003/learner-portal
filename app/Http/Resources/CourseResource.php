<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
   
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'level' => $this->level,
            'thumbnail_url' => $this->thumbnail_url,
            'is_published' => $this->is_published,
            'modules_count' => $this->whenCounted('modules'),
            'lessons_count' => $this->whenCounted('lessons'),
            'modules' => ModuleResource::collection($this->whenLoaded('modules')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

