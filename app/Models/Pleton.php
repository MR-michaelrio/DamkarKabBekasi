<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pleton extends Model
{
    protected $fillable = ['name'];

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}
