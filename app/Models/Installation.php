<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Installation extends Model
{
    use HasFactory, SoftDeletes;

    public $incrementing = true;

    protected $table = 'installations';

    protected $guarded = [];

    protected $casts = [
        'documentacion' => 'array',
        'conyuge_bienes_mancomunados_documentacion' => 'array',
        'dictamen_legal_fase_uno' => 'array',
        'dictamen_legal_fase_dos' => 'array',
        'fecha_dictamen' => 'date'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function valves()
    {
        return $this->hasMany(Valve::class);
    }

    /**
     * @param $value
     * @return false|string
     */
    protected function setDocumentacionAttribute($documentation): bool|string
    {
        $status_values = array_filter($documentation, fn($value, $key) => strpos($key, '_status', 0), ARRAY_FILTER_USE_BOTH);
        $status_values = array_map(fn($value) => $value === null ? 'revision' : $value, $status_values);
        foreach ($status_values as $key => $value) {
            $documentation[$key] = $value;
        }

        return $this->attributes['documentacion'] = json_encode($documentation);
    }

    public function getRegimenPropiedadInmuebleAttribute($value)
    {
        return match ($value) {
            'pr' => 'Propiedad privada',
            'ej' => 'Propiedad ejidal',
            'pa' => 'Parcela',
            'po' => 'Posesión',
            'ca' => 'Comunidad Agraria',
            default => '',
        };
    }
}
