<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::match(['GET', 'POST'], 'login', [
    'as'   => 'login',
    'uses' => 'HomeController@login'
]);

Route::match(['POST'], 'register', [
    'as'   => 'register',
    'uses' => 'HomeController@register'
]);

Route::match(['GET'], 'logout', [
    'as'   => 'logout',
    'uses' => function () {
        Auth::logout();
        return Redirect::route('home');
    }
]);

Route::group([
    'before' => 'auth'
], function () {
    Route::get('/', [
        'as'   => 'home',
        'uses' => 'HomeController@index'
    ]);

    Route::get('/main', [
        'as'   => 'main',
        'uses' => 'HomeController@main'
    ]);

    Route::group([
        'prefix' => 'advice'
    ], function () {

        Route::get('', [
            'as'   => 'advice',
            'uses' => 'AdviceController@index'
        ]);

        Route::post('function', [
            'as'   => 'advice.function',
            'uses' => 'AdviceController@adviceFunction'
        ]);

        Route::get('view/{id}/{idQuestion?}', [
            'as' => 'advice.view',
            'uses' => 'AdviceController@view'
        ]);

        Route::get('tour/{idAdvice}', [
            'as' => 'tour.view',
            'uses' => 'AdviceController@viewTours'
        ]);

        Route::get('/ontology', [
            'as' => 'ontology',
            'uses' => 'OntologyController@index'
        ]);

    });
});

Route::group([
    'prefix' => 'api'
], function () {
    Route::group([
        'before' => 'jwt'
    ], function () {
        Route::get('/', [
            'as' => 'api.index',
            'uses' => 'ApiController@index'
        ]);
    });



    Route::post('/login', [
        'as' => 'api.login',
        'uses' => 'ApiController@login'
    ]);
});

