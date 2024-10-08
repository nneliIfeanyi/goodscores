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

      $data = [
        'paperID' => '',
        'class' => val_entry($_POST['class']),
        'subject' => val_entry($_POST['subject']),
        'term' => val_entry($_POST['term']),
        'section' => $param,
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'year' => val_entry($_POST['year']),
        'num_rows' => val_entry($_POST['num_rows']),
        'tag' => val_entry($_POST['section_tag']),
        'instruction' => val_entry($_POST['instruction']),
      ];
      $paper_exist = $this->postModel->checkIfPaperExist($data);
      $section_exist = $this->postModel->checkIfSectionExist($data);
      // For Theory Question
      if ($param == 'theory_questions') {
        if ($paper_exist && $section_exits) {
          redirect('posts/add2/' . $section_exist->paperID);
        } elseif ($paper_exist && !$section_exist) { // Other section exist
          // Insert theory section with same paperID as theory
          $data['paperID'] = $paper_exist->paperID;
          $data['section_alt'] = '';
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add2/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          $data['section_alt'] = '';
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add2/' . $data['paperID']);
        }
      } elseif ($param == 'objectives_questions') {
       
        if ($paper_exist && $section_exits) {
          redirect('posts/add/' . $section_exist->paperID);
        } elseif ($paper_exist && !$section_exist) { // Only theory was set
          // Insert Objedctive params with same paperID as theory
          $data['paperID'] = $paper_exist->paperID;
          $data['section_alt'] = '';
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          $data['section_alt'] = '';
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add/' . $data['paperID']);
        }
      }elseif ($param == 'custom') {
       
        if ($paper_exist) {
          redirect('posts/custom/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          //
          $data['content'] = '';
          $this->postModel->setCustom($data);
          redirect('posts/custom/' . $data['paperID']);
        }
      } elseif ($param == 'others'){
        if ($paper_exist && $section_exits) {
          redirect('posts/add4/' . $section_exist->paperID);
        } elseif ($paper_exist && !$section_exist) {
          $data['paperID'] = $paper_exist->paperID;
          $data['section_alt'] = val_entry($_POST['section_alt']);
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add4/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          $data['section_alt'] = val_entry($_POST['section_alt']);
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add4/' . $data['paperID']);
        }
      }
    } else { // Not a post request
      redirect('users/set_questions');
    }
  } // End set question method

  public function review($param)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'paperID' => $_POST['paperID'],
        'class' => $_POST['class'],
        'subject' => $_POST['subject'],
        'tag' => $_POST['section_tag'],
        'section' => $param,
        'num_rows' => $_POST['num_rows'],
        'instruction' => $_POST['instruction'],
      ];
      if ($param == 'objectives_questions') {
        $paramsID = $this->postModel->getParamsByPaperID($data['paperID'], 'objectives_questions');
        $data['id'] = $paramsID->id;
        $data['section_alt'] = '';
        $this->postModel->updateParams($data);
        flash('msg', 'Update successful');
        redirect('posts/add/' . $data['paperID']);
      } elseif ($param == 'theory_questions') {
        $paramsID = $this->postModel->getParamsByPaperID($data['paperID'], 'theory_questions');
        $data['id'] = $paramsID->id;
        $data['section_alt'] = '';
        $this->postModel->updateParams($data);
        flash('msg', 'Update successful');
        redirect('posts/add2/' . $data['paperID']);
      } elseif ($param == 'others') {
        $paramsID = $this->postModel->getParamsByPaperID($data['paperID'], 'others');
        $data['id'] = $paramsID->id;
        $data['section_alt'] = val_entry($_POST['section_alt']);
        $this->postModel->updateParams($data);
        flash('msg', 'Update successful');
        redirect('posts/add4/' . $data['paperID']);
      }
    } else {
      die('Something went wrong');
    }
  }


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
