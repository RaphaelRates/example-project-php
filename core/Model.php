<?php

namespace NodacWeb\Core;

use NodacWeb\Core\Database;
use PDO;

class Model {
    protected $db;
    protected $conn; 

    public function __construct() {
        $this->db = new Database(); 
        $this->conn = $this->db->getConnection(); 
    }

    /**
     * Método responsável por verificar a existencia de uma tebal no banco de dados
     *
     * @param string $name
     * @param string $createQuery
     * @return bool
     */
    public function checkAndCreateTable($name, $createQuery) {
        if (empty($name) || preg_match('/[^a-zA-Z0-9_]/', $name)) {
            throw new InvalidArgumentException("Nome de tabela inválido: " . htmlspecialchars($name));
        }
        if ($this->validTable($name)) {
            return true;
        }
        $createStmt = $this->conn->prepare($createQuery);
        try {
            if ($createStmt->execute()) {
                return true;
            } else {
                throw new Exception("Erro ao criar a tabela: " . $name);
            }
        } catch (PDOException $e) {
            \Error($e);
            throw new Exception("Erro ao executar a consulta de criação: " . $e->getMessage());
        }
    }

    /**
     * Verifica se existe uma propriedade na tabela existente, caos exista ele 
     *  retorna true, se não ele retorna false
     *
     * @param string $table
     * @param string $property
     * @return bool
     */
    private function propertyExists($table, $property) {
        if($this->validTable($table)){
            $query = "SHOW COLUMNS FROM $table LIKE :property";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property', $property);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        }
        return false;
    }

    /**
     * Prepare a consulta para obter as tabelas existentes, retorna os
     * nomes das tabelas como um array
     *
     * @return void
     */
    private function getAllTables() {
        $query = 'SHOW TABLES';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Retorna se a tabela existe no banco de dados para validação de queries.
     *
     * @return void
     */
    private function validTable($table){
        $validTables = $this->getAllTables();
        if (!in_array($table, $validTables)) {
            return false;
        }
        return true;
    }

    /**
     * Métpdo resónsável por ler todos os elementos de uma table, recendo a 
     * table como parâmetro, usando a verificação de existencia da tabela no 
     * banco de dados 
     *
     * @param string $table
     * @return void
     */
    protected function getTable($table, $limit = NULL, $offset = NULL){
        if($this->validTable($table)){
            $query = 'SELECT * FROM ' . $table;
            if($limit != NULL) $query = 'SELECT * FROM ' . $table.' LIMIT '.$limit;
            if($offset != NULL) $query = 'SELECT * FROM ' . $table.' OFFSET '.$offset;
            if($limit != NULL && $offset != NULL) $query = 'SELECT * FROM ' . $table.' LIMIT '.$limit.' OFFSET '.$offset;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt; 
        }
    }
    
    /**
     * Método responsável por ler certos elemento da table atráves de uma propriedade 
     * junto ao valor da propriedade recebendo a tabela escolhida, o nome da
     *  propriedade e o valor de propriedade
     *
     * @param string $table
     * @param string $property
     * @param string $value
     * @return void
     */
    protected function getByProperty($table, $property, $value) {
        if($this->validTable($table) && $this->propertyExists($table, $property)){
            $query = "SELECT * FROM $table WHERE $property = :property";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property', $value);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
    }
    
    /**
     * Método responsável por executar uma atualização de um elemento em específico
     * no banco de dados atravé do seu Id
     *
     * @param string $table
     * @param array $propertyToUpdate
     * @param array $$contitions
     * @return void
     */
    protected function update($table, $propertiesToUpdate, $conditions) {
        if ($this->validTable($table)) {
            $updateParts = [];
            foreach ($propertiesToUpdate as $property => $value) {
                if ($this->propertyExists($table, $property)) {
                    $updateParts[] = "$property = :$property";
                } else {
                    throw new Exception("Property '$property' does not exist in table '$table'");
                }
            }
            $conditionParts = [];
            foreach ($conditions as $conditionProperty => $conditionValue) {
                if ($this->propertyExists($table, $conditionProperty)) {
                    $conditionParts[] = "$conditionProperty = :$conditionProperty";
                } else {
                    throw new Exception("Condition property '$conditionProperty' does not exist in table '$table'");
                }
            }

            $query = "UPDATE $table SET " . implode(', ', $updateParts) . " WHERE " . implode(' AND ', $conditionParts);
            $stmt = $this->conn->prepare($query);
            foreach ($propertiesToUpdate as $property => $value) {
                $stmt->bindParam(":$property", $propertiesToUpdate[$property]);
            }
            foreach ($conditions as $conditionProperty => $conditionValue) {
                $stmt->bindParam(":$conditionProperty", $conditions[$conditionProperty]);
            }
            if ($stmt->execute()) {
                return $stmt->rowCount();
            } else {
                throw new Exception("Failed to update record in table: $table");
            }
        }
        return 0;
    }
    

    /**
     * Método responsável por deletar certos elemtnso de uma tabela do banco de dados
     * atráves de uma propriedade junto ao valor no qual os elementos terão para serem
     * deletados da tabela.
     *
     * @param  $table
     * @param string $property
     * @param any $value
     * @return void
     */
    protected function deleteByProperty($table, $property, $value) {
        if ($this->validTable($table) && $this->propertyExists($table, $property)) {
            $query = "DELETE FROM $table WHERE $property = :property";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':property', $value);
            if ($stmt->execute()) {
                return $stmt->rowCount(); 
            } else {
                throw new Exception("Failed to delete record from table: $table");
            }
        } else {
            throw new InvalidArgumentException("Invalid table or property: " . htmlspecialchars($table) . " or " . htmlspecialchars($property));
        }
    }

    /**
     * Métodos responsável por Retornar o numero de elemtentos de uma tabela
     *
     * @param string $table
     * @return void
     */
    function getCountElements($table) {
        if($this->validTable($table)){
            $sql = "SELECT COUNT(*) AS total FROM $table";
            $resultado = $this->conn->prepare($sql);
            
            if ($resultado) {
                if ($resultado->execute()) {
                    $linha = $resultado->fetch(PDO::FETCH_ASSOC);
                    return $linha['total'] ?? 0;
                } else {
                    $errorInfo = $resultado->errorInfo();
                    throw new \Exception("Erro ao executar a consulta: " . $errorInfo[2]);
                }
            } else {
                throw new \Exception("Erro ao preparar a consulta SQL.");
            }
        }
    }

    /**
     * Método responsável por selecionar o elemtno próximo da tabela através do
     * id do proximo elemento
     *
     * @param string $table
     * @param int $currentId
     * @param string $direction
     * @return void
     */
    function getAdjacentUserId($table,$currentId, $direction) {
        if ($direction === 'next') {
            $query = "SELECT id FROM `$table` WHERE id > :currentId ORDER BY id ASC LIMIT 1";
        } elseif ($direction === 'previous') {
            $query = "SELECT id FROM `$table` WHERE id < :currentId ORDER BY id DESC LIMIT 1";
        } else {
            throw new Exception("Direção inválida. Use 'next' ou 'previous'.");
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':currentId', $currentId, PDO::PARAM_INT);
        $stmt->execute();
        $adjacentId = $stmt->fetchColumn();
        if ($adjacentId) {
            return $adjacentId;
        } else {
            return $currentId; 
        }
    }

    /**
     * Método responsável por pegar ou o maior ou o menor Elemento em questão de ID em uma tabela
     *
     * @param string $table
     * @param string $type
     * @return int | null
     */
    protected function getMaxMinxUserId($table,$type) {
        $type === "max" ? $query = "SELECT MAX(id) FROM $table":$query = "SELECT MIN(id) FROM $table"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $maxId = $stmt->fetchColumn();
        return $maxId; // Retorna o maior ou menor ID ou null se não houver registros
    }
}
