<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocalizacionController extends Controller
{
  /**
   * Get all locations
   */
  public function index() {
    return \DB::table('SOCOCO.LOCALIZACION')->get();
  }

  /**
   * Get a specific location
   */
  public function getSpecificLocation($bodega, $localizacion) {
    return \DB::table('SOCOCO.LOCALIZACION')->where('BODEGA', $bodega)
      ->where('LOCALIZACION', $localizacion)->get();
  }

  /**
   * Get Locations from a storage
   */
  public function getLocationFromStorage($bodega) {
    return \DB::table('SOCOCO.LOCALIZACION')
      ->where('BODEGA', $bodega)
      ->orderBy('BODEGA', 'DESC')
      ->get();
  }
}
