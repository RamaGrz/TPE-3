<?php

require_once './app/controllers/ApiController.php';
require_once './app/models/PlayerModel.php';
require_once './app/models/ClubModel.php';
require_once './app/helpers/AuthApiHelper.php';

class PlayerApiController extends ApiController{
    private $playerModel;
    private $clubModel;
    private $authApiHelper;

    public function __construct(){
        parent::__construct();
        $this->playerModel = new PlayerModel();
        $this->clubModel = new ClubModel();
        $this->authApiHelper = new AuthApiHelper();
    }

    public function getJugadores($params = []){
        if (empty($params)){ // VARIOS ITEMS
            // Defino variables y controlo parametros GET primero.
            // PAGINAMIENTO:            
            $inicio = 0;
            $limite = $this->playerModel->getCantidadJugadores(); // Por default el limite es la cantidad de jugadores que tenemos para que siempre se muestren todos.
            // Parametro GET 'limite': si se recibe un valor mayor al max de items de la tabla se devolverán todos los items, si se recibe string simple se devolverá un arreglo vacío, si se recibe string con caracteres especiales (código) avisará que el valor no es el esperado.
            if (!empty($_GET['pagina']) && $_GET['pagina'] >= 1 && $_GET['pagina'] <= $limite && !empty($_GET['limite']) && $_GET['limite'] >= 1){
                $pagina = intval($_GET['pagina']);
                $limite = intval($_GET['limite']);
                if ($pagina>1){
                    $inicio = ($pagina * $limite) - ($limite);
                }
            } else if (empty($_GET['pagina']) && !empty($_GET['limite']) && $_GET['limite'] >= 1){
                $this->view->response('Falta el valor de la pagina', 404);
                return;
            } else if (empty($_GET['limite']) && !empty($_GET['pagina']) && $_GET['pagina'] >= 1 && $_GET['pagina'] <= $limite){
                $this->view->response('Falta el valor de el limite', 404);
                return;
            } else if (!empty($_GET['pagina']) && !empty($_GET['limite']) && $_GET['limite'] >= 1){
                $this->view->response('El valor para pagina no es el esperado', 404);
                return;
            } else if (!empty($_GET['limite']) && !empty($_GET['pagina']) && $_GET['pagina'] >= 1 && $_GET['pagina'] <= $limite){
                $this->view->response('El valor para limite no es el esperado', 404);
                return;
            }
            
            // ORDENAMIENTO:
            // Por default los jugadores se devuelven ordenados por nombre en orden ascendente.
            $campo = 'nombre'; 
            $orden = 'ASC';
            if (!empty($_GET['campo'])){
                if ($_GET['campo'] == 'nombre' || $_GET['campo'] == 'edad' || $_GET['campo'] == 'nacionalidad' || $_GET['campo'] == 'posicion'|| $_GET['campo'] == 'id_club'){
                    $campo = $_GET['campo'];
                    if (!empty($_GET['orden'])){
                        if ($_GET['orden'] == 'ASC' || $_GET['orden'] == 'DESC'|| $_GET['orden'] == 'asc' || $_GET['orden'] == 'desc'){
                            $orden = $_GET['orden'];
                        // Si se manipula la url para enviar parametroGet orden=DESC sin enviar orden, se devolverán los jugadores por default. Es decir, por nombre ascendete.
                        } else {
                            $this->view->response('El valor para el tipo de orden no es el esperado', 404);
                            return;
                        }
                    } 
                } else{
                    $this->view->response('El valor para el campo de orden no es el esperado', 404);
                    return;
                }
            }
            // FILTRADO:
            // Si el valor no corresponde con un país de un jugador devuelve un arreglo vacío.
            if (!empty($_GET['nacionalidad'])){
                $nacionalidad = $_GET['nacionalidad'];
                $jugadores = $this->playerModel->getJugadoresFiltrados($nacionalidad, $campo, $orden, $inicio, $limite);
            } else{
                $jugadores = $this->playerModel->getJugadores($campo, $orden, $inicio, $limite);
            }
            
            $this->view->response($jugadores, 200);

        // ITEM ESPECIFICO    
        } else{
            $jugador = $this->playerModel->getJugadorById($params[':ID']);
            if ($jugador){
                $this->view->response($jugador, 200);
            } else{
                $this->view->response('El jugador con el id= '. $params[':ID'] . ' no existe', 404);
            }
        }
    }

    function updateJugador($params = []){
        $user = $this->authApiHelper->currentUser();
        if(!$user){
            $this->view->response('Unauthorized', 401);
            return;
        }
        $id_jugador = $params[':ID'];
        $jugador = $this->playerModel->getJugadorById($id_jugador);
        
        if ($jugador){
            $body = $this->getData();
            $nombre = $body->nombre;
            $edad = $body->edad;
            $nacionalidad = $body->nacionalidad;
            $posicion = $body->posicion;
            $id_club = $body->id_club;

            if (empty($nombre) || empty($edad) || empty($nacionalidad) || empty($posicion) || empty($id_club)) {
                $this->view->response("Complete todos los datos", 400);
            } else{          
                $club = $this->clubModel->getClub($id_club);
                if($club){
                    $filasAfectadas = $this->playerModel->modificarJugador($id_jugador, $nombre, $nacionalidad, $posicion,$edad , $id_club);
                    if($filasAfectadas>0){
                        $this->view->response('El jugador con el id= '. $id_jugador . ' ha sido modificado', 200);
                    }
                    else{
                        $this->view->response("El jugador no fue modificado", 400);
                    }
                } else{
                    $this->view->response("El club del id $id_club no existe", 404);
                }
            }   
        } else{
            $this->view->response('El jugador con el id= '. $id_jugador . ' no existe', 404);
        }
    }

    function deleteJugador($params = []){
        $id_jugador = $params[':ID'];
        $jugador = $this->playerModel->getJugadorById($id_jugador);

        if ($jugador){
            $this->playerModel->borrarJugador($id_jugador);
            $jugadorAEliminar = $this->playerModel->getJugadorById($id_jugador);
            if ($jugadorAEliminar){
                $this->view->response('El jugador con el id= '. $id_jugador . ' no pudo ser elimiano', 404);
            } else{
                $this->view->response('El jugador con el id= '. $id_jugador . ' ha sido eliminado', 200);
            }
        } else{
            $this->view->response('El jugador con el id= '. $id_jugador . ' no existe', 404);
        }
    }

    function agregarJugador($params = []){
        $user = $this->authApiHelper->currentUser();
        if(!$user){
            $this->view->response('Unauthorized', 401);
            return;
        }
        $body = $this->getData();
        $nombre = $body->nombre;
        $edad = $body->edad;
        $nacionalidad = $body->nacionalidad;
        $posicion = $body->posicion;
        $id_club = $body->id_club;

        if (empty($nombre) || empty($edad) || empty($nacionalidad) || empty($posicion) || empty($id_club)) {
            $this->view->response("Complete los datos", 400);
        } else{
            $club = $this->clubModel->getClub($id_club);
            if($club){
                $id = $this->playerModel->agregarJugador($nombre,  $nacionalidad, $posicion,$edad,  $id_club);
                if ($id){
                    $jugador = $this->playerModel->getJugadorById($id);
                    $this->view->response($jugador, 201);
                } else{
                    $this->view->response("La carga falló", 404);
                }
            } else{
                $this->view->response("El club del id $id_club no existe", 404);
            }
        }
    }
}