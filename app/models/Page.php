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


    // Find Teacher BY Username
    public function findSchByUsername($username)
    {
        $this->db->query("SELECT * FROM school WHERE username = :username");
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Find Teacher BY Username
    public function findSchByEmail($username)
    {
        $this->db->query("SELECT * FROM school WHERE email = :username");
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Find Sch BY Email
    public function findSch($email)
    {
        $this->db->query("SELECT * FROM school WHERE email = :email OR username = :email");
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Add User / Register school
    public function registerSch($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO school (name, email, phone, username) 
      VALUES (:name, :email, :phone, :username)');

        // Bind Values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':username', $data['username']);
        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
