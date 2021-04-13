<?php
$namespacePrefix = '\\'.config('neutrino.controllers.namespace').'\\';

Route::get('login', ['uses' => $namespacePrefix.'NeutrinoAuthController@login', 'as' => 'login']);
Route::post('login', ['uses' => $namespacePrefix.'NeutrinoAuthController@postLogin']);
Route::post('logout', ['uses' => $namespacePrefix.'NeutrinoController@logout'])->name('logout');

Route::group(['prefix' => 'admin', 'as' => 'neutrino.'], function () use ( $namespacePrefix ){

    Route::group(['middleware' => 'admin.user'], function () use ( $namespacePrefix ) {

        Route::get('/', ['uses' => $namespacePrefix.'NeutrinoController@index', 'as' => 'dashboard']);
		Route::get('/dashboard', ['uses' => $namespacePrefix.'NeutrinoController@index', 'as' => 'dashboard.dashboard']);

        Route::group([ 'as' => 'pages.'], function () use ( $namespacePrefix ) {

    		Route::get('/pages', $namespacePrefix.'Admin\PageController@index')->name('all');
            Route::get('/pages/sort', $namespacePrefix.'Admin\PageController@getsort')->name('sort');
            Route::post('/pages/sort', $namespacePrefix.'Admin\PageController@updateSort');
    		Route::get('/page/{id}', $namespacePrefix.'Admin\PageController@get')->name('edit');
    		Route::get('/page', $namespacePrefix.'Admin\PageController@getCreate')->name('show');
    		Route::post('/pages', $namespacePrefix.'Admin\PageController@create');
    		Route::put('/page/{id}', $namespacePrefix.'Admin\PageController@update');
    		Route::delete('/pages/{id}', $namespacePrefix.'Admin\PageController@delete');
    		Route::get('/pages-trash', $namespacePrefix.'Admin\PageController@getTrash')->name('trash');
    		Route::get('/pages/recover/{id}', $namespacePrefix.'Admin\PageController@recover')->name('recover');
    		Route::get('/pages/destroy/{id}', $namespacePrefix.'Admin\PageController@destroy')->name('destroy');
            Route::get('/pages-duplicate/{page}', $namespacePrefix.'Admin\PageController@duplicate')->name('duplicate');

        });

        Route::group([ 'as' => 'entries.'], function () use ( $namespacePrefix ) {

    		Route::get('/entries', $namespacePrefix.'Admin\EntryController@index')->name('all');
    		Route::get('/entry/{id}', $namespacePrefix.'Admin\EntryController@get')->name('edit');
    		Route::get('/entry', $namespacePrefix.'Admin\EntryController@getCreate')->name('show');
    		Route::post('/entries', $namespacePrefix.'Admin\EntryController@create');
    		Route::put('/entry/{id}', $namespacePrefix.'Admin\EntryController@update');
    		Route::post('/entry/terms/remove', $namespacePrefix.'Admin\EntryController@removeEntryTerm');
    		Route::delete('/entries/{id}', $namespacePrefix.'Admin\EntryController@delete');
    		Route::get('/entries-trash', $namespacePrefix.'Admin\EntryController@getTrash')->name('trash');
    		Route::get('/entries/recover/{id}', $namespacePrefix.'Admin\EntryController@recover')->name('recover');
    		Route::get('/entries/destroy/{id}', $namespacePrefix.'Admin\EntryController@destroy')->name('destroy');

            Route::get('/entry-types', $namespacePrefix.'Admin\EntryController@indexEntryTypes')->name('types-list');
            Route::post('/entry-types', $namespacePrefix.'Admin\EntryController@createEntryType');
            Route::get('/entry-types/{id}', $namespacePrefix.'Admin\EntryController@getEditEntryType')->name('entry-type');
            Route::post('/entry-types/{id}', $namespacePrefix.'Admin\EntryController@updateEntryType');
            Route::delete('/entry-types/{id}', $namespacePrefix.'Admin\EntryController@deleteEntryType');

            Route::group([ 'as' => 'comments.'], function () use ( $namespacePrefix ) {

                Route::get('/moderate-comments', $namespacePrefix.'Admin\CommentController@moderateComments')->name('list');
                Route::get('/comments', $namespacePrefix.'Admin\CommentController@all')->name('all');
                Route::post('/comment/{id}', $namespacePrefix.'Admin\CommentController@replyComment');
                Route::delete('/comment/{id}', $namespacePrefix.'Admin\CommentController@deleteComment');
                Route::get('/comment/{id}/approve', $namespacePrefix.'Admin\CommentController@approveComment')->name('approve');

            });

        });

        Route::group([ 'as' => 'taxonomies.'], function () use ( $namespacePrefix ) {

    		Route::get('/taxonomy-types', $namespacePrefix.'Admin\TaxonomiesController@indexTypes')->name('list');
    		Route::post('/taxonomy-types', $namespacePrefix.'Admin\TaxonomiesController@createType');
    		Route::get('/taxonomy-types/{id}', $namespacePrefix.'Admin\TaxonomiesController@getEditType')->name('taxonomy-types');
    		Route::post('/taxonomy-types/{id}', $namespacePrefix.'Admin\TaxonomiesController@updateType');
    		Route::delete('/taxonomy-types/{id}', $namespacePrefix.'Admin\TaxonomiesController@deleteType');
    		Route::get('/taxonomies/{id}', $namespacePrefix.'Admin\TaxonomiesController@index')->name('edit');
    		Route::post('/taxonomies/{id}', $namespacePrefix.'Admin\TaxonomiesController@create');
    		Route::get('/taxonomies/{type}/{id}', $namespacePrefix.'Admin\TaxonomiesController@getEdit')->name('type-edit');
    		Route::post('/taxonomies/{type}/{id}', $namespacePrefix.'Admin\TaxonomiesController@update');
    		Route::delete('/taxonomies/{type}/{id}', $namespacePrefix.'Admin\TaxonomiesController@delete');
            Route::post('/sort/terms', $namespacePrefix.'Admin\TaxonomiesController@sortTerms');
            Route::post('/sort/taxonomy', $namespacePrefix.'Admin\TaxonomiesController@sortTaxonomy');

        });

		Route::get('/media', $namespacePrefix.'Admin\MediaController@index')->name('media');

        Route::group([ 'as' => 'custom-fields.'], function () use ( $namespacePrefix ) {

    		Route::get('/custom-fields', $namespacePrefix.'Admin\CfController@index')->name('list');
    		Route::get('/custom-field-group', $namespacePrefix.'Admin\CfController@getCreateGroup')->name('custom-fields-groups');
    		Route::post('/custom-field-groups', $namespacePrefix.'Admin\CfController@createGroup');
    		Route::get('/custom-fields/group/{id}', $namespacePrefix.'Admin\CfController@getGroup')->name('custom-fields-group');
    		Route::post('/custom-fields/group/{id}', $namespacePrefix.'Admin\CfController@updateGroup');
    		Route::delete('/custom-fields/group/{id}', $namespacePrefix.'Admin\CfController@deleteGroup');
    		Route::get('/custom-fields/group/{id}/fields', $namespacePrefix.'Admin\CfController@getGroupFields')->name('fields-list');
    		Route::post('/custom-fields/group/{id}/fields', $namespacePrefix.'Admin\CfController@createGroupFields');
    		Route::delete('/custom-fields/field', $namespacePrefix.'Admin\CfController@deleteField');
    		Route::post('/custom-fields/group/{id}/fields/sort', $namespacePrefix.'Admin\CfController@createGroupFields');
    		Route::get('/custom-fields/entry-types', $namespacePrefix.'Admin\CfController@getEntryTypes')->name('fields-entry-types');
    		Route::get('/custom-fields/entry-type', $namespacePrefix.'Admin\CfController@getEntryType');
    		Route::get('/custom-fields/taxonomy-types', $namespacePrefix.'Admin\CfController@getTaxonomyTypes');
    		Route::get('/custom-fields/pages', $namespacePrefix.'Admin\CfController@getPages')->name('fields-pages');
    		Route::post('/custom-fields/group-rules', $namespacePrefix.'Admin\CfController@createGroupRule');
    		Route::delete('/custom-fields/rule',  $namespacePrefix.'Admin\CfController@deleteGroupRule');
    		Route::delete('/custom-fields/field-group-remove/{id}',  $namespacePrefix.'Admin\CfController@deleteRepeaterGroup');

        });

        Route::group([ 'as' => 'users.'], function () use ( $namespacePrefix ) {

    		Route::get('/users',  $namespacePrefix.'Admin\UserController@index')->name('list');
    		Route::get('/user',  $namespacePrefix.'Admin\UserController@getCreate')->name('create');
    		Route::post('/users',  $namespacePrefix.'Admin\UserController@create');
    		Route::get('/users/{id}',  $namespacePrefix.'Admin\UserController@get')->name('edit');
    		Route::put('/users/{id}',  $namespacePrefix.'Admin\UserController@update');
    		Route::delete('/users/{id}',  $namespacePrefix.'Admin\UserController@delete');

        });

        Route::group([ 'as' => 'shortcodes.'], function () use ( $namespacePrefix ) {

            Route::get('/shortcodes',  $namespacePrefix.'Admin\ShortcodesController@index')->name('list');
            Route::post('/shortcodes',  $namespacePrefix.'Admin\ShortcodesController@create');
            Route::get('/shortcodes/{id}',  $namespacePrefix.'Admin\ShortcodesController@get')->name('edit');
            Route::put('/shortcodes/{id}',  $namespacePrefix.'Admin\ShortcodesController@update');
            Route::delete('/shortcodes/{id}',  $namespacePrefix.'Admin\ShortcodesController@delete');

        });

        Route::group([ 'as' => 'settings.'], function () use ( $namespacePrefix ) {

    		Route::get('/settings',  $namespacePrefix.'Admin\SettingsController@index')->name('list');
    		Route::post('/settings',  $namespacePrefix.'Admin\SettingsController@create');
    		Route::get('/settings/{id}',  $namespacePrefix.'Admin\SettingsController@get')->name('edit');
    		Route::put('/settings/{id}',  $namespacePrefix.'Admin\SettingsController@update');
    		Route::delete('/settings/{id}',  $namespacePrefix.'Admin\SettingsController@delete');
            Route::get('/settings/activity/log',  $namespacePrefix.'Admin\SettingsController@getActivityLog');
            Route::post('/settings/sitemap',  $namespacePrefix.'Admin\SitemapController@update');

            Route::get('/cache/clear/{type}', $namespacePrefix.'Admin\SettingsController@cacheClear')->name('cache.clear');
            Route::get('/cache/clear-asset-cache', $namespacePrefix.'Admin\SettingsController@clearAssetCache')->name('cache.clear-assets');

        });

        Route::group([ 'as' => 'redirects.'], function () use ( $namespacePrefix ) {

            Route::get('/redirects',  $namespacePrefix.'Admin\RedirectController@index')->name('list');
            Route::post('/redirects',  $namespacePrefix.'Admin\RedirectController@create');
            Route::get('/redirects/{id}',  $namespacePrefix.'Admin\RedirectController@get')->name('edit');
            Route::put('/redirects/{id}',  $namespacePrefix.'Admin\RedirectController@update');
            Route::delete('/redirects/{id}',  $namespacePrefix.'Admin\RedirectController@delete');

        });

        Route::group([ 'as' => 'roles.'], function () use ( $namespacePrefix ) {

    		Route::get('/roles',  $namespacePrefix.'Admin\RolesController@index')->name('list');
    		Route::post('/roles',  $namespacePrefix.'Admin\RolesController@create');
    		Route::get('/roles/{id}',  $namespacePrefix.'Admin\RolesController@get')->name('edit');
    		Route::put('/roles/{id}',  $namespacePrefix.'Admin\RolesController@update');
    		Route::delete('/roles/{id}',  $namespacePrefix.'Admin\RolesController@delete');

        });

        Route::group([ 'as' => 'events.'], function () use ( $namespacePrefix ) {

    		Route::get('/events',  $namespacePrefix.'Admin\EventController@index')->name('list');
    		Route::post('/events',  $namespacePrefix.'Admin\EventController@create');
    		Route::get('/event',  $namespacePrefix.'Admin\EventController@getCreate')->name('show');
    		Route::get('/events/{id}',  $namespacePrefix.'Admin\EventController@get')->name('edit');
    		Route::put('/events/{id}',  $namespacePrefix.'Admin\EventController@update');
    		Route::delete('/events/{id}',  $namespacePrefix.'Admin\EventController@delete');

        });

        Route::group([ 'as' => 'places.'], function () use ( $namespacePrefix ) {

    		Route::get('/places',  $namespacePrefix.'Admin\PlaceController@index')->name('list');
    		Route::post('/places',  $namespacePrefix.'Admin\PlaceController@create');
    		Route::get('/place',  $namespacePrefix.'Admin\PlaceController@getCreate')->name('show');
    		Route::get('/places/{id}',  $namespacePrefix.'Admin\PlaceController@get')->name('edit');
    		Route::put('/places/{id}',  $namespacePrefix.'Admin\PlaceController@update');
    		Route::delete('/places/{id}',  $namespacePrefix.'Admin\PlaceController@delete');

        });

        Route::group([ 'as' => 'galleries.'], function () use ( $namespacePrefix ) {

            Route::get('/galleries',  $namespacePrefix.'Admin\GalleryController@index')->name('list');
            Route::post('/galleries',  $namespacePrefix.'Admin\GalleryController@create');
            Route::get('/gallery',  $namespacePrefix.'Admin\GalleryController@getCreate')->name('show');
            Route::get('/galleries/{id}',  $namespacePrefix.'Admin\GalleryController@get')->name('edit');
            Route::put('/galleries/{id}',  $namespacePrefix.'Admin\GalleryController@update');
            Route::delete('/galleries/{id}',  $namespacePrefix.'Admin\GalleryController@delete');
            Route::delete('/galleries/images/{id}',  $namespacePrefix.'Admin\GalleryController@deleteImage');
            Route::post('/sort/gallery',  $namespacePrefix.'Admin\GalleryController@updateSort');

        });

        Route::group([ 'as' => 'menus.'], function () use ( $namespacePrefix ) {

    		Route::get('/menus',  $namespacePrefix.'Admin\MenuController@index')->name('list');
    		Route::post('/menus',  $namespacePrefix.'Admin\MenuController@create');
    		Route::get('/menus/{id}',  $namespacePrefix.'Admin\MenuController@get')->name('edit');
    		Route::delete('/menus/{id}',  $namespacePrefix.'Admin\MenuController@delete');
    		Route::post('/menu-items',  $namespacePrefix.'Admin\MenuController@addItems');
    		Route::post('/menu-items/taxonomy-terms',  $namespacePrefix.'Admin\MenuController@getTaxonomyTerms');

        });

        Route::group([ 'as' => 'forms.'], function () use ( $namespacePrefix ) {

    		Route::get('/forms',  $namespacePrefix.'Admin\FormController@index')->name('list');
    		Route::get('/form',  $namespacePrefix.'Admin\FormController@getCreate')->name('show');
    		Route::post('/forms',  $namespacePrefix.'Admin\FormController@create');
    		Route::get('/forms/{id}',  $namespacePrefix.'Admin\FormController@get')->name('edit');
    		Route::post('/forms/{id}',  $namespacePrefix.'Admin\FormController@update');
    		Route::delete('/forms/{id}',  $namespacePrefix.'Admin\FormController@delete');
    		Route::delete('/form/field',  $namespacePrefix.'Admin\FormController@deleteField');
    		Route::get('/forms/{id}/fields',  $namespacePrefix.'Admin\FormController@getCreateFields')->name('edit.fields');
    		Route::post('/forms/{id}/fields',  $namespacePrefix.'Admin\FormController@createFields');
    		Route::post('/form-fields/{id}/sort', $namespacePrefix.'Admin\FormController@createFields');
            Route::get('/forms/{id}/submissions', $namespacePrefix.'Admin\FormController@getSubmissions');
            Route::get('/private-file', $namespacePrefix.'Admin\FormController@getPrivateFile');
            Route::get('/form/export/{id}',  $namespacePrefix.'Admin\FormController@exportForm');

        });

        Route::group([ 'as' => 'filemanager.'], function () use ( $namespacePrefix ) {

    		Route::get('/filemanager', $namespacePrefix.'Admin\MediaController@get')->name('list');
    		Route::post('/filemanager/folder', $namespacePrefix.'Admin\MediaController@createFolder');
    		Route::delete('/filemanager/folder', $namespacePrefix.'Admin\MediaController@deleteFolder');
    		Route::delete('/filemanager/file', $namespacePrefix.'Admin\MediaController@deleteFile');
    		Route::post('/filemanager/upload', $namespacePrefix.'Admin\MediaController@uploadFiles');
    		Route::post('/filemanager/edit-image', $namespacePrefix.'Admin\MediaController@editImage');

        });

        Route::group([ 'as' => 'utilities.'], function () use ( $namespacePrefix ) {

    		Route::post('/object-backup', $namespacePrefix.'Admin\BackupController@backup');
    		Route::get('/object-backup/{id}', $namespacePrefix.'Admin\BackupController@getBackup');
            Route::post('/heartbeat', $namespacePrefix.'Admin\HeartBeatController@heartbeat');
            Route::post('/heartbeat/expire-edit', $namespacePrefix.'Admin\HeartBeatController@expireEdit');

        });

    });

});
