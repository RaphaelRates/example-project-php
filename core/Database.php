<?php

namespace NodacWeb\Core;

use PDO;
use PDOException;

class Database {
    public $connection;

    /**
     * Constructor Singleton
     */
    public function __construct() {
        $this->connection = $this->connect();
    }

    /**
     * Método responsável por conectar ao banco de dados.
     *
     * @return PDO|null Retorna a conexão PDO ou null em caso de falha.
     */
    public function connect() {
        $host = getenv('DB_HOST');        
        $dbname = getenv('DB_NAME');     
        $user = getenv('DB_USER');       
        $password = getenv('DB_PASSWORD'); 
        $port = getenv('DB_PORT');      
        
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $user, $password, [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            return $pdo;
        } catch (PDOException $e) {
            echo "Falha na conexão: " . $e->getMessage();
            return null; 
        }
    }

    /**
     * Método responsável por retornar a conexão
     *
     * @return PDO|null
     */
    public function getConnection() {
        return $this->connection;
    }
}
