<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Albaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'cliente_id',
        'paciente',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'albaran_productos')
            ->withPivot('cantidad', 'precio_unitario', 'importe_total')
            ->withTimestamps();
    }
        public function facturas()
    {
        return $this->belongsToMany(Factura::class, 'factura_albarans')
            ->withPivot('importe')
            ->withTimestamps();
    }
}
