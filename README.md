<p align="center"><img width="400" src="https://neutrinocms.s3.us-east-2.amazonaws.com/neutrino.png"></p>

# Neutrino

Neutrino is a opinionated CMS for Laravel. Like a neutrino, the ghost particle, this CMS meant to be hidden and out of the way.

The goal is to separate the content owner from the designer and developer. I believe the content owner should never control the layout or try to design or develop templates. That should be the responsibility of the designer and developer. Again this is my opinion. **CAUTION** This CMS may not be for you.

All content is controlled in the admin area. A developer would be required to display that content per the design spec. Some Laravel experience is required. Please read docs below to help make implementation easier.
There is a very basic Bootstrap theme included. 

## Requirements

- PHP 7.2+
- Laravel 6+
- Database

## Installation

Install package via composer:

```
composer require newelement/neutrino
```

When composer is finished run the install command:

```
php artisan neutrino:install
```

Follow the install prompts.

Your CMS is now ready to use. Access the admin dashboard from the /admin route.

Make sure to install Laravel UI with Bootstrap. The default theme is compatible with Bootstrap.

```
composer require laravel/ui --dev
```
```
php artisan ui bootstrap
```

## Features

- Pages
- Entries (post types)
- Taxonomies (categories)
- Events
- Forms
- Menu builder
- Custom fields
- Widgets
- File manager
- Settings
- User management
- Roles
- Filters
- Shortcodes

## Development Documentation

All published views can be found in `resources/vendor/newelement/neutrino` . You are free to edit those as needed.

### Helper Functions

```php
// Get a menu in array or html
getMenu($menuName, $type = 'array');
// getMenu('Main Menu', 'html') array|html

// Get content for page or entry
getContent();

// Trim words for content
trimWords($text, $num_words = 5, $more = '...');

// Get a form and fields in array
getForm($id);
// You can find the ID on the forms list page
// Get a form and fields in HTML
getFormHTML($id, array $options);
$options['ul_parent'] = true; // Will wrap the menu list in UL tag

// Get custom fields for pages, entries or taxonomies
$data->custom_fields; // All of the fields will be returned in the $data variable in the blade.
getField('key');
getRepeater('key', $repeater);
// See Custom Fields example below

// Get Settings
getSetting($name);
// getSetting('allow_comments')

// Get Featured Image
getFeaturedImage($id, $objectType);
// $id would be your page or entry id
// $objectType would be page, entry, taxonomy, or event.

// Get Image Sizes
getImageSizes($imagePath); // the image path from $data
```

All base views will have a `$data` variable in which you can inspect its content.

### Routing Hierarchy

The dynamic router will look for content in this order based on the url slugs:

1. Page
2. Entry Types
3. Taxonomy Types
4. Events

The router will look for a corresponding blade in the Neutrino vendor views.

```
/my-page -> page.blade.php

/posts -> entry-archive.blade.php

/posts/my-article -> entry.blade.php

/category -> taxonomy.blade.php

/category/shirts -> taxonomy-term.blade.php

/events -> events.blade.php

/events/my-event -> event.blade.php
 ```

You can override specific blades by adding the specific slug to the blade file name:

```
/my-page -> page-my-page.blade.php

/news -> entry-news.blade.php

/posts/my-article -> entry-my-article.blade.php

/category -> taxonomy-category.blade.php

/category/shirts -> taxonomy-term-shirts.blade.php

/events/my-event -> event-my-event.blade.php
 ```

## Config

In the Neutrino config file that get published you can specify a default limits, date formats and sorting.

```php
'pagination_limits' => [
	'default' => 20,
	'entries' => 20,
	'post' => 20,
	'pages' => 20,
	'taxonomies' => 20,
	'taxonomy_terms' => 20
],
'calendar_date_format' => '',
```

If you have custom entry types or taxonomy it will look for the key base on the slug.

```php
'pagination_limits' => [
	'entries' => 20,
	'posts' => 20, // You can add your own items
	'category' => 20
],
```

Register shortcodes. Like `[form id="1"]`. Arguments will get passed to your callback in array.

```
'short_codes' => [
        ['name' => 'Form', 'tag' => 'form', 'callback' => 'form_short_code' ]
    ],
```

