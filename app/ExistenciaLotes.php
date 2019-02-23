<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExistenciaLotes extends Model
{
  protected $table = 'SOCOCO.EXISTENCIA_LOTE';

  public $incrementing = false;

  /**
   * Bodega foreign key relationship
   */
  public function storage() {
    return $this->hasOne('App\Bodegas', 'BODEGA', 'BODEGA');
  }
}
