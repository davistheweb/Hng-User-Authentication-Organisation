<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organisation extends Model
{
    use HasFactory;

    protected $primaryKey = 'orgId';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    protected $fillable = [
        'name',
        'description',
    ];

    protected $hidden = [
        'pivot',
        'created_at',
        'updated_at'
    ];

    // Define relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_user', 'organisation_id', 'user_id');
    }
}
