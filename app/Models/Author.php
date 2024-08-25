<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Author extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ["name", "bio", "birth_date"];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dates = ['deleted_at'];
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
    public static function findByUuid($uuid)
    {
        return static::where('id', $uuid)->first();
    }
    public function book(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
