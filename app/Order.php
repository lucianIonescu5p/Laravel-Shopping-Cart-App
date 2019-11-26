<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $fillable = [
        'name', 'email'
    ];

    // add many to many relationship
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
