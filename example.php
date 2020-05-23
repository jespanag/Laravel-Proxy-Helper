<?php

use App\Helpers\ProxyHelperFacade;
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

Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'proxy/{slug}', function(Request $request){

    return ProxyHelperFacade::CreateProxy($request)
            ->withHeaders(['x-proxy' => 'laravel'])
            ->withToken('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6Imp1YW5AZ21haWwuY29tIiwic2oxNTkwMTkyNTE1fQ.db5AZuw3eSAHqjdaRn9AZX8LPbNAxPmuO8BZlEmIGk4')
            ->preserveQuery(true)
            ->toHost('http://dockerserver.test','api/proxy');

})->where('slug', '([A-Za-z0-9\-\/]+)');
