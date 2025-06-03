<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    protected $fillable = [
        'fecha',
        'cliente_id',
        'total',
    ];
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function albaranes()
    {
        return $this->belongsToMany(Albaran::class, 'factura_albarans')
            ->withPivot('importe')
            ->withTimestamps();
    }
}
