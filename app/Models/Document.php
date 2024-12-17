<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['file_name'];

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
