<?php 

namespace  NodacWeb\Core;

class Config {
    private static $instance = null;

    private function __construct() {
        $this->loadEnv();
    }

    /**
     * Método responsável por retornar a sua próría instância
     * @return stdClass 
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        
        return self::$instance;
    }

    /**
     * Método Responsável por carregar as variáveis de ambiente
     *
     * @return void
     */
    private function loadEnv() {
        $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) continue;
            putenv(trim($line)); 
        }
    }

    /**
     * Método responsável por pegar uma variavés de ambiente
     *
     * @param [type] $key
     * @return void
     */
    public function get($key) {
        return getenv($key);
    }
}


?>