Insert admin menu links
```
'admin_menu_items' => [
        [ 
            'slot' => 4, // Where your menu links go
            'url' => '/admin/locations', 
            'parent_title' => 'Locations',
            'named_route' => 'neutrino.locations',
            'fa-icon' => 'fa-map-marked', // Use FontAwesome fal icons (light weights)
            'children' => [
                [ 'url' => '/admin/locations', 'title' => 'All Locations' ],
                [ 'url' => '/admin/location', 'title' => 'Create Location' ],
            ],
        ],
    ],
```

## Calendar Events

The base calendar events page will try to find events for the current month. You can override this by adding a request param *year_month*.

```php
/events?year_month=2022-11
```
If you would like to get a range of event months you can use the request param *end_span_month*
```php
/events?end_span_month=3 // To get next 3 months of events
```


## Custom Fields

To retrieve custom field data there are some helper functions to make life a little easier.

Get the full list of fields for your object type
```php
$fields = $data->custom_fields;
```

Get a specific field or a repeater group

```php
$hero_image = getField('hero_image');

$repeater = getField('my_repeater');

foreach( $repeater as $row ){
   
  $image = getRepeaterField('image', $row);
  $descr = getRepeaterField('image_descr', $row);   
        
}
 ```

## Widgets

Neutrino uses the [arrilot/laravel-widgets](https://github.com/arrilot/laravel-widgets) package for site-wide widgets. It offers several benefits over Laravel's view composers.

## Event Listeners

You can add listeners to perform other actions within your project. For example when an entry is added or a comment is submitted you can send an email notification.

Entry added
```php
\Event::listen('Newelement\Neutrino\Events\EntryAdded',
    function ($event) {
        // Do your stuff with the entry added data $event
    }
);
```

Entry updated
```php
\Event::listen('Newelement\Neutrino\Events\EntryUpdated',
    function ($event) {
        // Do your stuff with the entry updated data $event
    }
);
```

Comment submitted
```php
\Event::listen('Newelement\Neutrino\Events\CommentSubmitted',
    function ($event) {
        // Do your stuff with the comment data $event
    }
);
```

Form submitted
```php
\Event::listen('Newelement\Neutrino\Events\FormSubmitted',
    function ($event) {
        // Do your stuff with the form data $event
    }
);
```

In order to get the form listeners to work you will need to post the form data to:
```php
/neutrino-form // For general forms
/neutrino-comment // For comment forms
```
### Media Manager

The media manager currently only supports public and s3 disks. Make sure you have the correct config set in your .env file.

In the neutrino config file you can adjust the resize amounts.

```php
'media' => [
    'image_sizes' => [
       	'large' => 2000,
     	'medium' => 1200,
       	'small' => 600,
       	'thumb' => 280,
    ],
    'thumb_crop' => 'square',
	]
```
You can also add you own custom sizes. The values are in pixels.

The thumb_crop value is square for a centered cropped square image. Rename it to anything else for normal resizing.

### Dashboard Analytics Setup

In order to display Google Analytics on your dashboard you will need to setup access.

You will need your Google Analytics client ID. Enter or create this value in your .env file for:
```GOOGLE_ANALYTICS_CLIENT_ID=```

Acquire a client ID in Google Cloud Console. You will need to create a credential for OAuth 2.0 client ID.

You will also need your Google Analytics view ID. Enter or create this value in your .env file for:
```GOOGLE_ANALYTICS_VIEW_ID=```

The view ID can be found in Google Analytics by going to Admin -> Under the View column -> View Settings - look for View ID.

### Updating

If you attempt to update and run into asset issue, try to run:

```
php artisan vendor:publish --provider="Newelement\Neutrino\NeutrinoServiceProvider" --tag=public --force
```

## Screenshots

![Screenshot](https://neutrinocms.s3.us-east-2.amazonaws.com/dashboard.png)
![Screenshot](https://neutrinocms.s3.us-east-2.amazonaws.com/create-page.png)
![Screenshot](https://neutrinocms.s3.us-east-2.amazonaws.com/menu.png)

## Mad Props Yo

Neutrino is heavily influenced by Expression Engine, Wordpress and Voyager
