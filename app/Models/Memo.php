<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    protected $fillable = ['category_id', 'content', 'is_checked'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
