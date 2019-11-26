<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'title', 'description', 'price', 'image'
    ];

    // add many to many relationship
    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
