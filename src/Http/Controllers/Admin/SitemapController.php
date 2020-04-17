<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Newelement\Neutrino\Models\Setting;
use Illuminate\Support\Str;
use Newelement\Neutrino\Models\Sitemap;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\TaxonomyType;

class SitemapController extends Controller
{

    private $settings;
    private $objectTypes;

    function __construct()
    {
        $sitemapSettings = \DB::table('sitemap')->get();
        $this->settings = $sitemapSettings[0];
    }

    private $changes = [
        'always',
        'hourly',
        'daily',
        'weekly',
        'monthly',
        'yearly',
        'never'
    ];

    public function update(Request $request)
    {
        \DB::table('sitemap')->update([
            'page_default_change' => $request->page_default_change,
            'page_default_priority' => $request->page_default_priority,
            'entry_default_change' => $request->entry_default_change,
            'entry_default_priority' => $request->entry_default_priority,
            'entry_type_default_change' => $request->entry_type_default_change,
            'entry_type_default_priority' => $request->entry_type_default_priority,
            'taxonomy_default_change' => $request->taxonomy_default_change,
            'taxonomy_default_priority' => $request->taxonomy_default_priority,
            'term_default_change' => $request->term_default_change,
            'term_default_priority' => $request->term_default_priority,
            'event_default_change' => $request->event_default_change,
            'event_default_priority' => $request->event_default_priority
        ]);

        return redirect()->back()->with('success', 'Sitemap settings updated.');
    }

    public function generate()
    {
        $this->buildData();
        return response()
        ->view('neutrino::sitemap', [ 'object_types' => $this->objectTypes, 'sitemap_settings' => $this->settings ])
        ->header('Content-Type', 'text/xml');
    }

    public function buildData()
    {
         $objectTypes = [];

        if( Cache::has('sitemap-main') ){

            $objectTypes = Cache::get('sitemap-main');

        } else {

            $pages = Page::orderBy('title')->get();
            $objectTypes['page'] = $pages;

            $entries = Entry::orderBy('title')->get();
            $objectTypes['entry'] = $entries;

            $entryTypes = EntryType::orderBy('entry_type')->get();
            $objectTypes['entry_type'] = $entryTypes;

            $taxonomies = Taxonomy::orderBy('title')->get();
            $objectTypes['term'] = $taxonomies;

            $taxonomyTypes = TaxonomyType::orderBy('title')->get();
            $objectTypes['taxonomy'] = $taxonomyTypes;

            Cache::put('sitemap-main', $objectTypes, now()->addHours($this->settings->cache_hours));
        }

        $this->objectTypes = $objectTypes;
    }

    public function clearSitemapCache()
    {
        Cache::forget('sitemap-main');
    }

}
