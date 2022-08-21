<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->afterApplicationCreated(function () {
            // Thank you StackOverflow <3 
           $this->artisan('migrate', ['--path' => 'database/migrations/banking_system']);

        });

        return $app;
    }
}
