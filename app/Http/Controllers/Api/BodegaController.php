<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BodegaController extends Controller
{
  /**
   * Get All Storage
   */
  public function index() {
    return \DB::table('SOCOCO.BODEGA')->get();
  }

  /**
   * Get a specific storage
   */
  public function getStorage($bodega) {
    return \DB::table('SOCOCO.BODEGA')->where('BODEGA', $bodega)->get();
  }
}
