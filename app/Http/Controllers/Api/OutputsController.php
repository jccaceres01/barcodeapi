<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Articulos;

class OutputsController extends Controller
{
  /**
   * Get Outputs
   */
  public function getOutputs() {
    return \DB::table('SOCOCO.DOCUMENTO_INV')->where('CONSECUTIVO', 'SALIDA')
      ->where('PAQUETE_INVENTARIO', 'SAL')
      ->whereNull('APROBADO')
      ->get();
  }

  /**
   * Create new Output
   */
  public function createOutputs(Request $request) {
    $maskedCons = \DB::table('SOCOCO.CONSECUTIVO_CI')
      ->where('CONSECUTIVO', 'SALIDA')
      ->pluck('SIGUIENTE_CONSEC')->first();

    $cons = substr($maskedCons, strpos($maskedCons, '-') + 1,
      strlen($maskedCons));

    $nextCons = 'SAL-'.str_pad($cons+1, 6, '0', STR_PAD_LEFT);

    $data = [
      'REFERENCIA' => $request->REFERENCIA,
      'PAQUETE_INVENTARIO' => 'SAL',
      'DOCUMENTO_INV' => 'SAL-'.$cons,
      'FECHA_HOR_CREACION' => new \DateTime('now'),
      'FECHA_DOCUMENTO' => new \DateTime('now'),
      'SELECCIONADO' => 'N',
      'USUARIO' => 'SA',
      'CONSECUTIVO' => 'SALIDA'
    ];

    \DB::beginTransaction();
    try {
      \DB::table('SOCOCO.CONSECUTIVO_CI')->where('CONSECUTIVO', 'SALIDA')
        ->update([
          'ULTIMO_USUARIO' => 'SA',
          'SIGUIENTE_CONSEC' => $nextCons
        ]);

      \DB::table('SOCOCO.DOCUMENTO_INV')->insert($data);
      // Get the new output
      $output = \DB::table('SOCOCO.DOCUMENTO_INV')
        ->where('DOCUMENTO_INV', 'SAL-'.$cons)
        ->get();

      \DB::commit();
      return response()->json($output, 200);
    } catch (\Exception $e) {
      \DB::rollback();
      \Log::info($e);
      abort(500);
    }
  }

  /**
   * Get Output Lines
   */
  public function getOutputsLines($documento_inv) {
    return \DB::table('SOCOCO.LINEA_DOC_INV')
      ->join('SOCOCO.ARTICULO','ARTICULO.ARTICULO','=','LINEA_DOC_INV.ARTICULO')
      ->where('PAQUETE_INVENTARIO', 'SAL')
      ->where('DOCUMENTO_INV', $documento_inv)->get();
  }

