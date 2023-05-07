<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'documentacion' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $value
     * @return false|string
     */
    protected function setDocumentacionAttribute($documentation): bool|string
    {
        $status_values = array_filter($documentation, fn ($value, $key) => strpos($key, '_status', 0), ARRAY_FILTER_USE_BOTH);
        $status_values = array_map(fn ($value) => $value === null ? 'revision' : $value, $status_values);
        foreach ($status_values as $key => $value) {
            $documentation[$key] = $value;
        }

        return $this->attributes['documentacion'] = json_encode($documentation);
    }
}
