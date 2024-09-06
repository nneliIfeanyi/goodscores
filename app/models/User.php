<?php
class User
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  // Add User / Register Teacher
  public function registerTeacher($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO teachers (sch_id, name, email, phone, username, password) 
      VALUES (:sch_id, :name, :email, :phone, :username, :password)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':password', $data['password']);
    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Add User / Register Teacher
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

  // Find User BY Email
  public function findUserByEmail($email)
  {
    $this->db->query("SELECT * FROM teachers WHERE email = :email");
    $this->db->bind(':email', $email);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Find Sch BY Email
  public function findSchByEmail($email)
  {
    $this->db->query("SELECT * FROM school WHERE email = :email");
    $this->db->bind(':email', $email);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  // Find Teacher BY Username
  public function findTeacherByUsername($username)
  {
    $this->db->query("SELECT * FROM teachers WHERE username = :username");
    $this->db->bind(':username', $username);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
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

  // Login / Authenticate User
  public function login($username, $sch_id, $password)
  {
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND username = :username");
    $this->db->bind(':username', $username);
    $this->db->bind(':sch_id', $sch_id);

    $row = $this->db->single();

    $hashed_password = $row->password;
    if (password_verify($password, $hashed_password)) {
      return $row;
    } else {
      return false;
    }
  }

  // Find User By ID
  public function getUserById($id)
  {
    $this->db->query("SELECT * FROM teachers WHERE id = :id");
    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }


  // Get All Subjects Per User
  public function getUserSubjects($id)
  {
    $this->db->query("SELECT * FROM subjects WHERE user_id = :id;");
    $this->db->bind(':id', $id);
    $results = $this->db->resultset();

    return $results;
  }

  // Get All Classes Per User
  public function getUserClasses($id)
  {
    $this->db->query("SELECT * FROM classes WHERE user_id = :id;");
    $this->db->bind(':id', $id);
    $results = $this->db->resultset();

    return $results;
  }


  // Get All Subjects Per User
  public function getUserSubjectsRowCount($id)
  {
    $this->db->query("SELECT * FROM subjects WHERE user_id = :id;");
    $this->db->bind(':id', $id);
    $this->db->resultset();

    return $this->db->rowCount();
  }

  // Get All Classes Per User
  public function getUserClassesRowCount($id)
  {
    $this->db->query("SELECT * FROM classes WHERE user_id = :id;");
    $this->db->bind(':id', $id);
    $this->db->resultset();

    return $this->db->rowCount();
  }
}
