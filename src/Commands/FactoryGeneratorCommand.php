<?php

namespace MaxQuebral\LaravelDoctrineFactory\Commands;

use Illuminate\Console\Command;

class FactoryGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doctrine:generate-factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate entity factory for testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dump(__METHOD__);
    }
}
