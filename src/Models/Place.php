<?php

namespace Newelement\Neutrino\Models;

use Illuminate\Database\Eloquent\Model;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Searchable\SearchableTrait;
use Newelement\LaravelCalendarEvent\Interfaces\PlaceInterface;
use Newelement\LaravelCalendarEvent\Traits\CalendarEventPlaceTrait;

class Place extends Model implements PlaceInterface
{
    use SearchableTrait;
    use CalendarEventPlaceTrait;
    
    protected $searchable = [
        'columns' => [
            'location_name' => 7,
            'description' => 5
        ],
    ];

}
