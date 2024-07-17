<?php

require __DIR__."/vendor/autoload.php";

use App\Http\Router;
use App\Utils\View;

define('URL', 'http://localhost/Stingray');

View::init([
    'URL' => URL
]);

$router = new Router(URL);
include __DIR__.'/routes/pages.php';
$router->run()->sendResponse();
