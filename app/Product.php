<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'title', 'description', 'price', 'image'
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withTimestamps();
    }
}
