<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clasificaciones extends Model
{
  protected $table = 'SOCOCO.CLASIFICACION';
  public $incrementing = false;
  public $keyType = 'varchar';
  protected $primary = 'CLASIFICACION';
}
