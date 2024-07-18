<?php

require __DIR__."/vendor/autoload.php";
require __DIR__."/resources/functions/functions.php";

use App\Http\Router;
use App\Utils\View;


define('URL', 'http://localhost/Stingray');

View::init([
    'URL' => URL
]);

// $router = new Router(URL);
// include __DIR__.'/routes/pages.php';
// $router->run()->sendResponse();

// This is a commentary
