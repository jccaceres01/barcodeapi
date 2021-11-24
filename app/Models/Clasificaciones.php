<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clasificaciones extends Model
{
  protected $table = 'SOCOCO.CLASIFICACION';
  public $incrementing = false;
  protected $keyType = 'varchar';
  protected $primaryKey = 'CLASIFICACION';
}
