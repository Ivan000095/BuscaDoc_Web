<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ["name", "description", "price", "image"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            "id" => "integer",
            "price" => "decimal:2",
        ];
    }

    /**
     * Get the image URL attribute
     *
     * @return string|null
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset("storage/" . $this->image);
        }
        return null;
    }

    /**
     * Check if product has image
     *
     * @return bool
     */
    public function hasImage(): bool
    {
        return !empty($this->image) &&
            Storage::disk("public")->exists($this->image);
    }
}
