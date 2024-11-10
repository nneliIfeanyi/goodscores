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
  public function deleteCustomObj($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM custom_obj WHERE id = :id');

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
  public function deleteObjAll($id)
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
  public function deleteParamsData($id, $section)
  {
    // Prepare Query
    $this->db->query('DELETE FROM params WHERE paperID = :id AND section = :section');

    // Bind Values
    $this->db->bind(':id', $id);
    $this->db->bind(':section', $section);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
  // Delete all obj
  public function deleteTheoryAll($id,)
  {
    // Prepare Query
    $this->db->query('DELETE FROM theory WHERE paperID = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function deleteCustomAll($id)
  {
    // Prepare Query
    $this->db->query('DELETE FROM custom WHERE paperID = :id');

    // Bind Values
    $this->db->bind(':id', $id);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }



  //Check if Exam Paper exist
  public function checkIfPaperExist($data)
  {
    $this->db->query("SELECT * FROM core WHERE sch_id = :sch_id AND user_id = :user_id AND class = :class AND subject = :subject AND term = :term AND year = :year");
    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
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
  public function checkIfSectionExist($data)
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :user_id AND class = :class AND subject = :subject AND term = :term AND year = :year AND section = :section");
    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
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
  public function getParamsByPaperID($paper_id, $section)
  {
    $this->db->query("SELECT * FROM params WHERE paperID = :paperID AND section = :section;");
    // Bind Values
    $this->db->bind(':paperID', $paper_id);
    $this->db->bind(':section', $section);

    $row = $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $row;
    } else {
      return false;
    }
  }
  // Get params by paper id
  public function getParamsFromCore($paper_id)
  {
    $this->db->query("SELECT * FROM core WHERE paperID = :paperID;");
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
    $this->db->query('INSERT INTO params (sch_id, user_id, paperID, class, subject, term, section, year, num_rows, instruction, tag) 
      VALUES (:sch_id, :user_id, :paperID, :class, :subject, :term, :section, :year, :num_rows, :instruction, :tag)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':term', $data['term']);
    $this->db->bind(':section', $data['section']);
    $this->db->bind(':year', $data['year']);
    $this->db->bind(':num_rows', $data['num_rows']);
    $this->db->bind(':instruction', $data['instruction']);
    $this->db->bind(':tag', $data['tag']);
    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Add Exam Params
  public function addExamCore($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO core (sch_id, user_id, paperID, class, subject, term, year) 
      VALUES (:sch_id, :user_id, :paperID, :class, :subject, :term, :year)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':term', $data['term']);
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

  //Get Objective Questions
  public function getCustomObj($paper_id)
  {
    $this->db->query("SELECT * FROM custom_obj WHERE sch_id = :sch_id AND paperID = :paperID;");

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

  //Get custom Questions
  public function getCustomContent($paper_id)
  {
    $this->db->query("SELECT * FROM custom WHERE sch_id = :sch_id AND paperID = :paperID AND user_id = :user_id;");

    $this->db->bind(':user_id', $_SESSION['user_id']);
    $this->db->bind(':paperID', $paper_id);
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);

    $this->db->single();

    //Check Rows
    if ($this->db->rowCount() > 0) {
      return $this->db->single();
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
    $this->db->query('INSERT INTO objectives (sch_id, user_id, paperID, question, isSubjective, opt1, opt2, opt3, opt4, ans, img, subInstruction) 
      VALUES (:sch_id, :user_id, :paperID, :question, :isSubjective, :opt1, :opt2, :opt3, :opt4, :ans, :img, :sub_ins)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':question', $data['question']);
    $this->db->bind(':isSubjective', $data['isSubjective']);
    $this->db->bind(':opt1', $data['opt1']);
    $this->db->bind(':opt2', $data['opt2']);
    $this->db->bind(':opt3', $data['opt3']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':ans', $data['ans']);
    $this->db->bind(':img', $data['daigram']);
    $this->db->bind(':sub_ins', $data['sub_ins']);
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Insert objective questions to objectives table
  public function setObjQuestion($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO custom_obj (sch_id, user_id, paperID, question, opt1, opt2, opt3, opt4, img) 
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
    $this->db->query('INSERT INTO theory (sch_id, user_id, paperID, questionID, questionA, questionB, questionC, questionD, img) 
      VALUES (:sch_id, :user_id, :paperID, :questionID, :questionA, :questionB, :questionC, :questionD, :img)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':questionID', $data['questionID']);
    $this->db->bind(':questionA', $data['questionA']);
    $this->db->bind(':questionB', $data['questionB']);
    $this->db->bind(':questionC', $data['questionC']);
    $this->db->bind(':questionD', $data['questionD']);
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

  // Get All Exam params ie. Archives 
  // 
  public function getArchive($id)
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id ORDER BY id DESC;");
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':id', $id);
    $this->db->resultset();
    return $this->db->resultset();
  }

  // Get exam params for current term
  public function getRecentParams()
  {
    $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :user_id AND term = :term AND year = :year ORDER BY id DESC;");
    $this->db->bind(':sch_id', $_COOKIE['sch_id']);
    $this->db->bind(':user_id', $_SESSION['user_id']);
    $this->db->bind(':term', TERM);
    $this->db->bind(':year', SCH_SESSION);
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
  public function getCustomObjById($id)
  {
    $this->db->query("SELECT * FROM custom_obj WHERE id = :id");

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
    $this->db->query('UPDATE objectives SET question = :question, isSubjective = :isSubjective, opt1 = :opt1, opt2 = :opt2, opt3 = :opt3, opt4 = :opt4, ans = :ans, img = :img, subInstruction = :sub_ins WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':question', $data['question']);
    $this->db->bind(':opt1', $data['opt1']);
    $this->db->bind(':opt2', $data['opt2']);
    $this->db->bind(':opt3', $data['opt3']);
    $this->db->bind(':opt4', $data['opt4']);
    $this->db->bind(':img', $data['daigram']);
    $this->db->bind(':ans', $data['ans']);
    $this->db->bind(':isSubjective', $data['isSubjective']);
    $this->db->bind(':sub_ins', $data['sub_ins']);
    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function updateCustom($data)
  {
    $this->db->query('UPDATE custom SET content = :content WHERE paperID = :paperID');
    $this->db->bind(':content', $data['content']);
    $this->db->bind(':paperID', $data['paperID']);
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Update Post
  public function updateCustomObj($data)
  {
    // Prepare Query
    $this->db->query('UPDATE custom_obj SET question = :question, opt1 = :opt1, opt2 = :opt2, opt3 = :opt3, opt4 = :opt4, img = :img WHERE id = :id');

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

  public function updateParams($data)
  {
    // Prepare Query
    $this->db->query('UPDATE params SET subject = :subject, class = :class, tag = :tag, section = :section, num_rows = :num_rows, instruction = :instruction WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':class', $data['class']);
    $this->db->bind(':subject', $data['subject']);
    $this->db->bind(':num_rows', $data['num_rows']);
    $this->db->bind(':instruction', $data['instruction']);
    $this->db->bind(':tag', $data['tag']);
    $this->db->bind(':section', $data['section']);

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
    $this->db->query('UPDATE theory SET questionA = :questionA, questionB = :questionB, questionC = :questionC, questionD = :questionD, img = :img WHERE id = :id');

    // Bind Values
    $this->db->bind(':id', $data['id']);
    $this->db->bind(':questionA', $data['questionA']);
    $this->db->bind(':questionB', $data['questionB']);
    $this->db->bind(':questionC', $data['questionC']);
    $this->db->bind(':questionD', $data['questionD']);
    $this->db->bind(':img', $data['daigram']);

    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  // Add Comprehension
  public function setCustom($data)
  {
    // Prepare Query
    $this->db->query('INSERT INTO custom (sch_id, user_id, paperID, content) 
      VALUES (:sch_id, :user_id, :paperID, :content)');

    // Bind Values
    $this->db->bind(':sch_id', $data['sch_id']);
    $this->db->bind(':user_id', $data['user_id']);
    $this->db->bind(':paperID', $data['paperID']);
    $this->db->bind(':content', $data['content']);
    //Execute
    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
