<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\ObjectEditing;
use Carbon\Carbon;

class HeartBeatController extends Controller
{

    public function heartbeat(Request $request)
    {
        $edits = [];

        if( $request->object_user_edit ){

            $this->deleteOldEdits();

            $editObj = json_decode($request->object_user_edit);

            $this->cleanZombies($editObj);

            ObjectEditing::updateOrCreate(
                [
                    'object_id' => $editObj->id,
                    'object_type' => $editObj->object_type,
                    'user_id' => auth()->user()->id
                ],
                [
                    'updated_at' => now()
                ]
            );

        }

        if( $request->object_type ){
            $objectType = $request->object_type;
            $edits = $this->getEdits($objectType);
        }

        return response()->json(['success' => true, 'edits' => $edits ]);
    }

    public function expireEdit(Request $request)
    {
        if( $request->object_user_edit ){

            $this->deleteOldEdits();

            $editObj = json_decode($request->object_user_edit);

            ObjectEditing::where(
                [
                    'object_id' => $editObj->id,
                    'object_type' => $editObj->object_type,
                    'user_id' => auth()->user()->id
                ]
            )->delete();
        }
        return response()->json(['success' => true]);
    }

    private function getEdits($objectType)
    {
        $date = new \DateTime;
        $date->modify('-7 seconds');
        $formatted = $date->format('Y-m-d H:i:s');
        $edits = ObjectEditing::where([
            ['updated_at', '>=', $formatted],
            ['object_type', '=', $objectType]
        ])->with('user:id,name')->get();

        return $edits;
    }

    private function cleanZombies($obj)
    {
        ObjectEditing::where([
            [ 'object_id', '<>', $obj->id ],
            [ 'object_type', '=', $obj->object_type ],
            [ 'user_id', '=', auth()->user()->id ],
        ])->delete();
    }

    private function deleteOldEdits()
    {
        $date = new \DateTime;
        $date->modify('-20 minutes');
        $formatted = $date->format('Y-m-d H:i:s');
        ObjectEditing::where('updated_at', '<=', $formatted)->delete();
    }

}
