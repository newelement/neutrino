<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Backup;
use Carbon\Carbon;

class BackupController extends Controller
{

	public function backup(Request $request)
	{
		$object_id = $request->object_id;
		$object_type = $request->object_type;
		$content = $request->content;

		$backup = Backup::updateOrCreate([
			'object_id' => $object_id,
			'object_type' => $object_type,
			'content' => $content
		]);

		$created = $backup->wasRecentlyCreated;

		$count = $backup::where(['object_id' => $object_id,
		'object_type' => $object_type])->count();

		// Only keep last 50 changes
		Backup::where(['object_id' => $object_id,
		'object_type' => $object_type])
		->latest()->take($count)->skip(100)->get()->each(
			function($row){ $row->delete();
			});

		$backups = Backup::where([
			'object_id' => $object_id,
			'object_type' => $object_type]
			)->orderBy('updated_at', 'desc')->get();

		if( $backup ){
			$backup->timeAgo = $backup->updated_at->diffForHumans();
			$updated = $backup->updated_at;
			dump($updated);
			$backup->updated_at = Carbon::parse($updated)->format('M d, Y g:i:s a');
		}

		return response()->json([ 'backup' => $backup, 'created' => $created, 'backups' => $backups ]);
	}

	public function all()
	{
		$object_id = $request->object_id;
		$object_type = $request->object_type;

		$backups = Backup::where([
			'object_id' => $object_id,
			'object_type' => $object_type]
			)->orderBy('updated_at', 'desc')->get();

		return response()->json([ 'backups' => $backups ]);
	}

	public function getBackup($id)
	{
		$backup = Backup::find($id);
		if( !$backup ){
			abort(404);
		}
		return response()->json(['backup' => $backup]);
	}

    public function heartbeat()
    {
        return response()->json(['success' => true]);
    }
}