  /**
   * Create new output line
   */
  public function createLine(Request $request) {
    $lineNumber = \DB::table('SOCOCO.LINEA_DOC_INV')
      ->where('PAQUETE_INVENTARIO', 'SAL')
      ->where('DOCUMENTO_INV', $request->documento_inv)
      ->max('LINEA_DOC_INV') + 1;

    $data = [
      'PAQUETE_INVENTARIO' => 'SAL',
      'DOCUMENTO_INV' => $request->documento_inv,
      'ARTICULO' => $request->articulo,
      'BODEGA' => $request->bodega,
      'LOCALIZACION' => $request->localizacion,
      'CANTIDAD' => $request->cantidad,
      'LINEA_DOC_INV' => $lineNumber,
      'AJUSTE_CONFIG' => '~CC~',
      'NIT' => '130412618',
      'TIPO' => 'C',
      'SUBTIPO' => 'R',
      'SUBSUBTIPO' => 'N',
      'COSTO_TOTAL_LOCAL' => 0,
      'COSTO_TOTAL_DOLAR' => 0,
      'PRECIO_TOTAL_LOCAL' => 0,
      'PRECIO_TOTAL_DOLAR' => 0,
      'BODEGA_DESTINO' => null,
      'LOCALIZACION_DEST' => null,
      'CENTRO_COSTO' => $request->centro_costo,
      'SECUENCIA' => null,
      'SERIE_CADENA' => null,
      'UNIDAD_DISTRIBUCIO' => null,
      'CUENTA_CONTABLE' => '5-01-01-001-000',
      'COSTO_TOTAL_LOCAL_COMP' => 0,
      'COSTO_TOTAL_DOLAR_COMP' => 0
    ];

    $existenciaLote = null;
    $existenciaBodega = null;
    // $existenciaReserva = null;

    // Find existencia lote
    $existenciaLote = \DB::table('SOCOCO.EXISTENCIA_LOTE')
      ->where('ARTICULO', $request->articulo)
      ->where('BODEGA', $request->bodega)
      ->where('LOCALIZACION', $request->localizacion)
      ->where('LOTE', 'ND')->first();
    if ($existenciaLote == null) return response()->json('noExistsLote');

    //  Find existencia bodega
    $existenciaBodega = \DB::table('SOCOCO.EXISTENCIA_BODEGA')
      ->where('ARTICULO', $request->articulo)
      ->where('BODEGA', $request->bodega)->first();
    if ($existenciaBodega == null) return response()->json('noExistsBodega');

    if ($request->cantidad <= $existenciaLote->CANT_DISPONIBLE) {
      \DB::beginTransaction();
      try {
        // Update ExistenciaLote
        \DB::table('SOCOCO.EXISTENCIA_LOTE')
          ->where('ARTICULO',  $request->articulo)
          ->where('LOTE', 'ND')->where('LOCALIZACION', $request->localizacion)
          ->where('BODEGA', $request->bodega)
          ->update([
            'CANT_DISPONIBLE' =>
              $existenciaLote->CANT_DISPONIBLE - $request->cantidad,
            'CANT_RESERVADA' =>
              $existenciaLote->CANT_RESERVADA + $request->cantidad
          ]);

        // Update Existencia Bodega
        \DB::table('SOCOCO.EXISTENCIA_BODEGA')
          ->where('BODEGA', $request->bodega)
          ->where('ARTICULO', $request->articulo)
          ->update([
            'CANT_DISPONIBLE' =>
              $existenciaBodega->CANT_DISPONIBLE - $request->cantidad,
            'CANT_RESERVADA' =>
              $existenciaLote->CANT_RESERVADA + $request->cantidad
          ]);

        // Create or update existencia reserva
        if (( $existenciaReserva = \DB::table('SOCOCO.EXISTENCIA_RESERVA')
          ->where('ARTICULO', $request->articulo)
          ->where('APLICACION', $request->documento_inv)
          ->where('BODEGA', $request->bodega)
          ->where('LOTE', 'ND')
          ->where('LOCALIZACION', $request->localizacion)
          ->first()) == true) {
            \DB::table('SOCOCO.EXISTENCIA_RESERVA')
              ->where('ARTICULO', $request->articulo)
              ->where('APLICACION', $request->documento_inv)
              ->where('BODEGA', $request->bodega)
              ->where('LOTE', 'ND')
              ->where('LOCALIZACION', $request->localizacion)
              ->update([
                'CANTIDAD' => $existenciaReserva->CANTIDAD + $request->cantidad
              ]);
        } else {
          \DB::table('SOCOCO.EXISTENCIA_RESERVA')->insert([
            'ARTICULO' => $request->articulo,
            'APLICACION' => $request->documento_inv,
            'BODEGA' => $request->bodega,
            'LOTE' => 'ND',
            'LOCALIZACION' => $request->localizacion,
            'MODULO_ORIGEN' => 'CI',
            'CANTIDAD' => $request->cantidad,
            'USUARIO' => 'SA',
            'FECHA_HORA' => new \DateTime('now')
          ]);
        }
        //  Insert new lines for documento_inv
        \DB::table('SOCOCO.LINEA_DOC_INV')->insert($data);

        \DB::commit();
        return response()->json('inserted');
      } catch (\Exception $e) {
        switch ($e->getCode()) {
          default:
            \DB::rollback();
            \Log::info($e);
            abort(500);
        }
      }
    } else {
      // the quantity is more than the existence
      return response()->json('exceeded');
    }
  }

