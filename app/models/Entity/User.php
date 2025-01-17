<?php

namespace NodacWeb\Models\Entity;

use NodacWeb\Core\Model;

class User extends Model{
    /**
     * @var integer
     */
    public $id = 1;

    /**
     * nome do canal
     *
     * @var string
     */
    public $name = 'Gema';

    /**
     * Nome da tabela dos dados dos usuarios
     *
     * @var string
     */
    private $table = "users";

    /**
     * url do site dele no youtube
     *
     * @var string
     */
    public $site = 'https://www.youtube.com/gemaplys';

    /**
     * lorem ipsum da descrição do canal
     *
     * @var string
     */
    public $description = "Fugiat id reprehenderit amet adipisicing esse. Enim ut mollit 
    quis exercitation quis velit ullamco magna ea dolor ut fugiat amet eu. Esse consequat
    ullamco elit consequat non ut ipsum velit nostrud consectetur non. Dolor quis do 
    occaecat eiusmod. Ea ullamco pariatur esse aliqua fugiat minim velit veniam in
     exercitation dolore ad commodo ad.";

    
    function cadastrar(){
        
        if(parent::checkAndCreateTable($this->table, $this->queryTable())) {
            $query = 'INSERT INTO ' . $this->table . ' (name, site, description) VALUES (:name, :site, :description)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':site', $this->site);
            $stmt->bindParam(':description', $this->description);
            
            if ($stmt->execute()) {
                return true;

            }
            return false;
        }
    }

    function getUsers($limit, $offset){
        return $this->getTable($this->table, $limit, $offset);
    }

    function getUserById($id){
        return $this->getByProperty($this->table,'id', $id);
    }

    function deleteUser($id){
        return $this->getByProperty($this->table,'id', $id);
    }

    function getTotalUsers(){
        return $this->getCountElements($this->table);
    }

    function MaxMinxId($type){
        return $this->getMaxMinxUserId($this->table,$type);
    }

    function updateUser(){
        $propertiesToUpdate = [
            'name' => $this->name,
            'site' => $this->site,
            'description' => $this->description
        ];
        
        $conditions = [
            'id' => $this->id,
        ];
        
        $this->update($this->table, $propertiesToUpdate, $conditions);
    }
    function deleteUserById($value){
        return $this->deleteByProperty($this->table, $property = 'id', $value);
    }
    function getAdjacentUserById($value, $direction){
        return $this->getAdjacentUserId($this->table,$value, $direction);
    }

    private function queryTable(){
        return "CREATE TABLE users (
                id INT PRIMARY KEY AUTO_INCREMENT,  -- Campo ID como chave primária com incremento automático
                name VARCHAR(255) NOT NULL,          -- Campo name para o nome do canal
                site VARCHAR(255) NOT NULL,          -- Campo site para a URL do site do YouTube
                description TEXT NOT NULL            -- Campo description para a descrição do canal
            );";
    }
    
}
