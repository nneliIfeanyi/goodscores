<?php
class Page
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

    // Get single school by ID
    public function getSchool($id)
    {
        $this->db->query("SELECT * FROM school WHERE id = :id");
        $this->db->bind(':id', $id);

        $row = $this->db->single();

        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Edit | update teacher profile
    public function editProfile($data)
    {
        // Prepare Query
        $this->db->query('UPDATE school SET name = :name, username = :username, phone = :phone, motto = :motto, address = :address WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':motto', $data['motto']);
        $this->db->bind(':address', $data['address']);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
