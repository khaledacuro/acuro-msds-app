<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = ['field_name'];

    // Define relationship to DocumentData
    public function documentData()
    {
        return $this->hasMany(DocumentData::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Amsterdam')->toDateTimeString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Europe/Amsterdam')->toDateTimeString();
    }
}