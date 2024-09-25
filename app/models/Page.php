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
}
