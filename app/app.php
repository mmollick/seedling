<?php

define('__VERSION__', '0.1.1');

/**
 * Load composer
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Load Configuration
 */
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

/**
 * Initialize slim
 */
$app = new \Slim\Slim();

/**
 * Load services
 */
$services = require_once(__DIR__ . '/services.php');

/**
 * Load Bootstrap
 */

require_once __DIR__ . '/bootstrap.php';

/**
 * Setup default routes
 */
$app->get('/', 'App\Controllers\Controller:getHello');
$app->get('/health', 'App\Controllers\Controller:getHealth');
/**
 * Start slim
 */
$app->run();