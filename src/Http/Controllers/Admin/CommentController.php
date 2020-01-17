<?php
namespace Newelement\Neutrino\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Newelement\Neutrino\Facades\Neutrino;
use Newelement\Neutrino\Models\Setting;
use Newelement\Neutrino\Models\Entry;
use Newelement\Neutrino\Models\Comment;

class CommentController extends Controller
{

	public function moderateComments()
	{
		$comments = Comment::where('approved', 0)->orderBy('created_at', 'desc')->paginate(30);
		return view( 'neutrino::admin.entries.moderate' , [ 'comments' => $comments ]);
	}

	public function all()
	{
		$comments = Comment::where('approved', 1)->orderBy('created_at', 'desc')->paginate(30);
		return view( 'neutrino::admin.entries.comments' , [ 'comments' => $comments ]);
	}

	public function replyComment(Request $request, $id)
	{

	}

	public function approveComment(Request $request, $id)
	{
		$comment = Comment::find($id);
		$comment->approved = 1;
		$comment->approved_by = auth()->user()->id;
		$comment->approved_at = \Carbon\Carbon::now();
		$comment->save();
		return redirect('/admin/comments')->with('success', 'Comment approved.');
	}

	public function deleteComment($id)
	{
		$comment = Comment::find($id);
		$comment->delete();
		return redirect('/admin/comments')->with('success', 'Comment deleted.');
	}

}
