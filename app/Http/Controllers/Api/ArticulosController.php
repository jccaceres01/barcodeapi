<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Articulos;

class ArticulosController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return Articulos::paginate(10);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {

  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    return Articulos::create($request->all());
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    return Articulos::find($id);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {

  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $art = Articulos::findOrFail($id);
    return response()->json($art->update($request->all()));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    return Articulos::destroy($id);
  }

  /**
   * Custom search
   */
  public function customSearch(Request $request) {
    return Articulos::where('DESCRIPCION', 'like', '%'.$request->criteria.'%')
      ->orWhere('ARTICULO', 'like', '%'.$request->criteria.'%')
      ->get();
  }
  /**
   * Search by Description or Barcode
   */
  public function customSearchWithBarcode(Request $request) {
    return Articulos::where('DESCRIPCION', 'like', '%'.$request->criteria.'%')
      ->orWhere('ARTICULO', 'like', '%'.$request->criteria.'%')
      ->orWhere('CODIGO_BARRAS_VENT', 'like', '%'.$request->criteria.'%')
      ->orWhere('CODIGO_BARRAS_INVT', 'like', '%'.$request->criteria.'%')
      ->get();
  }

  /**
   * Get the stock of a item in lote
   */
  public function loteStockLevel(Request $request) {
    return $loteStockLevel = \DB::table('SOCOCO.EXISTENCIA_LOTE')
      ->where('ARTICULO', $request->articulo)
      ->orderBy('BODEGA', 'ASC')
      ->orderBy('LOCALIZACION', 'ASC')
      ->get();
  }
}
