<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

abstract class BaseModel extends Model
{
    use SoftDeletes, HasFactory, HasEvents;

    protected $appends = [
        'created_at2',
        'updated_at2',
        'deleted_at2'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getCreatedAt2Attribute(): string
    {
        return (new Carbon($this->created_at))->format('d/m/Y H:i:s');
    }

    public function getUpdatedAt2Attribute(): string
    {
        return (new Carbon($this->updated_at))->format('d/m/Y H:i:s');
    }

    public function getDeletedAt2Attribute(): ?string
    {
        return !is_null($this->deleted_at) ? (new Carbon($this->deleted_at))->format('d/m/Y H:i:s') : null;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
        static::retrieved(function ($model) {
            $hiddenFields = [];
            $attributes = $model->getAttributes();
            foreach ($attributes as $key => $value) {
                if (substr($key, -3) === '_id' && $key) {
                    $hiddenFields[] = $key;
                }
            }
            $model->makeHidden($hiddenFields);
        });
    }
}
