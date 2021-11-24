<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoInv extends Model
{
  protected $table = 'SOCOCO.DOCUMENTO_INV';

  public $incrementing = false;
  protected $keyType = 'varchar';
  public $timestamps = false;

  protected $fillable = [
    'ARTICULO',
    'DESCRIPCION',
    'CODIGO_BARRAS_INVT',
    'CODIGO_BARRAS_VENT'
  ];

}
