<?php

Route::get('/', 'MainController@main');
Route::post('match', 'MainController@getMatches')->name('match');
Route::post('updateMatchesData', 'MainController@updateMatches')->name('updateMatches');
