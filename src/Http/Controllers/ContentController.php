<?php
namespace Newelement\Neutrino\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\Event;
use Newelement\Neutrino\Models\EventSlug;
use Newelement\Neutrino\Models\Place;
use Newelement\Neutrino\Models\Role;
use Newelement\Neutrino\Models\Form;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\CfGroups;
use Newelement\Neutrino\Models\CfFields;
use Newelement\Neutrino\Models\CfRule;
use Newelement\Neutrino\Models\CfObjectData;
use Newelement\Neutrino\Models\Menu;
use Newelement\Neutrino\Models\MenuItem;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\ObjectTerm;
use Newelement\Neutrino\Events\FormSubmitted;
use Newelement\Neutrino\Events\CommentSubmitted;
use Newelement\LaravelCalendarEvent\Models\CalendarEvent;
use Newelement\LaravelCalendarEvent\Models\TemplateCalendarEvent;
use TorMorten\Eventy\Facades\Events as Eventy;
use \Carbon\Carbon;
use Auth;

use Newelement\Neutrino\Traits\CustomFields;

class ContentController extends Controller
{
	use CustomFields;

	private $bladeVendor = 'neutrino::';
	private static $shortcode_tags = [];
    protected $hiddenTaxonomies = [
        'product-category'
    ];

	public function __construct(){}


