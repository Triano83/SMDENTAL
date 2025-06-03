<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'precio',
    ];

    public function albaranes()
    {
        return $this->belongsToMany(Albaran::class, 'albaran_producto')
            ->withPivot('cantidad', 'precio_unitario', 'importe_total')
            ->withTimestamps();
    }

}
