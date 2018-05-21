<?php
Auth::routes();

Route::group(['middleware' => 'auth'], function()
{
    Route::get('/', function(){
    	return Redirect('admin/home');
    });
});

Route::group(['namespace' => 'Admin' , 'middleware' => ['auth']], function () {


});

Route::group(['prefix' => 'user' , 'middleware' => ['auth']], function () {
    Route::get('/index', 'UserController@getUsers');
    Route::post('/set_status/{id}', 'UserController@setStatus')->where('id', '[0-9]+');
    Route::post('/del/{id}', 'UserController@del')->where('id', '[0-9]+');
    Route::post('/edit', 'UserController@edit');
    Route::post('/add', 'UserController@add');
});

//用户
/*Route::group(['prefix' => 'user', 'middleware'=>'authority'], function () {
    Route::get('/index', 'UserController@getUsers');
    Route::post('/set_status/{id}', 'UserController@setStatus')->where('id', '[0-9]+');;
    Route::post('/del/{id}', 'UserController@del')->where('id', '[0-9]+');;
    Route::post('/edit/{id}', 'UserController@edit')->where('id', '[0-9]+');;
    Route::post('/add', 'UserController@add');
});*/

//角色
/*Route::group(['prefix' => 'role', 'middleware'=>'authority'], function () {
    Route::post('/add', 'RoleController@add');//
    Route::get('/index', 'RoleController@getRoles');//
    Route::post('/edit/{id}', 'RoleController@edit')->where('id', '[0-9]+');//
    Route::post('/del/{id}', 'RoleController@del')->where('id', '[0-9]+');//
    Route::get('/authorities/{id}', 'RoleController@getAuthorityTree')->where('id', '[0-9]+');
    Route::post('/edit_authorities/{id}', 'RoleController@editAuthorities')->where('id', '[0-9]+');
});*/

/*//权限
Route::group(['prefix' => 'authority', 'middleware'=>'authority'], function () {
    Route::post('/add', 'AuthorityController@add');//
    Route::get('/index', 'AuthorityController@getAuthorities');//
    Route::post('/edit/{id}', 'AuthorityController@edit')->where('id', '[0-9]+');//
    Route::post('/del/{id}', 'AuthorityController@del')->where('id', '[0-9]+');//
    Route::post('/modules/{id}', 'AuthorityController@getChildrenModules')->where('id', '[0-9]+');//
});*/

Route::get('/home', 'HomeController@index')->name('home');
