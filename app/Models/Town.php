<?php

namespace App\Models;

use App\Models\Traits\Filterable;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Town extends Model
{
    use HasFactory, HasUuids, Filterable, SoftDeletes;

    protected $table = "towns";

    protected $fillable = [
        'name',
        'alias',
        'sort_order',
        'comment',
        'last_editor_id',
        'destroyer_id',
        'author_id',
    ];

    public function author()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function last_editor()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function destroyer()
    {
        return $this->belongsTo(
            User::class
        );
    }
}
