<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExistenciaReservas extends Model
{
  protected $table = 'SOCOCO.EXISTENCIA_RESERVA';
  protected $primaryKey = [
    'ARTICULO',
    'APLICACION',
    'BODEGA',
    'LOTE',
    'LOCALIZACION'
  ];

  protected $keyType = 'string';
  public $incrementing = false;

  protected $fillable = [
    'ARTICULO',
    'APLICACION',
    'BODEGA',
    'LOTE',
    'LOCALIZACION',
    'SERIE_CADENA',
    'MODULO_ORIGEN',
    'CANTIDAD',
    'USUARIO',
    'FECHA_HORA'
  ];

}
