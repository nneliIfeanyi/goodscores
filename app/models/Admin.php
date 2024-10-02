<?php
class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    //Get all registered schools
    public function getSchools()
    {
        $this->db->query("SELECT * FROM school;");
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    //Get all Maths symbols
    public function getEntities()
    {
        $this->db->query("SELECT * FROM entities;");
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    // Add Maths Symbols
    public function addEntity($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO entities (name, code) 
      VALUES (:name, :code)');

        // Bind Values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':code', $data['code']);
        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