	public function guessContent(Request $request, $any = false)
	{

		$slugs = explode('/', $any);
		$slugCount = count($slugs);
		$data = new \stdClass;
		$blade = '';
		$parent = $slugs[0];
		$targetSlug = end($slugs);
		unset($slugs[0]);
		$childSlugs = $slugs;

		/*** Heirarchy ****/
		/*
		*	Page
		* 	Entry Type
		* 	Taxonomy Type
		* 	Event
		*
		*/

		if( !$any ){

			$blade = $this->bladeVendor.'index';
			if( getSetting('cache') ){
				$data = Cache::rememberForever('page_index', function () {
				    return Page::where('slug', 'home')->orWhere('slug', 'index')->first();
				});
			} else {
				$data = Page::where('slug', 'home')->orWhere('slug', 'index')->first();
			}

			$CFields = getCustomFields('page_'.$data->id);
            $data->custom_fields = $CFields;

			if($request->ajax()){
                return response()->json(['data' => $data]);
            } else {
        		view()->share('customFields', $CFields);
                view()->share('objectData', $data);
                return view( $blade, ['data' => $data]);
            }

		}





		/* IF PARENT = PAGE
        *
        *
        *
        *
        */

		if( getSetting('cache') ){
			$page = Cache::rememberForever('page_'.$parent, function () use ($parent) {
				return Page::where('slug', $parent)->first();
			});
		} else {
			$page = Page::where('slug', $parent)->first();
		}

		if( $page ){

			if( getSetting('cache') ){
				$targetObject = Cache::rememberForever('page_'.$targetSlug, function () use ($targetSlug) {
				    return Page::where('slug', $targetSlug)->first();
				});
			} else {
				$targetObject = Page::where('slug', $targetSlug)->first();
			}

			if( $targetObject->protected ){
    			if( !$this->checkProtection($targetObject->protected) ){
        			return redirect()->route( config('neutrino.protected_route', 'login') )->with('error', __('neutrino::messages.protected_content') );
    			}
			}

			$data = $targetObject;
			$data->data_type = 'page';

			$blade = $this->bladeVendor.'page';

			$CFields = getCustomFields('page_'.$data->id);
            $data->custom_fields = $CFields;

            $templateBlade = $page->template? $this->bladeVendor.'.templates.'.str_replace('.blade.php', '', $page->template) : false ;

            $bladeSet = [ $blade.'-'.$targetSlug, $blade];
            if( $templateBlade ){
                array_unshift( $bladeSet, $templateBlade);
            }

			if($request->ajax()){
	        	return response()->json(['data' => $data]);
	        } else {
				view()->share('customFields', $CFields);
                view()->share('objectData', $data);
				return view()->first( $bladeSet, ['data' => $data]);
			}
		}



        /* IF PARENT = ENTRY TYPE
        *
        *
        *
        *
        */

		if( getSetting('cache') ){
			$entryType = Cache::rememberForever('entry_type_'.$parent, function () use ($parent) {
				return EntryType::where('slug', $parent)->first();
			});
		} else {
            if( !in_array( $parent, $this->hiddenTaxonomies ) ){
			    $entryType = EntryType::where('slug', $parent)->first();
            }
		}


		// ENTRY TYPE NO CHILD
		if( $entryType && $slugCount === 1 ){
			$entryTypePageLimit = config('neutrino.pagination_limits.'.$entryType->slug);
			if( !$entryTypePageLimit ){
				$entryTypePageLimit = config('neutrino.pagination_limits.entries');
			}

			$entryOrderBy = config('neutrino.ordering.entries.'.$entryType->slug.'.order');
			$entrySort = config('neutrino.ordering.entries.'.$entryType->slug.'.sort');

			if( !$entryOrderBy ){
				$entryOrderBy = config('neutrino.ordering.entries.default.order');
			}

			if( !$entrySort ){
				$entrySort = config('neutrino.ordering.entries.default.sort');
			}

			$targetObject = Entry::where('entry_type', $entryType->slug)
                            ->where('status', 'P')
                            ->whereDate('publish_date', '<=', now() )
                            ->orderBy($entryOrderBy, $entrySort)
                            ->paginate( $entryTypePageLimit );

			$data->entries = $targetObject;
			$data->title = pluralTitle($entryType->slug);
			$data->data_type = 'entry_archive';
			$blade = $this->bladeVendor.'entry-archive';

			$bladeType = $blade.'-'.$entryType->slug;

			if($request->ajax()){
	        	return response()->json(['data' => $data]);
	        } else {
				return view()->first([ $bladeType, $blade], ['data' => $data]);
			}
		}




        /* IF PARENT = ENTRY TYPE AND ENTRY
        *
        *
        *
        *
        */
		if( $entryType && $slugCount > 1 ){

			if( getSetting('cache') ){
				$targetObject = Cache::rememberForever('entry_'.$targetSlug, function () use ($targetSlug) {
				    return Entry::
                            where('slug', $targetSlug)
                            ->where('status', 'P')
                            ->first();
				});
			} else {
				$targetObject = Entry::
                                where('slug', $targetSlug)
                                ->where('status', 'P')
                                ->first();
			}

            if( $targetObject->publish_date >= now() ){
                $targetObject = false;
            }

            if( $targetObject ){

    			if( $targetObject->protected ){
        			if( !$this->checkProtection($targetObject->protected) ){
            			return redirect()->route( config('neutrino.protected_route', 'login') )->with('error', __('neutrino::messages.protected_content') );
        			}
    			}

    			$data = $targetObject;

    			$data->data_type = 'entry';
    			$blade = $this->bladeVendor.'entry';

    			$bladeType = $this->bladeVendor.$entryType->slug;

                $templateBlade = $targetObject->template? $this->bladeVendor.'.templates.'.str_replace('.blade.php', '', $targetObject->template) : false ;

                $bladeSet = [ $bladeType.'-'.$targetSlug, $blade.'-'.$targetSlug, $blade.'-'.$entryType->slug, $blade];
                if( $templateBlade ){
                    array_unshift( $bladeSet, $templateBlade);
                }

    			if($request->ajax()){
    	        	return response()->json(['data' => $data]);
    	        } else {
    				$CFields = getCustomFields('entry_'.$data->id);
                    $data->custom_fields = $CFields;
                    view()->share('customFields', $CFields);
                    view()->share('objectData', $data);
    				return view()->first($bladeSet, ['data' => $data]);
    			}
            }
		}



        /* IF PARENT = TAXONOMY
        *
        *
        *
        *
        */

		$taxonomyType = TaxonomyType::where('slug', $parent)->first();

		// If taxonomy and no terms
		if( $taxonomyType && $slugCount === 1 ){

			$taxOrderBy = config('neutrino.ordering.taxonomies.'.$taxonomyType->slug.'.order');
			$taxSort = config('neutrino.ordering.taxonomies.'.$taxonomyType->slug.'.sort');

			if( !$taxOrderBy ){
				$taxOrderBy = config('neutrino.ordering.taxonomies.default.order');
			}

			if( !$taxSort ){
				$taxSort = config('neutrino.ordering.taxonomies.default.sort');
			}

			$limit = config('neutrino.pagination_limits.'.$taxonomyType->slug);
			if(!$limit){
				$limit = config('neutrino.pagination_limits.taxonomies');
			}

			$terms = Taxonomy::where(['taxonomy_type_id' => $taxonomyType->id, 'parent_id' => 0])
							->orderBy($taxOrderBy, $taxSort)
							->paginate( $limit );

			$data->terms = $terms;
			$data->title = pluralTitle($taxonomyType->title);
			$data->data_type = 'taxonomy';
			$blade = $this->bladeVendor.'taxonomy';

			$bladeType = $blade.'-'.$taxonomyType->slug;

			if($request->ajax()){
	        	return response()->json(['data' => $data]);
	        } else {
				$CFields = getCustomFields('taxonomy_'.$data->id);
                $data->custom_fields = $CFields;
                view()->share('customFields', $CFields);
                view()->share('objectData', $data);
				return view()->first([ $bladeType , $blade], ['data' => $data]);
			}
		}

		if( $taxonomyType && $slugCount > 1 ){

			// Get the targetSlug type
			$targetSlugIsTerm = Taxonomy::where('slug', $targetSlug)->first();
			$targetSlugIsEntry = Entry::where('slug', $targetSlug)->first();

			if( $targetSlugIsTerm ){

				$targetObject = $targetSlugIsTerm;

				$termOrderBy = config('neutrino.ordering.'.$targetSlug.'.order');
				$termSort = config('neutrino.ordering.'.$targetSlug.'.sort');

				if( !$termOrderBy ){
					$termOrderBy = config('neutrino.ordering.taxonomies.default.order');
				}

				if( !$termSort ){
					$termSort = config('neutrino.ordering.taxonomies.default.sort');
				}

				$limit = config('neutrino.pagination_limits.entries-'.$taxonomyType->slug);
				if( !$limit ){
					$limit = config('neutrino.pagination_limits.entries');
				}

				$entryOrderBy = config('neutrino.ordering.entries.'.$taxonomyType->slug.'.order');
				$entrySort = config('neutrino.ordering.entries.'.$taxonomyType->slug.'.sort');

				if( !$entryOrderBy ){
					$entryOrderBy = config('neutrino.ordering.entries.default.order');
				}

				if( !$entrySort ){
					$entrySort = config('neutrino.ordering.entries.default.sort');
				}

				// TODO Need to figure out the eloquent relationship for this
				$entries = Entry::
							join('object_terms', 'entries.id', '=', 'object_terms.object_id')
							->where([
								'object_terms.taxonomy_type_id' => $taxonomyType->id,
								'object_terms.taxonomy_id' => $targetObject->id,
								'object_terms.object_type' => 'entry',
								'entries.status' => 'P'
							])
							->whereNull('entries.deleted_at')
							->orderBy( 'entries.'.$entryOrderBy, $entrySort)
							->paginate( $limit );

				$limit2 = config('neutrino.pagination_limits.terms-'.$targetSlug);
				if( !$limit2 ){
					$limit2 = config('neutrino.pagination_limits.taxonomies');
				}

				$terms = Taxonomy::where('parent_id', $targetObject->id)
							->orderBy($termOrderBy, $termSort)
							->paginate( $limit2 );

				$data = $targetObject;
				$data->terms = $terms;
				$data->title = pluralTitle($data->title);
				$data->entries = $entries;
				$data->data_type = 'taxonomy_term';
				$blade = $this->bladeVendor.'taxonomy-term';

				$bladeType = $this->bladeVendor.'taxonomy-term';

				if($request->ajax()){
		        	return response()->json(['data' => $data]);
		        } else {
					return view()->first([ $bladeType.'-'.$targetSlug , $blade], ['data' => $data]);
				}

			}

			if( $targetSlugIsEntry ){

				$targetObject = $targetSlugIsEntry;
				$entryType = EntryType::where('slug', $targetObject->entry_type)->first();

				$data = $targetObject;
				$data->terms = [];
				$data->title = pluralTitle($data->title);
				$data->entries = [];
				$data->entry_type = $entryType;
				$data->data_type = 'term_entry';
				$blade = $this->bladeVendor.'term-entry';

				$bladeType = $this->bladeVendor.'term-entry';

				if($request->ajax()){
		        	return response()->json(['data' => $data]);
		        } else {
					$CFields = getCustomFields('entry_'.$data->id);
                    $data->custom_fields = $CFields;
                    view()->share('customFields', $CFields);
                    view()->share('objectData', $data);
					return view()->first([ $bladeType.'-'.$targetSlug , $blade], ['data' => $data]);
				}

			}

			if( !$targetSlugIsEntry && !$targetSlugIsTerm ){
				abort(404);
			}

		}



		/* IF PARENT = EVENTS
        *
        *
        *
        *
        */

		$events = $parent === 'events' ? true : false ;

		if( $events ){

            $data = new \stdClass;

            $yearMonth = date('Y-m');

            if( $request->year_month ){
                $yearMonth = $request->year_month;
            }

            $startMonth = explode('-', $yearMonth);
            $startMonth = (int) $startMonth[1];

            $parseYearMonth = Carbon::parse($yearMonth);

			$events = CalendarEvent::showPotentialCalendarEventsOfMonth($parseYearMonth);

			$events = collect($events);

			if( $request->end_span_month ){
                $endSpanMonth = (int) $request->end_span_month;

                $arr = [];
    			for ($x = 1; $x <= $endSpanMonth; $x++) {
                    $nextMonth = Carbon::parse($yearMonth)->addMonths($x);
                    $nextEvents = CalendarEvent::showPotentialCalendarEventsOfMonth($nextMonth);

                    if( $nextEvents->count() > 0 ){
                        $arr = collect($arr)->merge($nextEvents);
                    }
                }
                if( count($arr) > 0 ){
                    $arr = collect($arr);
                    if( $events->count() === 0 ){
                        $events = $arr;
                    } else {
                        $events->merge($arr);
                    }
                }

			}

			$page = $request->page? (int) $request->page : 1;
			$events = $this->paginate($events, config('neutrino.pagination_limits.events', 15));

			foreach( $events as $key => $event ){
    			$slug = false;
    			if( $event->is_not_exists ){
        			$template = CalendarEvent::where('template_calendar_event_id', $event->template->id)->first();
                    $slug = EventSlug::where('event_id', $template->id)->first();
    			} else {
        			$slug = EventSlug::where('event_id', $event->id)->first();
    			}

    			$start = $event->start_datetime->timestamp;
    			$end = $event->end_datetime->timestamp;
                $events[$key]->id = $event->id;

                $events[$key]->start_datetime = $event->start_datetime;
                $events[$key]->end_datetime = $event->end_datetime;

    			$events[$key]->title = $event->template->title;
    			$events[$key]->slug = $slug? $slug->slug : '';
    			$events[$key]->place = $event->template->place_id? Place::find($event->template->place_id) : null;
    			$events[$key]->description = $this->parseContent($event->template->description);
    			$events[$key]->url = '/event/'.$slug->slug.'/'.$start.'/'.$end;

			}

			$data->data_type = 'events';
			$data->title = 'Events';

			$data->events = $events;

			$blade = $this->bladeVendor.'events';
			$bladeType = $this->bladeVendor.'events';

			if($request->ajax()){
	        	return response()->json(['data' => $data]);
	        } else {
				return view()->first( [$bladeType.'-'.$targetSlug, $blade.'-'.$targetSlug, $blade], ['data' => $data]);
			}

        }




		/* IF PARENT = EVENT
        *
        *
        *
        *
        */

		if($parent === 'event') {

    		$targetObject = CalendarEvent::
    		                join('event_slugs','calendar_events.id','=','event_slugs.event_id')

    		                ->where('slug', $slugs[1])
    		                ->first();

    		if( $targetObject ){

    			$data = $targetObject;
    			$data->data_type = 'event';
    			$data->title = $targetObject->template->title;
    			$data->start_datetime = $targetObject->template->start_datetime;
    			$data->end_datetime = $targetObject->template->end_datetime;

    			$start = '';
    			$end = '';
    			try {
    			    $start = isset( $slugs[2] )? Carbon::createFromTimestamp($slugs[2]) : '';
    			} catch( \Exception $e ){
        			$start = 'Invalid timestamp';
    			}
    			$end = isset( $slugs[3] )? Carbon::createFromTimestamp($slugs[3]) : '';

                $data->start_datetime = $start;
                $data->end_datetime = $end;
    			$data->description = $this->parseContent($targetObject->template->description);
    			$data->place = $targetObject->template->place_id? Place::find($targetObject->template->place_id) : null;

    			$blade = $this->bladeVendor.'event';
    			$bladeType = $this->bladeVendor.'event';

    			if($request->ajax()){
    	        	return response()->json(['data' => $data]);
    	        } else {
    				return view()->first([ $bladeType.'-'.$targetSlug, $blade.'-'.$targetSlug, $blade], ['data' => $data]);
    			}
    		}

		}


		// Sorry we tired ...
		abort(404);


	}



