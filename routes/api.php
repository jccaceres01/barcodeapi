<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Route Resources for articulos
 */
Route::resource('articulos', 'Api\ArticulosController');
Route::get('search/articulos', 'Api\ArticulosController@customSearch')
  ->name('articulos.search');
Route::get('search/articuloscodigo','Api\ArticulosController@customSearchWithBarcode')
  ->name('articulos.search');
Route::get('buscar', 'Api\OutPutsController@lookupBybarcode');

/**
 * Routes for stock outputs
 */
Route::get('outputs', 'Api\OutputsController@getOutputs'); // Get outputs
Route::post('outputs', 'Api\OutputsController@createOutputs'); // Create outputs

/**
 * Routes for stock outputs lines
 */
Route::get('outputlines/{documento_inv}',
  'Api\OutputsController@getOutputsLines'); // Get output Lines of a output
Route::post('outputline',
  'Api\OutputsController@createLine'); // Create a new Line for a output
// Delete output line
Route::delete('outputlines', 'Api\OutPutsController@delOutputsLines');

/**
 * Routes for LOCALIZACION
 */
// All Locations
Route::get('localizacion', 'Api\LocalizacionController@index');
// get specific location
Route::get('localizacion/{bodega}/{localizacion}',
  'Api\LocalizacionController@getSpecificLocation');
// Localizacion from a specific storage
Route::get('localizacion/{bodega}',
  'Api\LocalizacionController@getLocationFromStorage');

/**
 * Routes for Storages
 */
Route::get('bodega', 'Api\BodegaController@index');
Route::get('bodega/{bodega}', 'Api\BodegaController@getStorage');

/**
 * Routes for Stocks levels of an item
 */

 // Get lote stock for the given item as parameter
Route::get('stock/lote', 'Api\ArticulosController@loteStockLevel');


/**
 * Routes for stock lote exists
 */
Route::get('lote', 'Api\ExistenciaLoteController@index')->name('lote.index');
Route::post('lote', 'Api\ExistenciaLoteController@store')->name('lote.store');
Route::get('lote/{bodega}/{articulo}/{localizacion}',
  'Api\ExistenciaLoteController@show')->name('lote.show');
Route::match(['put', 'patch'], 'lote/{bodega}/{articulo}/{localizacion}',
  'Api\ExistenciaLoteController@update')->name('lote.update');
Route::delete('lote/{bodega}/{articulo}/{localizacion}',
  'Api\ExistenciaLoteController@destroy')->name('lote.destroy');
  
/**
 * Routes for phisical inventory ticket
 */
Route::get('inventory/ticket',
  'Api\PhisicalInventoryTicketController@index');
Route::post('inventory/ticket',
  'Api\PhisicalInventoryTicketController@store');
Route::get('inventory/ticket/{inventory_ticket}',
  'Api\PhisicalInventoryTicketController@show');
Route::delete('inventory/ticket/{inventory_ticket}',
  'Api\PhisicalInventoryTicketController@delete');
