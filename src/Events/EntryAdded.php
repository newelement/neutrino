<?php
namespace Newelement\Neutrino\Events;
use Illuminate\Queue\SerializesModels;
use Newelement\Neutrino\Models\Entry;

class EntryAdded
{
    use SerializesModels;

	public $entry;

    public function __construct(Entry $entry)
    {
        $this->entry = $entry;
    }
}
