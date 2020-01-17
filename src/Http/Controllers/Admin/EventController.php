<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\LaravelCalendarEvent\Models\CalendarEvent;
use Newelement\LaravelCalendarEvent\Enums\RecurringFrequenceType;
use Newelement\LaravelCalendarEvent\Models\TemplateCalendarEvent;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\EventSlug;
use Newelement\Neutrino\Models\Place;
use Carbon\Carbon;

class EventController extends Controller
{

	public function __construct(){}

	public function index()
	{
		$calendarEvents = CalendarEvent::showPotentialCalendarEventsOfMonth(Carbon::parse('2019-09'));
		$events = CalendarEvent::paginate(30);

		return view('neutrino::admin.events.index', ['events' => $events, 'month_events' => $calendarEvents]);
	}

	public function getCreate()
	{
		$locations = Place::orderBy('location_name', 'asc')->get();
		return view('neutrino::admin.events.create', ['locations' => $locations]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
			'start_date' => 'required',
			'end_date' => 'required',
			'start_time' => 'required',
			'end_time' => 'required',
			'slug' => 'required',
	    ]);

		$types = [
			'DAY' => RecurringFrequenceType::DAY,
			'WEEK' => RecurringFrequenceType::WEEK,
			'MONTH' => RecurringFrequenceType::MONTH,
			'YEAR' => RecurringFrequenceType::YEAR,
			'NTHWEEKDAY' => RecurringFrequenceType::NTHWEEKDAY
		];
		
		$freqType = $request->frequency_type ? $types[$request->frequency_type] : null ;
		$endRecurTime = $request->end_recurring_date ? Carbon::parse($request->end_recurring_date.' '.$request->end_recurring_time) : null ;
		
		$place = null;
		
		if( $request->place ){
    		$place = Place::find( $request->place );
		}

		$calendarEvent = new CalendarEvent();
		$calendarEvent = $calendarEvent->createCalendarEvent([
		    'title'                         => $request->title,
		    'start_datetime'                => Carbon::parse($request->start_date.' '.$request->start_time),
		    'end_datetime'                  => Carbon::parse($request->end_date.' '.$request->end_time),
		    'description'                   => htmlentities($request->description),
		    'is_recurring'                  => (int) $request->recurring? true : false ,
		    'frequence_number_of_recurring' => (int) $request->recurr_times? $request->recurr_times : 0 ,
		    'frequence_type_of_recurring'   => $freqType,
		    'is_public'                     => true,
		    'end_of_recurring'              => $endRecurTime
		], $user = null, $place);

		EventSlug::create([
			'event_id' => $calendarEvent->id,
			'slug' => toSlug($request->slug, 'event')
		]);

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $calendarEvent->id,
				'object_type' => 'event',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);

		}

		return redirect('/admin/events/'.$calendarEvent->id)->with('success', 'Event created.');

	}

	public function get($id)
	{
		$event = CalendarEvent::
		        join('event_slugs', 'event_slugs.event_id', '=', 'calendar_events.id')
		        ->where('calendar_events.id', $id)
		        ->select('calendar_events.*', 'event_slugs.slug')
		        ->first();
		        
		$locations = Place::orderBy('location_name', 'asc')->get();
		
		return view('neutrino::admin.events.edit', ['event' => $event, 'locations' => $locations]);
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'title' => 'required|max:255',
			'start_date' => 'required',
			'end_date' => 'required',
			'start_time' => 'required',
			'end_time' => 'required',
	    ]);

		$types = [
			'DAY' => RecurringFrequenceType::DAY,
			'WEEK' => RecurringFrequenceType::WEEK,
			'MONTH' => RecurringFrequenceType::MONTH,
			'YEAR' => RecurringFrequenceType::YEAR,
			'NTHWEEKDAY' => RecurringFrequenceType::NTHWEEKDAY
		];
		
		$freqType = $request->frequency_type ? $types[$request->frequency_type] : null ;
		$endRecurTime = $request->end_recurring_date ? Carbon::parse($request->end_recurring_date.' '.$request->end_recurring_time) : null ;
		
		$place = null;
		
		if( $request->place ){
    		$place = Place::find( $request->place );
		}
        
		$calendarEvent = CalendarEvent::find($id);
		$calendarEvent = $calendarEvent->editCalendarEvent([
		    'title'                         => $request->title,
		    'start_datetime'                => Carbon::parse($request->start_date.' '.$request->start_time),
		    'end_datetime'                  => Carbon::parse($request->end_date.' '.$request->end_time),
		    'description'                   => htmlentities($request->description),
		    'is_recurring'                  => (int) $request->recurring? true : false ,
		    'frequence_number_of_recurring' => (int) $request->recurr_times? : 0,
		    'frequence_type_of_recurring'   => $freqType,
		    'is_public'                     => true,
		    'end_of_recurring'              => $endRecurTime
		], $user = null, $place);
		
		$newId = $calendarEvent? $calendarEvent->id : $id;

        EventSlug::where('event_id', $id)->update(['event_id' => $newId]);

		$currSlug = EventSlug::where('event_id', $newId)->first();

		if( $currSlug->slug !== $request->slug ){
            $slug = toSlug($request->slug, 'event');
		    EventSlug::updateOrCreate(
    			['event_id' => $newId],
    			['slug' => $slug]
            );
		}

		if( $request->featured_image ){
    		ObjectMedia::where(['object_id' => $id, 'object_type' => 'event', 'featured' => 1])->update(['object_id' => $newId]);
			ObjectMedia::updateOrCreate([
				'object_id' => $newId,
				'object_type' => 'event',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);
		}
        
		return redirect('/admin/events/'.$newId)->with('success', 'Event updated.');

	}


	public function delete($id)
	{
		$calendarEvent = CalendarEvent::find($id);
		$isDeleted = $calendarEvent->deleteCalendarEvent($isRecurring = $calendarEvent->is_recurring);
		return redirect('/admin/events')->with('success', 'Event deleted.');
	}

}
