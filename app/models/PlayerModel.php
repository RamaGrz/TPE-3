<?php

require_once './app/models/Model.php';

class PlayerModel extends Model{

    function getJugadores($campo, $orden, $inicio, $limite){
        $query = $this->dataBase->prepare("SELECT * FROM jugadores ORDER BY $campo $orden LIMIT $inicio,$limite");
        $query->execute();
        
        return $query->fetchAll(PDO::FETCH_OBJ);
    }
    
    function getJugadoresFiltrados($nacionalidad, $campo, $orden, $inicio, $limite){
        $query = $this->dataBase->prepare("SELECT * FROM jugadores WHERE nacionalidad = ? ORDER BY $campo $orden LIMIT $inicio,$limite");
        $query->execute([$nacionalidad]);

        return $query->fetchAll(PDO::FETCH_OBJ);
    }


    function getCantidadJugadores() {
        $query = $this->dataBase->prepare('SELECT COUNT(*) as cantidad FROM jugadores');
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
    
        if ($result) {
            return $result->cantidad;
        } else {
            return 0; // En caso de error o si no se encuentra ningún jugador.
        }
    }    

    function getJugadorById($id){
        $query = $this->dataBase->prepare('SELECT jugadores.*, clubes.nombre AS nombre_club FROM jugadores INNER JOIN clubes ON jugadores.id_club = clubes.id_club WHERE id_jugador = ?');
        $query->execute([$id]);

        $jugador = $query->fetch(PDO::FETCH_OBJ);
        return $jugador;
    }

    function agregarJugador($nombre, $nacionalidad, $posicion, $edad,$id_club){
        $query = $this->dataBase->prepare('INSERT INTO jugadores (nombre,  nacionalidad, posicion,edad,  id_club) VALUES (?,?,?,?,?)');
        $query->execute([$nombre, $nacionalidad, $posicion, $edad,$id_club]);
        
        return $this->dataBase->lastInsertId();
    }

    function modificarJugador($id,$nombre,  $nacionalidad, $posicion,$edad,$id_club){
        $query = $this->dataBase->prepare('UPDATE jugadores SET nombre = ?, nacionalidad = ?, posicion = ?, edad = ?, id_club = ? WHERE id_jugador = ?');
        $query->execute([$nombre, $nacionalidad, $posicion, $edad,$id_club,$id]);

        $filasAfectadas = $query->rowCount();
        return $filasAfectadas;
    }

    function borrarJugador($id){
        $query = $this->dataBase->prepare('DELETE FROM jugadores WHERE id_jugador = ?');
        $query->execute([$id]);
    }

}