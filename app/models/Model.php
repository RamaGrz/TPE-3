<?php
require_once './config.php';

class Model {
    protected $dataBase;

    function __construct() {
        $this->dataBase = new PDO('mysql:host='. DB_HOST .';charset=utf8', DB_USER, DB_PASSWORD);
        $this->deploy();
    }

    function deploy() {
        // Verificar si la base de datos "tpe_web2" existe
        $query = $this->dataBase->query('SHOW DATABASES LIKE "futbol-db"');
        $databaseExists = $query->rowCount() > 0;

        if (!$databaseExists) {
            // Si la base de datos no existe, créala
            $this->dataBase->exec('CREATE DATABASE futbol-db');
        }

        // Seleccionar la base de datos "tpe_web2"
        $this->dataBase->exec('USE `futbol-db`');

        // A continuación, puedes agregar la creación de tablas y la inserción de datos para las tablas que necesites en la base de datos "tpe_web2".

        // Creación de la tabla "usuarios" (sin restricciones de clave externa)
        $this->dataBase->exec('
            CREATE TABLE IF NOT EXISTS `users` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `userName` varchar(50) NOT NULL,
                `password` varchar(255) NOT NULL,
                PRIMARY KEY (`id`)
            )
        ');

        // Insertar datos en la tabla "usuarios" solo si no existen registros
        $query = $this->dataBase->query('SELECT * FROM `users`');
        if ($query->rowCount() == 0) {
            $password = 'admin'; // Cambia esto por la contraseña deseada
            $passwordEncriptada = password_hash($password, PASSWORD_DEFAULT);
            $this->dataBase->exec('
                INSERT INTO `users` (`userName`, `password`) VALUES
                ("webadmin", "' . $passwordEncriptada . '")
            ');
        }

        

       
    }
}