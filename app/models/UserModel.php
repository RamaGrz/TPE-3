<?php

require_once './app/models/Model.php';

class UserModel extends Model{

    function getUserByUsername($usuario){
        $query = $this->dataBase->prepare('SELECT * FROM usuarios WHERE usuario = ?');
        $query->execute([$usuario]);

        return $query->fetch(PDO::FETCH_OBJ);
    }

}