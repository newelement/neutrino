<?php
$namespacePrefix = '\\'.config('neutrino.controllers.namespace').'\\';

Route::get('/', $namespacePrefix.'ContentController@guessContent');

Route::post('/neutrino-form', $namespacePrefix.'ContentController@submitForm');
Route::post('/neutrino-comment', $namespacePrefix.'ContentController@submitComment');
Route::post('/neutrino-comment-vote', $namespacePrefix.'ContentController@submitCommentVote');

Route::get('email-reset-password', $namespacePrefix.'NeutrinoAuthController@getEmailResetPassword');
Route::post('email-reset-password', $namespacePrefix.'NeutrinoAuthController@emailResetPassword');
Route::get('reset-password/{email}/{token}', $namespacePrefix.'NeutrinoAuthController@getResetPassword');
Route::post('reset-password', $namespacePrefix.'NeutrinoAuthController@resetPassword');

Route::get('register', $namespacePrefix.'NeutrinoAuthController@getRegister')->name('register');
Route::post('register', $namespacePrefix.'NeutrinoRegisterController@create')->name('register');

Route::get('/{any}', $namespacePrefix.'ContentController@guessContent')->where('any', '.*'); // Yeah seriously
Route::group(['prefix' => 'api', 'middleware' => ['api']], function () use ( $namespacePrefix ) {
	Route::get('/{any}', $namespacePrefix.'ContentController@guessContent')->where('any', '.*');
});
