<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExistenciaLotes extends Model
{
  protected $table = 'SOCOCO.EXISTENCIA_LOTE';
  protected $primaryKey = [
    'BODEGA',
    'ARTICULO',
    'LOCALIZACION',
    'LOTE'
  ];
  
  protected $keyType = 'string';
  public $incrementing = false;

  protected $fillable = [
    'BODEGA',
    'ARTICULO',
    'LOCALIZACION',
    'LOTE',
    'CANT_DISPONIBLE',
    'CANT_RESERVADA',
    'CANT_NO_APROBADA',
    'CANT_VENCIDA',
    'CANT_REMITIDA',
    'COSTO_UNT_PROMEDIO_LOC',
    'COSTO_UNT_PROMEDIO_DOL',
    'COSTO_UNT_ESTANDAR_LOC',
    'COSTO_UNT_ESTANDAR_DOL'
  ];
  /**
   * Bodega foreign key relationship
   */
  public function storage() {
    return $this->hasOne('App\Bodegas', 'BODEGA', 'BODEGA');
  }
}
