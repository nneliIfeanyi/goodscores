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
  public function findTeacher($username)
  {
    $this->db->query("SELECT * FROM teachers WHERE username = :username OR email = :username");
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
  public function findTeacherById($id)
  {
    $this->db->query("SELECT * FROM teachers WHERE id = :id");
    $this->db->bind(':id', $id);

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
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND username = :username OR email = :username");
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

  // Get class by ID
  public function getSingleClass($id)
  {
    $this->db->query("SELECT * FROM classes WHERE id = :id;");
    $this->db->bind(':id', $id);
    $results = $this->db->single();

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


  // Add Class
  public function addClass($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO classes (sch_id, user_id, classname, num_rows, num_rows2, choice, duration) 
      VALUES (:sch_id, :user_id, :classname, :obj_num_rows, :theory_num_rows, :choice, :duration)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':classname', $data['classname']);
    $this->db->bind(':obj_num_rows', $data['obj_num_rows']);
    $this->db->bind(':theory_num_rows', $data['theory_num_rows']);
    $this->db->bind(':choice', $data['choice']);
    $this->db->bind(':duration', $data['duration']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }


  // Add Subject
  public function addSubject($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO subjects (sch_id, user_id, subject) 
      VALUES (:sch_id, :user_id, :subject)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':subject', $data['subject']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }



  // Delete Class
  public function deleteClass($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM classes WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }



  // Delete Subject
  public function deleteSubject($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM subjects WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function editClass($data)
  {
    // Prepare Query
    $this->db->query('UPDATE classes SET classname = :classname, num_rows = :num_rows, num_rows2 = :num_rows2, choice = :choice, duration = :duration WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':classname', $data['classname']);
    $this->db->bind(':num_rows', $data['num_rows']);
    $this->db->bind(':num_rows2', $data['num_rows2']);
    $this->db->bind(':choice', $data['choice']);
    $this->db->bind(':duration', $data['duration']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }


  // Edit | update teacher profile
  public function editProfile($data)
  {
    // Prepare Query
    $this->db->query('UPDATE teachers SET name = :name, username = :username, phone = :phone, role = :role, email = :email WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':username', $data['username']);
    $this->db->bind(':phone', $data['phone']);
    $this->db->bind(':role', $data['role']);
    $this->db->bind(':email', $data['email']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Edit | update teacher profile
  public function changePass($data)
  {
    // Prepare Query
    $this->db->query('UPDATE teachers SET password = :password WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':password', $data['new']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Edit | update teacher profile photo
  public function editPhoto($id, $photo)
  {
    // Prepare Query
    $this->db->query('UPDATE teachers SET img = :img WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);
    $this->db->bind(':img', $photo);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Edit | update teacher profile photo
  public function removePhoto($id)
  {
    // Prepare Query
    $this->db->query('UPDATE teachers SET img = :img WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);
    $this->db->bind(':img', '');

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
