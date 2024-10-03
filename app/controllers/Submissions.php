<?php
class Submissions extends Controller
{
  public $postModel;
  public $userModel;
  public function __construct()
  {
    if (!isset($_SESSION['user_id'])) {
      redirect('users/login');
    }
    // Load Models
    $this->postModel = $this->model('Post');
    $this->userModel = $this->model('User');
  }


  // Add Class
  public function add_class()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST

      $data = [
        'classname' => val_entry($_POST['classname']),
        //'obj_num_rows' => val_entry($_POST['obj_num_rows']),
        // 'theory_num_rows' => val_entry($_POST['theory_num_rows']),
        'user_id' => $_SESSION['user_id'],
        'sch_id' => $_COOKIE['sch_id'],
        // 'choice' => val_entry($_POST['choice']),
        // 'duration' => val_entry($_POST['duration'])
      ];

      //$data['classname'] = strtolower($data['classname']);
      //$data['classname'] = preg_replace('/\s+/', '-', $data['classname']);

      if ($this->userModel->addClass($data)) {
        // Redirect to login
        flash('msg', 'Class is added successfully');
        redirect('users/classes');
      } else {
        die('Something went wrong');
      }
    } else { // Not a post request
      die('Something went wrong');
    }
  }

  public function edit_class($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST

      $data = [
        'classname' => val_entry($_POST['classname']),
        'num_rows' => val_entry($_POST['obj_num_rows']),
        'num_rows2' => val_entry($_POST['theory_num_rows']),
        'id' => $id,
        'choice' => val_entry($_POST['choice']),
        'duration' => val_entry($_POST['duration'])
      ];

      //$data['classname'] = strtolower($data['classname']);
      //$data['classname'] = preg_replace('/\s+/', '-', $data['classname']);

      if ($this->userModel->editClass($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        redirect('users/classes');
      } else {
        die('Something went wrong');
      }
    } else { // Not a post request
      die('Something went wrong');
    }
  }


  public function profile($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST

      $data = [
        'name' => val_entry($_POST['name']),
        'username' => val_entry($_POST['username']),
        'phone' => val_entry($_POST['phone']),
        'email' => val_entry($_POST['email']),
        'id' => $id,
        'role' => val_entry($_POST['role'])
      ];
      if ($this->userModel->editProfile($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        redirect('users/profile/' . $id);
      } else {
        die('Something went wrong');
      }
    } else { // Not a post request
      die('Something went wrong');
    }
  }

  // Remove | delete teacher profile image
  public function remove_img($id)
  {
    $user = $this->userModel->findTeacherById($id);
    if ($this->userModel->removePhoto($id)) {
      unlink($user->img);
      unset($_SESSION['photo']);
      flash('msg', 'Profile photo removed..', 'alert alert-danger bg-danger text-light border-0 alert-dismissible');
      echo "<script>
                history.go(-1)
          </script>";
    } else {
      die('Something went wrong');
    }
  } // End remove | delete teacher profile image

  // Change | update teacher password
  public function password($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $user = $this->userModel->findTeacherById($id);
      $data = [
        'id' => $id,
        'old' => val_entry($_POST['old']),
        'new' => val_entry($_POST['new']),
        'err' => '',
      ];
      if (password_verify($data['old'], $user->password)) {
        // validation complete
        $data['new'] = password_hash($data['new'], PASSWORD_DEFAULT);
        if ($this->userModel->changePass($data)) {
          // Redirect to login
          flash('msg', 'Password reset is successfull..');
          redirect('users/profile/' . $id);
        } else {
          die('Something went wrong');
        }
      } else {
        flash('msg', 'The current password entered is incorrect..', 'alert alert-danger bg-danger text-light border-0 alert-dismissible');
        redirect('users/profile/' . $id);
      }
    } else {
      die('Something went wrong');
    }
  }

  // Add Subject
  public function add_subject()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'subject' => trim($_POST['subject']),
        'user_id' => $_SESSION['user_id'],
        'sch_id' => $_COOKIE['sch_id']
      ];

      if ($this->userModel->addSubject($data)) {
        // Redirect to login
        flash('msg', 'Subject Added Successfully');
        redirect('users/subjects');
      } else {
        die('Something went wrong');
      }
    } else {
      die('Something went wrong');
    }
  }

  // Delete Subject
  public function delete_subject($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->userModel->deleteSubject($id)) {
        // Redirect to login
        flash('msg', 'Subject is deleted successfull', 'alert alert-danger');
        redirect('users/subjects');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('users/subjects');
    }
  }


  // Delete Class
  public function delete_class($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->userModel->deleteClass($id)) {
        // Redirect to login
        flash('msg', 'Class is deleted successfully', 'alert alert-danger');
        redirect('users/classes');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('users/classes');
    }
  }



  // Exam Params
  public function set($param)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'paperID' => '',
        'class' => $_POST['class'],
        'subject' => $_POST['subject'],
        'term' => $_POST['term'],
        'section' => $param,
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'year' => $_POST['year'],
        'num_rows' => $_POST['num_rows'],
        'duration' => $_POST['duration'],
        'instruction' => $_POST['instruction'],
      ];

      // For Theory Question
      if ($param == 'theory_questions') {
        // check if exam theory exists 
        $exam_exits = $this->postModel->checkExamParams($data); // checks for objectives initiated
        $exam_exits2 = $this->postModel->checkExamParams2($data); // checks if theory exist
        if (!$exam_exits) { // Exam param did not exist both as obj and as theory
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add2/' . $data['paperID']);
        } elseif ($exam_exits && !$exam_exits2) { // found objective in params but not theory
          //Append existing paperID
          $data['paperID'] = $exam_exits->paperID;
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add2/' . $data['paperID']);
        } elseif ($exam_exits && $exam_exits2) { // found both obj and theory in param
          $data['id'] = $exam_exits2->id;
          $this->postModel->updateParams($data);
          redirect('posts/add2/' . $exam_exits->paperID);
        }
      } elseif ($param == 'objectives_questions') {
        // Coming from users/set_question
        $exam_exits = $this->postModel->checkExamParams($data);
        $exam_exits2 = $this->postModel->checkExamParams2($data);
        if ($exam_exits && $exam_exits2) { // Objectives has been set
          // Exam has been set
          $data['id'] = $exam_exits2->id;
          $this->postModel->updateParams($data);
          redirect('posts/add/' . $exam_exits->paperID);
        } elseif ($exam_exits && !$exam_exits2) { // Only theory was been set
          // Insert Objedctive params with same paperID as theory
          $data['paperID'] = $exam_exits->paperID;
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add/' . $exam_exits->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add/' . $data['paperID']);
        }
      } // Coming from users/set_question ends
    } else { // Not a post request
      redirect('users/set_questions');
    }
  } // End set question method



  // Edit Theory question
  public function edit2($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'id' => $id,
        'question-A' => $_POST['question-A'],
        'A-i' => $_POST['A-i'],
        'A-ii' => $_POST['A-ii'],
        'A-iii' => $_POST['A-iii'],
        'A-iv' => $_POST['A-iv'],
        'question-B' => $_POST['question-B'],
        'B-i' => $_POST['B-i'],
        'B-ii' => $_POST['B-ii'],
        'B-iii' => $_POST['B-iii'],
        'B-iv' => $_POST['B-iv'],
        'question-C' => $_POST['question-C'],
        'C-i' => $_POST['C-i'],
        'C-ii' => $_POST['C-ii'],
        'C-iii' => $_POST['C-iii'],
        'question-D' => $_POST['question-D']
      ];
      if (empty($_SESSION['daigram'])) { // Update has no diagram
        $data['daigram'] = $_POST['daigram'];
        if ($this->postModel->updateTheory($data)) {
          flash('msg', 'Question is Updated');
          redirect('posts/edit2/' . $id);
        } else {
          die('Something went wrong');
        }
      } else {
        $data['daigram'] = $_SESSION['daigram'];
        if ($this->postModel->updateTheory($data)) {
          unset($_SESSION['daigram']);
          flash('msg', 'Question is Updated');
          redirect('posts/edit2/' . $id);
        } else {
          die('Something went wrong');
        }
      }
    } else { // Not a post request
      die('Something went wrong..');
    }
  }
}
