<?php

namespace App\Console\Commands;

use App\Http\Controllers\V1\ImportPostJobController;
use Illuminate\Console\Command;

class GetPostsFromRemote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remote-posts:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $controller = new ImportPostJobController();
        $controller->getPosts();
        return 0;
    }
}
