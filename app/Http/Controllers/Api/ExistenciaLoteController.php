<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ExistenciaLoteController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    try {

      return \DB::table('SOCOCO.EXISTENCIA_LOTE')
      ->join('SOCOCO.ARTICULO', 'SOCOCO.EXISTENCIA_LOTE.ARTICULO', '=', 'SOCOCO.ARTICULO.ARTICULO')
      ->select(
        'SOCOCO.EXISTENCIA_LOTE.BODEGA',
        'SOCOCO.EXISTENCIA_LOTE.ARTICULO',
        'SOCOCO.ARTICULO.DESCRIPCION',
        'SOCOCO.EXISTENCIA_LOTE.LOCALIZACION',
        'SOCOCO.ARTICULO.CLASIFICACION_2',
        'SOCOCO.EXISTENCIA_LOTE.CANT_DISPONIBLE',
        'SOCOCO.EXISTENCIA_LOTE.CANT_RESERVADA'
      )->get();

    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          info($e);
          return abort(500);
      }
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      return response()->json(\DB::table('SOCOCO.EXISTENCIA_LOTE')
        ->insert($request->all()));
    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          info($e);
          abort(500);
      }
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($bodega, $articulo, $localizacion)
  {
    try {
      return response()->json(\DB::table('SOCOCO.EXISTENCIA_LOTE')->where('BODEGA', $bodega)
        ->where('ARTICULO', $articulo)
        ->where('LOCALIZACION', $localizacion)
        ->where('LOTE', 'ND')->get());
    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          info($e);
          abort(500);
      }
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $bodega, $articulo, $localizacion)
  {
    try {
      return response()->json(\DB::table('SOCOCO.EXISTENCIA_LOTE')
        ->where('BODEGA', $bodega)->where('ARTICULO', $articulo)
        ->where('LOCALIZACION', $localizacion)->where('LOTE', 'ND')
        ->update($request->all()));
    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          info($e);
          abort(500);
      }
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($bodega, $articulo, $localizacion)
  {
    try {
      return response()->json(\DB::table('SOCOCO.EXISTENCIA_LOTE')->where('ARTICULO', $articulo)
        ->where('LOCALIZACION', $localizacion)->where('LOTE', 'ND')->delete());
    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          info($e);
          abort(500);
      }
    }
  }
}
