<?php
namespace Newelement\Neutrino\Commands;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Newelement\Neutrino\Facades\Neutrino;
use DB;

class DomainMigrationCommand extends Command
{

    protected $name = 'neutrino:domainmigrate';

    protected $description = 'Updates any old domains with the new domain.';


    public function fire()
    {
        return $this->handle();
    }

    public function handle()
    {

        $oldDomain = $this->ask('Enter the old domain:');
        $newDomain = $this->ask('Enter the new domain to replace the old domain:');

        if( !strlen($oldDomain) || !strlen($newDomain) ){
            $this->error('Please enter your old and new domain!');
        } else {
            DB::statement( DB::raw("UPDATE pages SET content = replace(content, :olddomain, :newdomain)"), [ 'olddomain' => $oldDomain, 'newdomain' => $newDomain ]);
            DB::statement( DB::raw("UPDATE posts SET content = replace(content, :olddomain, :newdomain)"), [ 'olddomain' => $oldDomain, 'newdomain' => $newDomain ]);

            DB::statement( DB::raw("UPDATE pages SET block_content = replace(block_content::TEXT,:olddomain,:newdomain)::json"), [ 'olddomain' => $oldDomain, 'newdomain' => $newDomain ]);
            DB::statement( DB::raw("UPDATE posts SET block_content = replace(block_content::TEXT,:olddomain,:newdomain)::json"), [ 'olddomain' => $oldDomain, 'newdomain' => $newDomain ]);

            $this->info('Clearing cache.');
            $this->call('cache:clear');
            $this->call('view:clear');

            $this->info('Domain has been updated in pages and posts.');
        }

    }



}
