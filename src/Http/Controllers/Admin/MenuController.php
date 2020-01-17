<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\ObjectMedia;
use Newelement\Neutrino\Models\Menu;
use Newelement\Neutrino\Models\MenuItem;
use Newelement\Neutrino\Models\Page;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\EntryType;
use Newelement\Neutrino\Models\Location;
use Newelement\Neutrino\Models\Taxonomy;
use Newelement\Neutrino\Models\TaxonomyType;
use Newelement\Neutrino\Models\User;

class MenuController extends Controller
{

	private $order = 0;

	public function __construct(){}

	public function index()
	{
		$menus = Menu::paginate(30);
		$menu = new \stdClass;
		$menu->id = '';
		$menu->name = '';
		$menu->items = [];
		return view('neutrino::admin.menus.index', ['menus' => $menus, 'edit_menu' => $menu , 'edit' => false]);
	}

	public function create(Request $request)
	{
		$validatedData = $request->validate([
	        'name' => 'required|max:255',
	    ]);

		$menu = new Menu;
		$menu->name = $request->name;
		$menu->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $menu->id,
				'object_type' => 'menu',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);
		}

		return redirect('/admin/menus/'.$menu->id)->with('success', 'Menu created.');

	}

	public function get($id)
	{
		$menu = Menu::find($id);
		$items = MenuItem::where(['menu_id' => $id])->orderBy('order', 'asc')->get()->toArray();
		$menuItems = $this->getMenuItems($items, 0);

		$pages = Page::orderBy('title', 'asc')->get();
		$entryTypes = EntryType::orderBy('entry_type', 'asc')->get();
		$entries = Entry::orderBy('title', 'asc')->get();
		$taxonomies = TaxonomyType::orderBy('title', 'asc')->get();
		return view('neutrino::admin.menus.menu-items', ['menu' => $menu,
														'menu_items' => $menuItems,
														'entry_types' => $entryTypes,
														'entries' => $entries,
														'pages' => $pages,
														'taxonomies' => $taxonomies
													]);
	}

	private function getMenuItems($items, $parentId = 0)
	{
		$branch = [];

	    foreach ($items as $element) {
	        if ($element['parent_id'] === $parentId) {
	            $children = $this->getMenuItems($items, $element['id']);
	            if ($children) {
	                $element['children'] = $children;
	            }
	            $branch[] = $element;
	        }
	    }

	    return $branch;
	}

	public function getTaxonomyTerms(Request $request)
	{
		$taxonomyId = $request->id;
		$terms = Taxonomy::where( 'taxonomy_type_id', $taxonomyId )->orderBy('title', 'asc')->get();

		$i = 0;
		foreach( $terms as $term ){
			$terms[$i]->url = $term->url();
			$i++;
		}

		return response()->json( ['terms' => $terms] );
	}

	public function update(Request $request, $id)
	{
		$validatedData = $request->validate([
	        'name' => 'required|max:255',
	    ]);

		$menu = new Menu;
		$menu->name = $request->name;
		$menu->save();

		if( $request->featured_image ){
			ObjectMedia::updateOrCreate([
				'object_id' => $id,
				'object_type' => 'menu',
				'featured' => 1
			], [ 'file_path' => parse_url($request->featured_image, PHP_URL_PATH) ]);
		}

		return redirect('/admin/menus/'.$id)->with('success', 'Menu updated.');

	}


	public function delete($id)
	{
		$menu = Menu::find($id);
		$menu->delete();
		return redirect('/admin/menus')->with('success', 'Menu deleted.');
	}

	public function addItems(Request $request)
	{
		$payLoad = json_decode(request()->getContent(), true);
		$menuId = (int) $payLoad['menu_id'];

		MenuItem::where('menu_id', $menuId)->delete();

		foreach( $payLoad['items'] as $item ){
			$this->recurMenuItems($menuId, $item, $parentId = 0);
			$this->order++;
		}

		return response()->json(['success' => true]);
	}

	private function recurMenuItems($menuId, $item, $parentId = 0)
	{
		$menuItem = new MenuItem;
		$menuItem->menu_id = $menuId;
		$menuItem->title = $item['title'];
		$url = $item['url'];
		if( $item['type'] === 'file' ){
			$url = parse_url($item['url'], PHP_URL_PATH);
		}
		$menuItem->url = $url;
		$menuItem->target = $item['target'];
		$menuItem->parent_id = $parentId;
		$menuItem->order = $this->order;
		$menuItem->parameters = $item['type'];
		$menuItem->save();

		if( isset($item['children']) ){
			foreach( $item['children'] as $child ){
				$this->recurMenuItems($menuId, $child, $menuItem->id);
				$this->order++;
			}
		}
	}

}
