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
    if ($data['username'] == 'Admin' || $data['username'] == 'admin') {
      // Prepare Query
      $this->db->query('INSERT INTO teachers (sch_id, name, email, phone, username, role, password) 
      VALUES (:sch_id, :name, :email, :phone, :username, :role, :password)');

      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':phone', $data['phone']);
      $this->db->bind(':username', $data['username']);
      $this->db->bind(':role', 'Admin');
      $this->db->bind(':password', $data['password']);
      //Execute
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    } else {
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
  }



  // Find User BY Email
  public function findUserByEmail($email)
  {
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND email = :email");
    $this->db->bind(':email', $email);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

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
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND username = :username");
    $this->db->bind(':username', $username);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->single();

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
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND username = :username OR email = :username");
    $this->db->bind(':username', $username);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

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
  public function login($username, $password)
  {
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :sch_id AND username = :username OR email = :username");
    $this->db->bind(':username', $username);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

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
  public function getTeachers()
  {
    $this->db->query("SELECT * FROM teachers WHERE sch_id = :id;");
    $this->db->bind(':id', $_COOKIE['sch_id']);
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

  // Get subject by ID
  public function getSingleSubject($id)
  {
    $this->db->query("SELECT * FROM subjects WHERE id = :id;");
    $this->db->bind(':id', $id);
    $results = $this->db->single();

    return $results;
  }

  public function checkIfClassExist($classname)
  {
    $this->db->query("SELECT * FROM classes WHERE classname = :classname AND user_id = :user_id AND sch_id = :sch_id;");
    $this->db->bind(':classname', $classname);
    $this->db->bind(':user_id', $_SESSION['user_id']);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
  }

  public function checkIfSubjectExist($subject)
  {
    $this->db->query("SELECT * FROM subjects WHERE subject = :subject AND user_id = :user_id AND sch_id = :sch_id;");
    $this->db->bind(':subject', $subject);
    $this->db->bind(':user_id', $_SESSION['user_id']);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return true;
    } else {
      return false;
    }
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
    $this->db->query('INSERT INTO classes (sch_id, user_id, classname) 
      VALUES (:sch_id, :user_id, :classname)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':classname', $data['classname']);
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
    $this->db->query('UPDATE classes SET classname = :classname WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':classname', $data['classname']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function editSubject($data)
  {
    // Prepare Query
    $this->db->query('UPDATE subjects SET subject = :subject WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':subject', $data['subject']);

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
  public function coreEdit($data)
  {
    // Prepare Query
    $this->db->query('UPDATE core SET published = :published, duration = :duration, publishedAS = :publishedAS WHERE paperID = :id AND sch_id = :sch_id');

    // Bind Values
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':published', $data['published']);
    $this->db->bind(':duration', $data['duration']);
    $this->db->bind(':publishedAS', $data['publishAS']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
  public function setDuration($data)
  {
    // Prepare Query
    $this->db->query('UPDATE core SET duration = :duration WHERE paperID = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':duration', $data['duration']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
