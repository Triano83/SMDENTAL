<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
        use HasFactory;
    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'direccion',
        'NIF',
        'CP',
        'poblacion',
        'provincia',
    ];

    public function albaranes()
    {
        return $this->hasMany(Albaran::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

}