	private function parseContent($content)
	{
		return html_entity_decode($content);
	}

	private function checkProtection($protected)
	{
    	$protected = collect(explode(',', $protected));

    	if( !Auth::check() ){
        	return false;
    	}

    	$role = Role::find(Auth::user()->role_id);
        return $protected->contains($role->name);
	}

	public function submitForm(Request $request)
	{
		$form = Form::find($request->id);
        if( !$form ){
            abort(404);
        }
		$fields = $form->fields()->get();
		foreach( $fields as $field ){
			if( $field->required ){
				$max = '';
				if( $field->max_length ){
					$max = '|max:'.$field->max_length;
				}
				$validate[$field->field_name] = 'required'.$max;
			}
		}

		$validatedData = $request->validate($validate);

		event(new FormSubmitted($form, $data));

		return redirect()->back()->with('success', 'Thank you. Your form has been submitted.');
	}

	public function submitComment(Request $request)
	{
		$validatedData = $request->validate([
	        'comment' => 'required|max:500',
	    ]);

		$entry = Entry::find($request->entry_id);
		$comment = new Comment;
		$comment->entry_id = $request->entry_id;
		$comment->user_id = auth()->user()->id;
		$comment->parent_id = $request->parent_id? $request->parent_id : 0;
		$comment->comment = $request->comment;

		$approvedMessage = '';
		$moderate = getSetting('moderate_comments');
		if( $moderate === 'Y' ){
			$comment->approved = 0;
			$approvedMessage = 'Comments are moderated and will be reviewed as soon as possible.';
		}

		$comment->save();

		event(new CommentSubmitted($comment, $entry));

		return redirect()->back()->with('success', 'Thank you. Your comment has been submitted. '.$approvedMessage);

	}

