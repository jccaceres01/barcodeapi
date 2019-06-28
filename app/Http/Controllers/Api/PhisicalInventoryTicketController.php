<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhisicalInventoryTicketController extends Controller
{
  /**
   * Get all inventry tickets
   */
  public function index() {
    return \DB::table('SOCOCO.BOLETA_INV_FISICO')
      ->select(
        'BOLETA_INV_FISICO.BOLETA',
        'BOLETA_INV_FISICO.ARTICULO',
        'BOLETA_INV_FISICO.BODEGA',
        'BOLETA_INV_FISICO.LOCALIZACION',
        'BOLETA_INV_FISICO.LOTE',
        'BOLETA_INV_FISICO.CANT_DISP_RESERV',
        'BOLETA_INV_FISICO.CANT_NO_APROBADA',
        'BOLETA_INV_FISICO.CANT_VENCIDA',
        'BOLETA_INV_FISICO.USUARIO',
        'BOLETA_INV_FISICO.FECHA_HORA',
        'BOLETA_INV_FISICO.VALIDADA',
        'BOLETA_INV_FISICO.SERIE_CADENA_DISP',
        'BOLETA_INV_FISICO.SERIE_CADENA_NOAPR',
        'BOLETA_INV_FISICO.SERIE_CADENA_VENC',
        'ARTICULO.DESCRIPCION',
      )->join('SOCOCO.ARTICULO', 'SOCOCO.ARTICULO.ARTICULO', '=',
        'SOCOCO.BOLETA_INV_FISICO.ARTICULO')->get();
  }

  /**
   * Get specific phisical inventory ticket
   */
  public function show($inventory_ticket) {
    return \DB::table('SOCOCO.BOLETA_INV_FISICO')
      ->where('BOLETA', $inventory_ticket)->get();
  }

  /**
   * Create new phisical Inventory ticket
   */
  public function store(Request $request) {

    if (count(\DB::table('SOCOCO.BOLETA_INV_FISICO')
        ->where('ARTICULO', $request->articulo)
        ->where('BODEGA', $request->bodega)
        ->where('LOCALIZACION', $request->localizacion)
        ->get()) > 0) {

      return response()->json('exists');
    } else {
      // last ticket in sequence
      $lastTicket = \DB::table('SOCOCO.BOLETA_INV_FISICO')
        ->orderBy('BOLETA', 'DESC')->first()->BOLETA + 1;

      // Compute the next ticket in sequence
      $nextTicket = str_pad($lastTicket, 8, '0', STR_PAD_LEFT);

      // Data to insert
      $data = [
        'BOLETA' => $nextTicket,
        'ARTICULO' => $request->articulo,
        'BODEGA' => $request->bodega,
        'LOCALIZACION' => $request->localizacion,
        'LOTE' => 'ND',
        'CANT_DISP_RESERV' => $request->cant,
        'CANT_NO_APROBADA' => 0,
        'CANT_VENCIDA' => 0,
        'USUARIO' => 'SA',
        'FECHA_HORA' => new \DateTime('now'),
        'VALIDADA' => 'N',
        'SERIE_CADENA_DISP' => null,
        'SERIE_CADENA_NOAPR' => null,
        'SERIE_CADENA_VENC' => null
      ];

      \DB::beginTransaction();

      try {
        // Insert the new phisical inventory ticket
        \DB::table('SOCOCO.BOLETA_INV_FISICO')->insert($data);

        // Update EXISTENCIA_BODEGA if fecha_descong is present
        if ($request->fecha_descong != null) {
          \DB::table('SOCOCO.EXISTENCIA_BODEGA')
            ->where('ARTICULO', $request->articulo)
            ->where('BODEGA', $request->bodega)
            ->update([
              'CONGELADO' => 'S',
              'FECHA_CONG' => new \DateTime('now'),
              'BLOQUEA_TRANS' => 'S',
              'FECHA_DESCONG' => $request->fecha_descong
            ]);
          }

          \DB::commit();
          return response()->json('added');
        } catch (\Exception $e) {
          switch ($e->getCode()) {
            default:
              \DB::rollback();
              info($e);
              abort(500);
        }
      }
    }
  }

  /**
   * Delete inventory ticket
   */
  public function delete($inventory_ticket) {
    \DB::beginTransaction();

    try {
      \DB::table('SOCOCO.BOLETA_INV_FISICO')
        ->where('BOLETA', $inventory_ticket)->delete();
      \DB::commit();
      return response()->json(true);
    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          \DB::rollback();
          info($e);
          abort(500);
      }
    }
  }
}
