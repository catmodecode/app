<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Lumen\Routing\Router;

class RoutesCommand extends Command
{
    protected $name = 'Show route list';

    protected $signature = 'route:list';

    protected $description = 'Show route list';

    public function __construct(protected Router $router)
    {
        parent::__construct();
    }

    public function handle()
    {
        $routes = collect($this->router->namedRoutes)->map(
            fn ($val, $key) =>
            collect([$key, $val])
        );
        $this->table(['name', 'route'], $routes);
        return self::SUCCESS;
    }
}