	public function submitCommentVote(Request $request)
	{

	}

	private function paginate($collection, $perPage = 5, $pageName = 'page', $fragment = null)
    {
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage);
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'pageName' => $pageName,
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query' => $query,
                'fragment' => $fragment
            ]
        );

        return $paginator;
    }

    public static function doShortCodes($content)
    {
        $shortcode_tags = [];
        $shortCodes = config('neutrino.short_codes', []);
        foreach( $shortCodes as $shortCode ){
            $shortcode_tags[$shortCode['tag']] = $shortCode['callback'];
        }

        self::$shortcode_tags = $shortcode_tags;

        if ( false === strpos( $content, '[' ) ) {
            return $content;
        }

        if ( empty( $shortcode_tags ) || ! is_array( $shortcode_tags ) ) {
            return $content;
        }

        // Find all registered tag names in $content.
        preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
        $tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

        if ( empty( $tagnames ) ) {
            return $content;
        }

        $content = self::do_shortcodes_in_html_tags( $content, $tagnames );

        $pattern = self::get_shortcode_regex( $tagnames );
        $content = preg_replace_callback( "/$pattern/", array(self::class, 'do_shortcode_tag'), $content );

        return $content;
    }

    private static function do_shortcode_tag( $m ) {

        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr( $m[0], 1, -1 );
        }

        $tag  = $m[2];
        $attr = self::shortcode_parse_atts( $m[3] );

        if ( ! is_callable( self::$shortcode_tags[ $tag ] ) ) {
            return $m[0];
        }


        $content = isset( $m[5] ) ? $m[5] : null;
        //$output = $m[1] . call_user_func( $this->shortcode_tags[ $tag ], $attr, $content, $tag ) . $m[6];
        $output = $m[1] . call_user_func( self::$shortcode_tags[ $tag ], $attr ) . $m[6];

        return $output;
    }

    private static function get_shortcode_regex( $tagnames = null ) {

        if ( empty( $tagnames ) ) {
            $tagnames = array_keys( $this->shortcode_tags );
        }
        $tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );

        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return
            '\\['                                // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
        // phpcs:enable
    }

    private static function do_shortcodes_in_html_tags( $content, $tagnames ) {
        // Normalize entities in unfiltered HTML before adding placeholders.
        $trans   = array(
            '&#91;' => '&#091;',
            '&#93;' => '&#093;',
        );
        $content = strtr( $content, $trans );
        $trans   = array(
            '[' => '&#91;',
            ']' => '&#93;',
        );

        $pattern = self::get_shortcode_regex( $tagnames );
        $textarr = self::html_split( $content );

        foreach ( $textarr as &$element ) {
            if ( '' == $element || '<' !== $element[0] ) {
                continue;
            }

            $noopen  = false === strpos( $element, '[' );
            $noclose = false === strpos( $element, ']' );
            if ( $noopen || $noclose ) {
                // This element does not contain shortcodes.
                if ( $noopen xor $noclose ) {
                    // Need to encode stray [ or ] chars.
                    $element = strtr( $element, $trans );
                }
                continue;
            }

            if ( $ignore_html || '<!--' === substr( $element, 0, 4 ) || '<![CDATA[' === substr( $element, 0, 9 ) ) {
                // Encode all [ and ] chars.
                $element = strtr( $element, $trans );
                continue;
            }

            $attributes = $this->kses_attr_parse( $element );
            if ( false === $attributes ) {
                // Some plugins are doing things like [name] <[email]>.
                if ( 1 === preg_match( '%^<\s*\[\[?[^\[\]]+\]%', $element ) ) {
                    $element = preg_replace_callback( "/$pattern/", 'do_shortcode_tag', $element );
                }

                // Looks like we found some crazy unfiltered HTML.  Skipping it for sanity.
                $element = strtr( $element, $trans );
                continue;
            }

            // Get element name
            $front   = array_shift( $attributes );
            $back    = array_pop( $attributes );
            $matches = array();
            preg_match( '%[a-zA-Z0-9]+%', $front, $matches );
            $elname = $matches[0];

            // Look for shortcodes in each attribute separately.
            foreach ( $attributes as &$attr ) {
                $open  = strpos( $attr, '[' );
                $close = strpos( $attr, ']' );
                if ( false === $open || false === $close ) {
                    continue; // Go to next attribute.  Square braces will be escaped at end of loop.
                }
                $double = strpos( $attr, '"' );
                $single = strpos( $attr, "'" );
                if ( ( false === $single || $open < $single ) && ( false === $double || $open < $double ) ) {
                    // $attr like '[shortcode]' or 'name = [shortcode]' implies unfiltered_html.
                    // In this specific situation we assume KSES did not run because the input
                    // was written by an administrator, so we should avoid changing the output
                    // and we do not need to run KSES here.
                    $attr = preg_replace_callback( "/$pattern/", array($this, 'do_shortcode_tag'), $attr );
                } else {
                    // $attr like 'name = "[shortcode]"' or "name = '[shortcode]'"
                    // We do not know if $content was unfiltered. Assume KSES ran before shortcodes.
                    $count    = 0;
                    $new_attr = preg_replace_callback( "/$pattern/", array($this, 'do_shortcode_tag'), $attr, -1, $count );
                    if ( $count > 0 ) {
                        // Sanitize the shortcode output using KSES.
                        $new_attr = $this->kses_one_attr( $new_attr, $elname );
                        if ( '' !== trim( $new_attr ) ) {
                            // The shortcode is safe to use now.
                            $attr = $new_attr;
                        }
                    }
                }
            }
            $element = $front . implode( '', $attributes ) . $back;

            // Now encode any remaining [ or ] chars.
            $element = strtr( $element, $trans );
        }

        $content = implode( '', $textarr );

        return $content;
    }

    private static function shortcode_parse_atts( $text ) {
        $atts    = array();
        $pattern = self::get_shortcode_atts_regex();
        $text    = preg_replace( "/[\x{00a0}\x{200b}]+/u", ' ', $text );
        if ( preg_match_all( $pattern, $text, $match, PREG_SET_ORDER ) ) {
            foreach ( $match as $m ) {
                if ( ! empty( $m[1] ) ) {
                    $atts[ strtolower( $m[1] ) ] = stripcslashes( $m[2] );
                } elseif ( ! empty( $m[3] ) ) {
                    $atts[ strtolower( $m[3] ) ] = stripcslashes( $m[4] );
                } elseif ( ! empty( $m[5] ) ) {
                    $atts[ strtolower( $m[5] ) ] = stripcslashes( $m[6] );
                } elseif ( isset( $m[7] ) && strlen( $m[7] ) ) {
                    $atts[] = stripcslashes( $m[7] );
                } elseif ( isset( $m[8] ) && strlen( $m[8] ) ) {
                    $atts[] = stripcslashes( $m[8] );
                } elseif ( isset( $m[9] ) ) {
                    $atts[] = stripcslashes( $m[9] );
                }
            }

            // Reject any unclosed HTML elements.
            foreach ( $atts as &$value ) {
                if ( false !== strpos( $value, '<' ) ) {
                    if ( 1 !== preg_match( '/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value ) ) {
                        $value = '';
                    }
                }
            }
        } else {
            $atts = ltrim( $text );
        }

        return $atts;
    }

    private static function get_shortcode_atts_regex()
    {
        return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
    }

    private static function html_split( $input )
    {
        return preg_split( self::get_html_split_regex(), $input, -1, PREG_SPLIT_DELIM_CAPTURE );
    }

    private static function get_html_split_regex() {
        static $regex;

        if ( ! isset( $regex ) ) {
            // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
            $comments =
                '!'             // Start of comment, after the <.
                . '(?:'         // Unroll the loop: Consume everything until --> is found.
                .     '-(?!->)' // Dash not followed by end of comment.
                .     '[^\-]*+' // Consume non-dashes.
                . ')*+'         // Loop possessively.
                . '(?:-->)?';   // End of comment. If not found, match all input.

            $cdata =
                '!\[CDATA\['    // Start of comment, after the <.
                . '[^\]]*+'     // Consume non-].
                . '(?:'         // Unroll the loop: Consume everything until ]]> is found.
                .     '](?!]>)' // One ] not followed by end of comment.
                .     '[^\]]*+' // Consume non-].
                . ')*+'         // Loop possessively.
                . '(?:]]>)?';   // End of comment. If not found, match all input.

            $escaped =
                '(?='             // Is the element escaped?
                .    '!--'
                . '|'
                .    '!\[CDATA\['
                . ')'
                . '(?(?=!-)'      // If yes, which type?
                .     $comments
                . '|'
                .     $cdata
                . ')';

            $regex =
                '/('                // Capture the entire match.
                .     '<'           // Find start of element.
                .     '(?'          // Conditional expression follows.
                .         $escaped  // Find end of escaped element.
                .     '|'           // ... else ...
                .         '[^>]*>?' // Find end of normal element.
                .     ')'
                . ')/';
            // phpcs:enable
        }

        return $regex;
    }

    private static function kses_attr_parse( $element ) {
        $valid = preg_match( '%^(<\s*)(/\s*)?([a-zA-Z0-9]+\s*)([^>]*)(>?)$%', $element, $matches );
        if ( 1 !== $valid ) {
            return false;
        }

        $begin  = $matches[1];
        $slash  = $matches[2];
        $elname = $matches[3];
        $attr   = $matches[4];
        $end    = $matches[5];

        if ( '' !== $slash ) {
            // Closing elements do not get parsed.
            return false;
        }

        // Is there a closing XHTML slash at the end of the attributes?
        if ( 1 === preg_match( '%\s*/\s*$%', $attr, $matches ) ) {
            $xhtml_slash = $matches[0];
            $attr        = substr( $attr, 0, -strlen( $xhtml_slash ) );
        } else {
            $xhtml_slash = '';
        }

        // Split it
        $attrarr = self::kses_hair_parse( $attr );
        if ( false === $attrarr ) {
            return false;
        }

        // Make sure all input is returned by adding front and back matter.
        array_unshift( $attrarr, $begin . $slash . $elname );
        array_push( $attrarr, $xhtml_slash . $end );

        return $attrarr;
    }

    private static function kses_hair_parse( $attr ) {
        if ( '' === $attr ) {
            return array();
        }

        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        $regex =
        '(?:'
        .     '[-a-zA-Z:]+'   // Attribute name.
        . '|'
        .     '\[\[?[^\[\]]+\]\]?' // Shortcode in the name position implies unfiltered_html.
        . ')'
        . '(?:'               // Attribute value.
        .     '\s*=\s*'       // All values begin with '='
        .     '(?:'
        .         '"[^"]*"'   // Double-quoted
        .     '|'
        .         "'[^']*'"   // Single-quoted
        .     '|'
        .         '[^\s"\']+' // Non-quoted
        .         '(?:\s|$)'  // Must have a space
        .     ')'
        . '|'
        .     '(?:\s|$)'      // If attribute has no value, space is required.
        . ')'
        . '\s*';              // Trailing space is optional except as mentioned above.
        // phpcs:enable

        // Although it is possible to reduce this procedure to a single regexp,
        // we must run that regexp twice to get exactly the expected result.

        $validation = "%^($regex)+$%";
        $extraction = "%$regex%";

        if ( 1 === preg_match( $validation, $attr ) ) {
            preg_match_all( $extraction, $attr, $attrarr );
            return $attrarr[0];
        } else {
            return false;
        }
    }

    private static function kses_one_attr( $string, $element )
    {
        //$uris              = wp_kses_uri_attributes();
        //$allowed_html      = wp_kses_allowed_html( 'post' );
        //$allowed_protocols = wp_allowed_protocols();
        //$string            = wp_kses_no_null( $string, array( 'slash_zero' => 'keep' ) );

        // Preserve leading and trailing whitespace.
        $matches = array();
        preg_match( '/^\s*/', $string, $matches );
        $lead = $matches[0];
        preg_match( '/\s*$/', $string, $matches );
        $trail = $matches[0];
        if ( empty( $trail ) ) {
            $string = substr( $string, strlen( $lead ) );
        } else {
            $string = substr( $string, strlen( $lead ), -strlen( $trail ) );
        }

        // Parse attribute name and value from input.
        $split = preg_split( '/\s*=\s*/', $string, 2 );
        $name  = $split[0];
        if ( count( $split ) == 2 ) {
            $value = $split[1];

            // Remove quotes surrounding $value.
            // Also guarantee correct quoting in $string for this one attribute.
            if ( '' == $value ) {
                $quote = '';
            } else {
                $quote = $value[0];
            }
            if ( '"' == $quote || "'" == $quote ) {
                if ( substr( $value, -1 ) != $quote ) {
                    return '';
                }
                $value = substr( $value, 1, -1 );
            } else {
                $quote = '"';
            }

            // Sanitize quotes, angle braces, and entities.
            //$value = esc_attr( $value );

            $string = "$name=$quote$value$quote";
            $vless  = 'n';
        } else {
            $value = '';
            $vless = 'y';
        }

        // Sanitize attribute by name.
        //wp_kses_attr_check( $name, $value, $string, $vless, $element, $allowed_html );

        // Restore whitespace.
        return $lead . $string . $trail;
    }

    public static function form_short_code( $attrs = [] ){
        $args = [];

        $args['show_title'] = isset($attrs['show_title']) && $attrs['show_title'] === 'true'? true : false;

        $form = getFormHTML($attrs['id'], $args);
        return $form;
    }

    public static function gallery_short_code( $attrs = [] ){
        $args = [];

        $args['show_title'] = isset($attrs['show_title']) && $attrs['show_title'] === 'true'? true : false;
        $args['show_description'] = isset($attrs['show_description']) && $attrs['show_description'] === 'true'? true : false;
        $args['theme'] = isset($attrs['theme'])? $attrs['theme'] : false;
        $args['slides_to_show'] = isset($attrs['slides_to_show'])? $attrs['slides_to_show'] : false;
        $args['slides_to_scroll'] = isset($attrs['slides_to_scroll'])? $attrs['slides_to_scroll'] : false;
        $args['dots'] = isset($attrs['dots']) && $attrs['dots'] === 'true'? true : false;
        $args['autoplay'] = isset($attrs['autoplay']) && $attrs['autoplay'] === 'true'? true : false;

        $gallery = getGalleryHTML($attrs['id'], $args);
        return $gallery;
    }

}
