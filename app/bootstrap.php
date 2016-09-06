<?php

/**
 * wrap getenv to accept a default
 * @param $key
 * @param null $default
 * @return null
 */
function env($key, $default = null) {
    if(getenv($key))
        return $key;

    return $default;
}

/**
 * Setup slim app
 */
$app->setName('Seedling');
$app->config([
    'debug' => true,
    'version' => __VERSION__
]);

/**
 * Setup predis client
 */
$app->container->singleton('predis', function() {
    return new \Predis\Client(getenv);
});

/**
 * Setup monolog
 */
$app->container->singleton('log', function () use($app)
{
    $log = new Monolog\Logger($app->getName());
    $log->pushHandler(new Monolog\Handler\StreamHandler('../logs/application.log', \Monolog\Logger::DEBUG));
    return $log;
});

/**
 * Register the service providers
 */
foreach($services as $service) {
    $class = new $service();
    $class->register($app);
}