<?php

namespace App\Console\Commands;

use App\Actions\City\LoadCityAction;
use Illuminate\Console\Command;

class LoadCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-cities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Загрузить в БД список городов из cities.csv файла';

    /**
     * Execute the console command.
     */
    public function handle(LoadCityAction $action): void
    {
        $action();
    }
}