  /**
   * Delete outputs Lines
   */
    public function delOutputsLines(Request $request) {

    $existenciaLote = null;
    $existenciaBodega = null;
    $existenciaReserva = null;

    // Find existencia lote
    if (($existenciaLote = \DB::table('SOCOCO.EXISTENCIA_LOTE')
      ->where('ARTICULO', $request->articulo)
      ->where('BODEGA', $request->bodega)
      ->where('LOCALIZACION', $request->localizacion)
      ->where('LOTE', 'ND')->first()) != true) {
        return response()->json('noExistsLote');
      }

    //  Find existencia bodega
    if (($existenciaBodega = \DB::table('SOCOCO.EXISTENCIA_BODEGA')
      ->where('ARTICULO', $request->articulo)
      ->where('BODEGA', $request->bodega)->first()) != true) {
        return response()->json('noExistsBodega');
      }

    // Find existencia reserva
    if (($existenciaReserva = \DB::table('SOCOCO.EXISTENCIA_RESERVA')
      ->where('ARTICULO', $request->articulo)
      ->where('APLICACION', $request->documento_inv)
      ->where('BODEGA', $request->bodega)
      ->where('LOTE', 'ND')
      ->where('LOCALIZACION', $request->localizacion)->first()) != true) {
        return response()->json('noExistsReserva');
      }

    \DB::beginTransaction();
    try {
      // Update existencia lote
      \DB::table('SOCOCO.EXISTENCIA_LOTE')
        ->where('ARTICULO', $request->articulo)
        ->where('BODEGA', $request->bodega)
        ->where('LOCALIZACION', $request->localizacion)
        ->where('LOTE', 'ND')
        ->update([
          'CANT_DISPONIBLE' =>
            $existenciaLote->CANT_DISPONIBLE + $request->cantidad,
          'CANT_RESERVADA' =>
            $existenciaLote->CANT_RESERVADA - $request->cantidad
        ]);

      // Update existencia bodega
      \DB::table('SOCOCO.EXISTENCIA_BODEGA')
        ->where('ARTICULO', $request->articulo)
        ->where('BODEGA', $request->bodega)
        ->update([
          'CANT_DISPONIBLE' =>
            $existenciaLote->CANT_DISPONIBLE + $request->cantidad,
          'CANT_RESERVADA' =>
            $existenciaLote->CANT_RESERVADA - $request->cantidad
        ]);

      if (($existenciaReserva->CANTIDAD - $request->cantidad) == 0) {
        \DB::table('SOCOCO.EXISTENCIA_RESERVA')
          ->where('ARTICULO', $request->articulo)
          ->where('APLICACION', $request->documento_inv)
          ->where('BODEGA', $request->bodega)
          ->where('LOTE', 'ND')
          ->where('LOCALIZACION', $request->localizacion)->delete();
      } else {
        \DB::table('SOCOCO.EXISTENCIA_RESERVA')
          ->where('ARTICULO', $request->articulo)
          ->where('APLICACION', $request->documento_inv)
          ->where('BODEGA', $request->bodega)
          ->where('LOTE', 'ND')
          ->where('LOCALIZACION', $request->localizacion)
          ->update([
            'CANTIDAD' => $existenciaReserva->CANTIDAD - $request->cantidad
          ]);
      }
      // Delete Line
      \DB::table('SOCOCO.LINEA_DOC_INV')
        ->where('PAQUETE_INVENTARIO', 'SAL')
        ->where('DOCUMENTO_INV', $request->documento_inv)
        ->where('LINEA_DOC_INV', $request->linea)
        ->delete();
      \DB::commit();
      return response()->json('lineDroped');

    } catch (\Exception $e) {
      switch ($e->getCode()) {
        default:
          \DB::rollback();
          \Log::info($e);
          abort(500);
          break;
      }
    }
  }

  /**
   * Find by Barcode
   */
  public function lookupBybarcode(Request $request) {
    return Articulos::where('CODIGO_BARRAS_INVT', $request->barcode)
      ->whereNotNull('CODIGO_BARRAS_INVT')
      ->where('CODIGO_BARRAS_INVT', '!=', '')
      ->get();
  }
}
