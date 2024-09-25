<?php
class Post
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }



  // Delete single obj
  public function deleteObj($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM objectives WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Delete single obj
  public function deleteTheory($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM theory WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Delete all obj
  public function deleteObjall($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM objectives WHERE paperID = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Delete all obj
  public function deleteParamsData($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM params WHERE paperID = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }


  //Check if Exam obj Params Already Exit
  public function checkExamParams($data)
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND class = :class AND subject = :subject AND term = :term AND year = :year");
    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':term', $data['term']);
    $this->db->bind(':year', $data['year']);
    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $row;
    } else {
      return false;
    }
  }

  //Check if Exam obj Params Already Exit
  public function checkExamParams2($data)
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND class = :class AND subject = :subject AND term = :term AND year = :year AND section = :section");
    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':term', $data['term']);
    $this->db->bind(':year', $data['year']);
    $this->db->bind(':section', $data['section']);
    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $row;
    } else {
      return false;
    }
  }

  // Get params by paper id
  public function getParamsByPaperID($paper_id)
  {
    $this->db->query("SELECT * FROM params WHERE paperID = :paperID;");
    // Bind Values
    $this->db->bind(':paperID', $paper_id);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $row;
    } else {
      return false;
    }
  }

  // Add Exam Params
  public function addExamParams($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO params (sch_id, user_id, paperID, class, subject, term, section, year) 
      VALUES (:sch_id, :user_id, :paperID, :class, :subject, :term, :section, :year)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':term', $data['term']);
    $this->db->bind(':section', $data['section']);
    $this->db->bind(':year', $data['year']);
    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  //Check Objective Questions RowCount
  public function checkObjectivesNumRows($paper_id, $sch_id)
  {
    $this->db->query("SELECT * FROM objectives WHERE sch_id = :sch_id AND paperID = :paperID;");

    $this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':paperID', $paper_id);

    $this->db->resultset();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->rowCount();
    } else {
      return false;
    }
  }

  //Check Objective Questions RowCount
  public function checkTheoryNumRows($paper_id, $sch_id)
  {
    $this->db->query("SELECT * FROM theory WHERE sch_id = :sch_id AND paperID = :paperID;");

    $this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':paperID', $paper_id);

    $this->db->resultset();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->rowCount();
    } else {
      return false;
    }
  }

  //Get Objective Questions
  public function getObjectives($paper_id)
  {
    $this->db->query("SELECT * FROM objectives WHERE sch_id = :sch_id AND paperID = :paperID;");

    //$this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':paperID', $paper_id);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

    $this->db->resultset();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->resultset();
    } else {
      return false;
    }
  }


  //Get Theory Questions
  public function getTheory($paper_id)
  {
    $this->db->query("SELECT * FROM theory WHERE sch_id = :sch_id AND paperID = :paperID;");

    //$this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':paperID', $paper_id);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

    $this->db->resultset();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->resultset();
    } else {
      return false;
    }
  }

  public function pullEach($question_id, $paper_id)
  {
    $this->db->query("SELECT * FROM theory WHERE sch_id = :sch_id AND paperID = :paperID AND questionID = :question_id;");

    //$this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':paperID', $paper_id);
    $this->db->bind(':question_id', $question_id);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

    $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->single();
    } else {
      return false;
    }
  }




  //Check subject num_rows
  public function checkSubjectNumRows($class, $user_id, $sch_id)
  {
    $this->db->query("SELECT * FROM classes WHERE sch_id = :sch_id AND user_id = :user_id AND classname = :class;");

    $this->db->bind(':sch_id', $sch_id);
    $this->db->bind(':user_id', $user_id);
    $this->db->bind(':class', $class);

    $num_rows = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $num_rows;
    } else {
      return false;
    }
  }


  // // Get All Activities
  // public function getAllActivities($sch_id, $user_id)
  // {
  //   $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :user_id;");

  //   $this->db->bind(':sch_id', $sch_id);
  //   $this->db->bind(':user_id', $user_id);

  //   $results = $this->db->resultset();

  //   //Check Rows
  //   if ($this->db->rowCount() > 0) {
  //     return $results;
  //   } else {
  //     return false;
  //   }
  // }

  // Insert objective questions to objectives table
  public function setQuestions($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO objectives (sch_id, user_id, paperID, question, opt1, opt2, opt3, opt4, img) 
      VALUES (:sch_id, :user_id, :paperID, :question, :opt1, :opt2, :opt3, :opt4, :img)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':question', $data['question']);
    $this->db->bind(':opt1', $data['opt1']);
    $this->db->bind(':opt2', $data['opt2']);
    $this->db->bind(':opt3', $data['opt3']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':img', $data['daigram']);
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }


  // Insert theory questions to theory table
  public function setQuestions2($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO theory (sch_id, user_id, paperID, questionID, questionA, questionB, questionC, questionD, img, Ai, Aii, Aiii, Aiv, Bi, Bii, Biii, Biv, Ci, Cii, Ciii) 
      VALUES (:sch_id, :user_id, :paperID, :questionID, :questionA, :questionB, :questionC, :questionD, :img, :Ai, :Aii, :Aiii, :Aiv, :Bi, :Bii, :Biii, :Biv, :Ci, :Cii, :Ciii)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':questionID', $data['questionID']);
    $this->db->bind(':questionA', $data['question-A']);
    $this->db->bind(':questionB', $data['question-B']);
    $this->db->bind(':questionC', $data['question-C']);
    $this->db->bind(':questionD', $data['question-D']);
    $this->db->bind(':Ai', $data['A-i']);
    $this->db->bind(':Aii', $data['A-ii']);
    $this->db->bind(':Aiii', $data['A-iii']);
    $this->db->bind(':Aiv', $data['A-iv']);
    $this->db->bind(':Bi', $data['B-i']);
    $this->db->bind(':Bii', $data['B-ii']);
    $this->db->bind(':Biii', $data['B-iii']);
    $this->db->bind(':Biv', $data['B-iv']);
    $this->db->bind(':Ci', $data['C-i']);
    $this->db->bind(':Cii', $data['C-ii']);
    $this->db->bind(':Ciii', $data['C-iii']);
    $this->db->bind(':img', $data['img']);
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }



  // Get All Initiated Exam setting activity
  // Row Count
  public function getParamsRowCount()
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id;");

    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':id', $_SESSION['user_id']);
    $this->db->resultset();

    return $this->db->rowCount();
  }

  // Get All Initiated Exam setting activity
  // 
  public function getParams()
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id ORDER BY id DESC;");
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':id', $_SESSION['user_id']);
    $this->db->resultset();

    return $this->db->resultset();
  }


  // Get Post By ID
  public function getObjById($id)
  {
    $this->db->query("SELECT * FROM objectives WHERE id = :id");

    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }

  // Get Post By ID
  public function getTheoryById($id)
  {
    $this->db->query("SELECT * FROM theory WHERE id = :id");

    $this->db->bind(':id', $id);

    $row = $this->db->single();

    return $row;
  }


  // Update Post
  public function updateObj($data)
  {
    // Prepare Query
    $this->db->query('UPDATE objectives SET question = :question, opt1 = :opt1, opt2 = :opt2, opt3 = :opt3, opt4 = :opt4, img = :img WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':question', $data['question']);
    $this->db->bind(':opt1', $data['opt1']);
    $this->db->bind(':opt2', $data['opt2']);
    $this->db->bind(':opt3', $data['opt3']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':img', $data['daigram']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Update Post
  public function updateTheory($data)
  {
    // Prepare Query
    $this->db->query('UPDATE theory SET questionA = :questionA, questionB = :questionB, questionC = :questionC,questionD = :questionD, Ai = :Ai, Aii = :Aii, Aiii = :Aiii, Aiv = :Aiv, Bi = :Bi, Bii = :Bii, Biii = :Biii, Biv = :Biv, Ci = :Ci, Cii = :Cii, Ciii = :Ciii, img = :img WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':questionA', $data['question-A']);
    $this->db->bind(':questionB', $data['question-B']);
    $this->db->bind(':questionC', $data['question-C']);
    $this->db->bind(':questionD', $data['question-D']);
    $this->db->bind(':Ai', $data['A-i']);
    $this->db->bind(':Aii', $data['A-ii']);
    $this->db->bind(':Aiii', $data['A-iii']);
    $this->db->bind(':Aiv', $data['A-iv']);
    $this->db->bind(':Bi', $data['B-i']);
    $this->db->bind(':Bii', $data['B-ii']);
    $this->db->bind(':Biii', $data['B-iii']);
    $this->db->bind(':Biv', $data['B-iv']);
    $this->db->bind(':Ci', $data['C-i']);
    $this->db->bind(':Cii', $data['C-ii']);
    $this->db->bind(':Ciii', $data['C-iii']);
    $this->db->bind(':img', $data['daigram']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
