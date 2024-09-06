<?php
  class Post {
    private $db;
    
    public function __construct(){
      $this->db = new Database;
    }


    // Add Class
    public function addClass($data){
      // Prepare Query
      $this->db->query('INSERT INTO classes (sch_id, user_id, classname, num_rows, num_rows2) 
      VALUES (:sch_id, :user_id, :classname, :obj_num_rows, :theory_num_rows)');

      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':classname', $data['classname']);
      $this->db->bind(':obj_num_rows', $data['obj_num_rows']);
      $this->db->bind(':theory_num_rows', $data['theory_num_rows']);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }


     // Add Subject
    public function addSubject($data){
      // Prepare Query
      $this->db->query('INSERT INTO subjects (sch_id, user_id, subject) 
      VALUES (:sch_id, :user_id, :subject)');

      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':subject', $data['subject']);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }



     // Delete Class
    public function deleteClass($id){
      // Prepare Query
      $this->db->query('DELETE FROM classes WHERE id = :id');

      // Bind Values
      $this->db->bind(':id', $id);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }


     // Delete single obj
    public function deleteObj($id){
      // Prepare Query
      $this->db->query('DELETE FROM objectives WHERE id = :id');

      // Bind Values
      $this->db->bind(':id', $id);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete all obj
    public function deleteObjall($id){
      // Prepare Query
      $this->db->query('DELETE FROM objectives WHERE paperID = :id');

      // Bind Values
      $this->db->bind(':id', $id);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete all obj
    public function deleteParamsData($id){
      // Prepare Query
      $this->db->query('DELETE FROM params WHERE paperID = :id');

      // Bind Values
      $this->db->bind(':id', $id);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }



     // Delete Subject
    public function deleteSubject($id){
      // Prepare Query
      $this->db->query('DELETE FROM subjects WHERE id = :id');

      // Bind Values
      $this->db->bind(':id', $id);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }


    //Check if Exam obj Params Already Exit
    public function checkExamParams($data){
      $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND class = :class AND subject = :subject AND term = :term AND year = :year");
      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':class', $data['class']);
      $this->db->bind(':subject', $data['subject']);
      $this->db->bind(':term', $data['term']);
      $this->db->bind(':year', $data['year']);
      $row = $this->db->single();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $row;
      } else {
        return false;
      }
    }

    //Check if Exam obj Params Already Exit
    public function checkExamParams2($data){
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
      if($this->db->rowCount() > 0){
        return $row;
      } else {
        return false;
      }
    }

    // Get params by paper id
    public function getParamsByPaperID($paper_id){
      $this->db->query("SELECT * FROM params WHERE paperID = :paperID;");
      // Bind Values
      $this->db->bind(':paperID', $paper_id);

      $row = $this->db->single();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $row;
      } else {
        return false;
      }
    }

    // Add Exam Params
    public function addExamParams($data){
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
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    //Check Objective Questions RowCount
    public function checkObjectivesNumRows($paper_id, $sch_id){
      $this->db->query("SELECT * FROM objectives WHERE sch_id = :sch_id AND paperID = :paperID;");

      $this->db->bind(':sch_id', $sch_id);
      $this->db->bind(':paperID',$paper_id);

      $this->db->resultset();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $this->db->rowCount();
      } else {
        return false;
      }
    }

    //Check Objective Questions RowCount
    public function checkTheoryNumRows($paper_id, $sch_id){
      $this->db->query("SELECT * FROM theory WHERE sch_id = :sch_id AND paperID = :paperID;");

      $this->db->bind(':sch_id', $sch_id);
      $this->db->bind(':paperID',$paper_id);

      $this->db->resultset();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $this->db->rowCount();
      } else {
        return false;
      }
    }

    //Get Objective Questions
    public function getObjectives($paper_id){
      $this->db->query("SELECT * FROM objectives WHERE paperID = :paperID;");

      //$this->db->bind(':sch_id', $sch_id);
      $this->db->bind(':paperID',$paper_id);

      $this->db->resultset();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $this->db->resultset();
      } else {
        return false;
      }
    }

    //Check subject num_rows
    public function checkSubjectNumRows($class, $user_id, $sch_id){
      $this->db->query("SELECT * FROM classes WHERE sch_id = :sch_id AND user_id = :user_id AND classname = :class;");

      $this->db->bind(':sch_id', $sch_id);
      $this->db->bind(':user_id', $user_id);
      $this->db->bind(':class',$class);

      $num_rows = $this->db->single();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $num_rows;
      } else {
        return false;
      }
    }


    // Get All Activities
    public function getAllActivities($sch_id,$user_id){
      $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :user_id;");

      $this->db->bind(':sch_id', $sch_id);
      $this->db->bind(':user_id', $user_id);

      $results = $this->db->resultset();

      //Check Rows
      if($this->db->rowCount() > 0){
        return $results;
      } else {
        return false;
      }
    }

    // Insert objective questions to objectives table
    public function setQuestions($data){
      // Prepare Query
      $this->db->query('INSERT INTO objectives (sch_id, user_id, paperID, question, opt1, opt2, opt3, opt4) 
      VALUES (:sch_id, :user_id, :paperID, :question, :opt1, :opt2, :opt3, :opt4)');

      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':paperID', $data['paperID']);
      $this->db->bind(':question', $data['question']);
      $this->db->bind(':opt1', $data['opt1']);
      $this->db->bind(':opt2', $data['opt2']);
      $this->db->bind(':opt3', $data['opt3']);
      $this->db->bind(':opt4', $data['opt4']);
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Insert theory questions to theory table
    public function setQuestions2($data){
      // Prepare Query
      $this->db->query('INSERT INTO theory (sch_id, user_id, paperID, question, img) 
      VALUES (:sch_id, :user_id, :paperID, :question, :img)');

      // Bind Values
      $this->db->bind(':sch_id', $data['sch_id']);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':paperID', $data['paperID']);
      $this->db->bind(':question', $data['question']);
      $this->db->bind(':img', $data['img']);
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }


    // Get All Initiated Exam setting activity
    // Limit 3
    public function getParamsLimit3(){
      $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id ORDER BY id DESC LIMIT 3;");

      $this->db->bind(':sch_id', $_COOKIE['sch_id']);
      $this->db->bind(':id', $_SESSION['user_id']);
      $this->db->resultset();

      return $this->db->resultset();
    }


     // Get All Initiated Exam setting activity
    // Row Count
    public function getParamsRowCount(){
      $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id;");

      $this->db->bind(':sch_id', $_COOKIE['sch_id']);
      $this->db->bind(':id', $_SESSION['user_id']);
      $this->db->resultset();

      return $this->db->rowCount();
    }

     // Get All Initiated Exam setting activity
    // 
    public function getParams(){
      $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND user_id = :id ORDER BY id DESC;");
      $this->db->bind(':sch_id', $_COOKIE['sch_id']);
      $this->db->bind(':sch_id', $_COOKIE['sch_id']);
      $this->db->bind(':id', $_SESSION['user_id']);
      $this->db->resultset();

      return $this->db->resultset();
    }


    // Get Post By ID
    public function getObjById($id){
      $this->db->query("SELECT * FROM objectives WHERE id = :id");

      $this->db->bind(':id', $id);
      
      $row = $this->db->single();

      return $row;
    }



































    

    // Update Post
    public function updateObj($data){
      // Prepare Query
      $this->db->query('UPDATE objectives SET question = :question, opt1 = :opt1, opt2 = :opt2, opt3 = :opt3, opt4 = :opt4 WHERE id = :id');

      // Bind Values
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':question', $data['question']);
      $this->db->bind(':opt1', $data['opt1']);
      $this->db->bind(':opt2', $data['opt2']);
      $this->db->bind(':opt3', $data['opt3']);
      $this->db->bind(':opt4', $data['opt4']);
      
      //Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

   
  }