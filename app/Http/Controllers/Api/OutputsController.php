<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Articulos;

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
      \DB::commit();
      return response()->json(true);
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

    return response()->json(\DB::table('SOCOCO.LINEA_DOC_INV')->insert($data));
  }

  /**
   * Delete outputs Lines
   */
  public function delOutputsLines($documento_inv, $linea_doc_inv) {
    return \DB::table('SOCOCO.LINEA_DOC_INV')
      ->where('PAQUETE_INVENTARIO', 'SAL')
      ->where('DOCUMENTO_INV', $documento_inv)
      ->where('LINEA_DOC_INV', $linea_doc_inv)
      ->delete();
  }

  /**
   * Find by Barcode
   */
    public function lookupBybarcode($barcode) {
      return Articulos::where('CODIGO_BARRAS_VENT', $barcode)->get();
      //return Articulo::where('CODIGO_BARRAS_VENT', $barcode)->first();
  }
}
