<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

    if (getenv('APP_ENV') !== 'prod') {
        $command = 'php ' . dirname(__DIR__) . '/bin/console doctrine:database:drop --force --env=test';
        passthru($command);
        $command = 'php ' . dirname(__DIR__) . '/bin/console doctrine:schema:update --env=test --force --complete --no-interaction';
        passthru($command);
        $command = 'php ' . dirname(__DIR__) . '/bin/console doctrine:fixtures:load -n --env=test --group=test';
        passthru($command);
    }
}
