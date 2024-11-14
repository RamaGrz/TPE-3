<?php

require_once './libs/router.php';
require_once './config.php';

require_once './app/controllers/PlayerApiController.php';
require_once './app/controllers/UserApiController.php';

$router = new Router();

$router->addRoute('jugadores','GET','PlayerApiController','getJugadores');
$router->addRoute('jugadores/:ID','GET','PlayerApiController','getJugadores');
$router->addRoute('jugadores/:ID','DELETE','PlayerApiController','deleteJugador');
$router->addRoute('jugadores/:ID', 'PUT', 'PlayerApiController', 'updateJugador');
$router->addRoute('jugadores', 'POST', 'PlayerApiController', 'agregarJugador');

$router->addRoute('user/token', 'GET', 'UserApiController', 'getToken');

$router->route($_GET['resource'], $_SERVER['REQUEST_METHOD']);
    
 ?>