<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentData extends Model
{
    use HasFactory;

    protected $fillable = ['field_id', 'document_id', 'value', 'confidence'];


    // Define relationship to Field
    public function field()
    {
        return $this->belongsTo(Field::class);
    }


    // Define relationship to Document
    public function document()
    {
        return $this->belongsTo(Document::class);
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
