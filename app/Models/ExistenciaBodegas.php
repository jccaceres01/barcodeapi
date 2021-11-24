<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExistenciaBodegas extends Model
{
  protected $table = 'SOCOCO.EXISTENCIA_BODEGA';

  protected $primaryKey = [
    'ARTICULO',
    'BODEGA'
  ];

  protected $fillable = [
    'ARTICULO',
    'BODEGA',
    'EXISTENCIA_MINIMA',
    'EXISTENCIA_MAXIMA',
    'PUNTO_DE_REORDEN',
    'CANT_DISPONIBLE',
    'CANT_RESERVADA',
    'CANT_NO_APROBADA',
    'CANT_VENCIDA',
    'CANT_TRANSITO',
    'CANT_PRODUCCION',
    'CANT_PEDIDA',
    'CANT_REMITIDA',
    'CONGELADO',
    'FECHA_CONG',
    'BLOQUEA_TRANS',
    'FECHA_DESCONG',
    'COSTO_UNT_PROMEDIO_LOC',
    'COSTO_UNT_PROMEDIO_DOL',
    'COSTO_UNT_ESTANDAR_LOC',
    'COSTO_UNT_ESTANDAR_DOL',
    'COSTO_PROM_COMPARATIVO_LOC',
    'COSTO_PROM_COMPARATIVO_DOLAR'
  ];

  protected $keyType = 'string';
  public $incrementing = false;
}
