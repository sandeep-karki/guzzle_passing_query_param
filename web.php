<?php

use App\Resturant;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

define("PREFIX", env('PREFIX','system'));
define("BACKEND", env('BACKEND', 'system'));
define("PAGINATION", env('PAGINATION',20));


Route::get('/', function (Request $request) {

	$resturantList= Resturant::get();
	// dd($resturantList);
	foreach ($resturantList as $vendor) {
		// dd($vendor);
		$client = new Client();
		$request = $client->request('GET','https://nepal.pointnemo.info/api/v1/location-info', [
			'query' => [
				'key'      =>    '$2y$10$/KHWOao9vs7CNWg7U68xB.0IFJ8TjC74k9ABVOnWWL/oaCtkNENY2',
				'latitude'  =>	 $vendor->latitude, 
				'longitude' => 	 $vendor->longitude
			]
		]);
		$response = $request->getBody()->getContents();
		// dd($response);
		$datas = json_decode($response, TRUE);
		// dd($datas);
		foreach ($datas['data']['admin_data'] as $data) {
			// dd($data);
			if($data['type'] == "country"){
				$vendor['country'] = $data['name'];
			}
			if($data['type'] == "provience"){
				$vendor['provience'] = $data['name'];
			}
			if($data['type'] == "district"){
				$vendor['district'] = $data['name'];
			}
			if($data['type'] == "local_government"){
				$vendor['local_government'] = $data['name'];
			}
			if($data['type'] == "ward"){
				$vendor['ward'] = $data['name'];
			}
			if($data['type'] == "street_address"){
				$vendor['street_name'] = $data['name'];
			}
			if($data['type'] == "country"){
				$vendor['country'] = $data['name'];
			}
			$vendor->save();
		}
	}
});

Route::get('terms-condition','Admin\home\homeController@getTermsAndCondition');
Route::get('about-us','Admin\home\homeController@getAboutUs');
Route::get('privacy-policy','Admin\home\homeController@getPrivacyPolicy');


Route::get('system','Auth\LoginController@showLoginForm')->name('login');
Route::get('/system/login','Auth\LoginController@showLoginForm')->name('login');

Route::get('/system/verify','Auth\LoginController@verify');
////Route::get('/','Auth\LoginController@showLoginForm');
//Route::post('/system/login','Auth\LoginController@login')->middleware('throttle:5');
Route::post('/system/login','Auth\LoginController@login')->middleware(['throttle:5','log']);
Route::get('/system/logout','Auth\LoginController@logout');
Route::post('/system/verification','Auth\LoginController@verification');
Route::get('/logout','Auth\LoginController@logout');
//
Route::get('/resetpassword/{token}/{userid}','frontend\resetpassword\resetpasswordController@index');
Route::post('/resetpassword/updatepassword','frontend\resetpassword\resetpasswordController@resetUserPassword');

Route::get('/forgotpassword','frontend\resetpassword\recoverpasswordController@index');
Route::post('/forgotpassword/recover','frontend\resetpassword\recoverpasswordController@recoverpassword');

// Route::get('/newslist','Auth\LoginController@newslist');

Route::get('/newslist', function () {
    $exitCode = Artisan::call('baahrakhari:handle'
    );
});

include('backend.php');