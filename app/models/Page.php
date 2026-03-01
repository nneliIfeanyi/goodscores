<?php
class Page
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Get single school by ID
    public function getSchool($id)
    {
        $this->db->query("SELECT * FROM school WHERE user_id = :id");
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
        $this->db->query('UPDATE school SET name = :name, motto = :motto, address = :address WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':motto', $data['motto']);
        $this->db->bind(':address', $data['address']);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Add User / Register school
    public function registerSch($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO school (user_id, name, motto, address) 
      VALUES (:user_id, :name, :motto, :address)');

        // Bind Values
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':name', $data['name']);
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
