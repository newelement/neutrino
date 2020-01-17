<?php
namespace Newelement\Neutrino\Events;
use Illuminate\Queue\SerializesModels;
use Newelement\Neutrino\Models\Comment;
use Newelement\Neutrino\Models\Entry;

class CommentSubmitted
{
    use SerializesModels;

	public $comment;
	public $entry;

    public function __construct(Comment $comment, Entry $entry)
    {
        $this->comment = $comment;
		$this->entry = $entry;
    }
}
