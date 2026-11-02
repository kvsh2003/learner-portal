<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrolmentResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enrolled_at' => $this->created_at,
            'course' => [
                'id' => $this->course->id,
                'title' => $this->course->title,
                'slug' => $this->course->slug,
                'description' => $this->course->description,
                'level' => $this->course->level,
                'thumbnail_url' => $this->course->thumbnail_url,
            ],
            'progress' => $this->progress,
        ];
    }
}

