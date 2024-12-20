<?php
class Student
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    //Get all registered students
    public function getStudents()
    {
        $this->db->query("SELECT * FROM students WHERE sch_id = :sch_id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    public function studentsPerClass($class)
    {
        $this->db->query("SELECT * FROM students WHERE sch_id = :sch_id AND class = :class;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':class', $class);
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    public function countPerClass($class)
    {
        $this->db->query("SELECT * FROM students WHERE sch_id = :sch_id AND class = :class;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':class', $class);
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->rowCount();
        } else {
            return false;
        }
    }

    //Get students rowcount
    public function getStudentsCount()
    {
        $this->db->query("SELECT * FROM students WHERE sch_id = :sch_id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->resultset();
        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $this->db->rowCount();
        } else {
            return false;
        }
    }
    public function addStudent($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO students (sch_id, firstname, middlename, surname, fullname, class, gender, passkey) 
      VALUES (:sch_id, :firstname, :middlename, :surname, :fullname, :class, :gender, :passkey)');

        // Bind Values
        $this->db->bind(':sch_id', $data['sch_id']);
        $this->db->bind(':firstname', $data['firstname']);
        $this->db->bind(':middlename', $data['middlename']);
        $this->db->bind(':surname', $data['surname']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':class', $data['class']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':passkey', $data['regNo']);
        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function deleteStudent($id)
    {
        // Prepare Query
        $this->db->query('DELETE FROM students WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $id);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Find Student BY Username
    public function findStudentById($id)
    {
        $this->db->query("SELECT * FROM students WHERE id = :id");
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
    public function editStudent($data)
    {
        // Prepare Query
        $this->db->query('UPDATE students SET firstname = :firstname, middlename = :middlename, surname = :surname, fullname = :fullname,  gender = :gender, phone = :phone, email = :email, dob = :dob, class = :class, address = :address,religion = :religion, state = :state, country = :country WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':firstname', $data['firstname']);
        $this->db->bind(':middlename', $data['middlename']);
        $this->db->bind(':surname', $data['surname']);
        $this->db->bind(':fullname', $data['fullname']);
        $this->db->bind(':gender', $data['gender']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':dob', $data['dob']);
        $this->db->bind(':class', $data['class']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':religion', $data['religion']);
        $this->db->bind(':state', $data['state']);
        $this->db->bind(':country', $data['country']);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Edit | update student profile photo
    public function editPhoto($id, $photo)
    {
        // Prepare Query
        $this->db->query('UPDATE students SET img = :img WHERE id = :id');

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

    // Remove student profile photo
    public function removePhoto($id)
    {
        // Prepare Query
        $this->db->query('UPDATE students SET img = :img WHERE id = :id');

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

    // Find Teacher BY Username
    public function findStudent($passkey)
    {
        $this->db->query("SELECT * FROM students WHERE passkey = :passkey AND sch_id = :sch_id;");
        $this->db->bind(':passkey', $passkey);
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $row = $this->db->single();

        //Check Rows
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    // Get Cbt Params per class
    public function getCbtCore($paper_id)
    {
        $this->db->query("SELECT * FROM core WHERE sch_id = :sch_id AND term = :term AND year = :year AND paperID = :id AND published = :yes ORDER BY id DESC;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':term', TERM);
        $this->db->bind(':year', SCH_SESSION);
        $this->db->bind(':id', $paper_id);
        $this->db->bind(':yes', '1');
        $this->db->single();

        return $this->db->single();
    }

    public function getCbtParam($paper_id)
    {
        $this->db->query("SELECT * FROM params WHERE sch_id = :sch_id AND term = :term AND year = :year AND paperID = :id ORDER BY id DESC;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':term', TERM);
        $this->db->bind(':year', SCH_SESSION);
        $this->db->bind(':id', $paper_id);
        $this->db->single();

        return $this->db->single();
    }

    // Get All Cbt Params per class
    public function getCbtParams($class)
    {
        $this->db->query("SELECT * FROM core WHERE sch_id = :sch_id AND term = :term AND year = :year AND class = :class AND published = :yes ORDER BY id DESC;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':term', TERM);
        $this->db->bind(':year', SCH_SESSION);
        $this->db->bind(':class', $class);
        $this->db->bind(':yes', '1');
        $this->db->resultset();

        return $this->db->resultset();
    }



    // Get Cbt Question per class
    public function getCbtQuestions($paper_id)
    {
        $this->db->query("SELECT * FROM objectives WHERE sch_id = :sch_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->resultset();

        return $this->db->resultset();
    }

    public function getCbtRowCount($paper_id)
    {
        $this->db->query("SELECT * FROM objectives WHERE sch_id = :sch_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->resultset();

        return $this->db->rowCount();
    }

    public function insertScore($data)
    {
        if ($data['cbtTag'] == 'CA1') {
            // Prepare Query
            $this->db->query('INSERT INTO scores (sch_id, student_id, class, term, sub_ject, CA1, CA2, exam) 
      VALUES (:sch_id, :student_id, :class, :term, :sub_ject, :CA1, :CA2, :exam)');

            // Bind Values
            $this->db->bind(':sch_id', $data['sch_id']);
            $this->db->bind(':student_id', $data['student_id']);
            $this->db->bind(':class', $data['class']);
            $this->db->bind(':term', TERM);
            $this->db->bind(':sub_ject', $data['subject']);
            $this->db->bind(':CA1', $data['result']);
            $this->db->bind(':CA2', '');
            $this->db->bind(':exam', '');
            //Execute
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        } elseif ($data['cbtTag'] == 'CA2') {
            // Prepare Query
            $this->db->query('INSERT INTO scores (sch_id, student_id, class, term, sub_ject, CA1, CA2, exam) 
      VALUES (:sch_id, :student_id, :class, :term, :sub_ject, :CA1, :CA2, :exam)');

            // Bind Values
            $this->db->bind(':sch_id', $data['sch_id']);
            $this->db->bind(':student_id', $data['student_id']);
            $this->db->bind(':class', $data['class']);
            $this->db->bind(':term', TERM);
            $this->db->bind(':sub_ject', $data['subject']);
            $this->db->bind(':CA1', '');
            $this->db->bind(':CA2', $data['result']);
            $this->db->bind(':exam', '');
            //Execute
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        } elseif ($data['cbtTag'] == 'exam') {
            // Prepare Query
            $this->db->query('INSERT INTO scores (sch_id, student_id, class, term, sub_ject, CA1, CA2, exam) 
      VALUES (:sch_id, :student_id, :class, :term, :sub_ject, :CA1, :CA2, :exam)');

            // Bind Values
            $this->db->bind(':sch_id', $data['sch_id']);
            $this->db->bind(':student_id', $data['student_id']);
            $this->db->bind(':class', $data['class']);
            $this->db->bind(':term', TERM);
            $this->db->bind(':sub_ject', $data['subject']);
            $this->db->bind(':CA1', '');
            $this->db->bind(':CA2', '');
            $this->db->bind(':exam', $data['result']);
            //Execute
            if ($this->db->execute()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function checkIfExamTaken($paper_id)
    {
        $this->db->query("SELECT * FROM responses WHERE sch_id = :sch_id AND student_id = :student_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':student_id', $_SESSION['student_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->resultset();
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function checkRowExist($subject, $class, $term)
    {
        $this->db->query("SELECT * FROM scores WHERE sch_id = :sch_id AND student_id = :student_id AND sub_ject = :sub_ject AND class = :class AND term = :term;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':student_id', $_SESSION['student_id']);
        $this->db->bind(':sub_ject', $subject);
        $this->db->bind(':class', $class);
        $this->db->bind(':term', $term);
        $row = $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $row;
        } else {
            return false;
        }
    }

    public function initTime($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO timing (sch_id, student_id, paperID, startTime, endTime) 
      VALUES (:sch_id, :student_id, :paperID, :startTime, :endTime)');

        // Bind Values
        $this->db->bind(':sch_id', $data['sch_id']);
        $this->db->bind(':student_id', $data['student_id']);
        $this->db->bind(':paperID', $data['paperID']);
        $this->db->bind(':startTime', $data['startTime']);
        $this->db->bind(':endTime', $data['endTime']);
        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function pullTime($paper_id)
    {
        $this->db->query("SELECT * FROM timing WHERE sch_id = :sch_id AND student_id = :student_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':student_id', $_SESSION['student_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->single();

        return $this->db->single();
    }
    // Insert objective questions to objectives table
    public function setResponse($data)
    {
        // Prepare Query
        $this->db->query('INSERT INTO responses (sch_id, student_id, paperID, question, isSubjective, opt1, opt2, opt3, opt4, ans, img, subInstruction, response) 
      VALUES (:sch_id, :student_id, :paperID, :question, :isSubjective, :opt1, :opt2, :opt3, :opt4, :ans, :img, :sub_ins, :response)');

        // Bind Values
        $this->db->bind(':sch_id', $data['sch_id']);
        $this->db->bind(':student_id', $data['student_id']);
        $this->db->bind(':paperID', $data['paperID']);
        $this->db->bind(':question', $data['question']);
        $this->db->bind(':isSubjective', $data['isSubjective']);
        $this->db->bind(':opt1', $data['opt1']);
        $this->db->bind(':opt2', $data['opt2']);
        $this->db->bind(':opt3', $data['opt3']);
        $this->db->bind(':opt4', $data['opt4']);
        $this->db->bind(':opt4', $data['opt4']);
        $this->db->bind(':ans', $data['ans']);
        $this->db->bind(':img', $data['img']);
        $this->db->bind(':sub_ins', $data['subInstruction']);
        $this->db->bind(':response', $data['response']);
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getResponse($paper_id)
    {
        $this->db->query("SELECT * FROM responses WHERE sch_id = :sch_id AND student_id = :student_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':student_id', $_SESSION['student_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->resultset();

        return $this->db->resultset();
    }

    public function checkIfResponseExist($paper_id)
    {
        $this->db->query("SELECT * FROM responses WHERE sch_id = :sch_id AND student_id = :student_id AND paperID = :id;");
        $this->db->bind(':sch_id', $_COOKIE['sch_id']);
        $this->db->bind(':student_id', $_SESSION['student_id']);
        $this->db->bind(':id', $paper_id);
        $this->db->single();

        return $this->db->single();
    }

    public function getScores($class, $term, $subject)
    {
        $this->db->query("SELECT * FROM scores WHERE term =:term AND class =:class AND sub_ject = :sub_ject;");
        $this->db->bind(':term', $term);
        $this->db->bind(':sub_ject', $subject);
        $this->db->bind(':class', $class);
        $this->db->resultset();
        if ($this->db->rowCount() > 0) {
            return $this->db->resultset();
        } else {
            return false;
        }
    }

    public function getSingleScores($id)
    {
        $this->db->query("SELECT * FROM scores WHERE id = :id;");
        // $this->db->bind(':term', $term);
        // $this->db->bind(':sub_ject', $subject);
        // $this->db->bind(':class', $class);
        $this->db->bind(':id', $id);
        $this->db->single();
        if ($this->db->rowCount() > 0) {
            return $this->db->single();
        } else {
            return false;
        }
    }

    public function updateScoreRow3($id, $score)
    {
        // Prepare Query
        $this->db->query('UPDATE scores SET exam =:exam WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $id);
        $this->db->bind(':exam', $score);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function updateScoreRow2($id, $score)
    {
        // Prepare Query
        $this->db->query('UPDATE scores SET CA2 =:CA2 WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $id);
        $this->db->bind(':CA2', $score);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function updateScoreRow1($id, $score)
    {
        // Prepare Query
        $this->db->query('UPDATE scores SET CA1 =:CA1 WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $id);
        $this->db->bind(':CA1', $score);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function updateScores($data)
    {
        // Prepare Query
        $this->db->query('UPDATE scores SET CA1 = :CA1, CA2 = :CA2, exam =:exam WHERE id = :id');

        // Bind Values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':CA1', $data['CA1']);
        $this->db->bind(':CA2', $data['CA2']);
        $this->db->bind(':exam', $data['exam']);

        //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
