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
        'user_id' => $_SESSION['user_id'],
        'sch_id' => $_COOKIE['sch_id']
      ];

      //$data['classname'] = strtolower($data['classname']);
      //$data['classname'] = preg_replace('/\s+/', '-', $data['classname']);

      if ($this->userModel->checkIfClassExist($data['classname'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Class is already added!
          </p>
        ";
      } else {
        if ($this->userModel->addClass($data)) {
          flash('msg', 'Class is added successfully');
          $redirect = URLROOT . '/users/classes';
          echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
        } else {
          die('Something went wrong!');
        }
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
        'id' => $id,
      ];

      //$data['classname'] = strtolower($data['classname']);
      //$data['classname'] = preg_replace('/\s+/', '-', $data['classname']);

      if ($this->userModel->editClass($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        $redirect = URLROOT . '/users/classes';
        echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
      } else {
        die('Something went wrong');
      }
    } else { // Not a post request
      die('Something went wrong');
    }
  }

  public function edit_subject($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST

      $data = [
        'subject' => val_entry($_POST['subject']),
        'id' => $id,
      ];

      if ($this->userModel->editSubject($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        $redirect = URLROOT . '/users/subjects';
        echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
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
        $redirect = URLROOT . '/users/profile/' . $id;
        echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
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
      setcookie('photo', $user->img, time() - (3), '/');
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
          $redirect = URLROOT . '/users/profile/' . $id;
          echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
        } else {
          die('Something went wrong');
        }
      } else {
        flash('msg', 'The current password entered is incorrect..', 'alert alert-danger bg-danger text-light border-0 alert-dismissible');

        $redirect = URLROOT . '/users/profile/' . $id;
        echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
      }
    } else {
      die('Something went wrong');
    }
  }

  // Add Subject
  public function add_subject()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'subject' => val_entry($_POST['subject']),
        'user_id' => $_SESSION['user_id'],
        'sch_id' => $_COOKIE['sch_id']
      ];

      if ($this->userModel->checkIfSubjectExist($data['subject'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;You already added this subject!
          </p>
        ";
      } else {
        if ($this->userModel->addSubject($data)) {
          flash('msg', 'Subject is added successfully');
          $redirect = URLROOT . '/users/subjects';
          echo "><meta http-equiv='refresh' content='0; $redirect'>
        ";
        } else {
          die('Something went wrong!');
        }
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
        if ($paper_exist && $section_exist) {
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

        if ($paper_exist && $section_exist) {
          redirect('posts/add/' . $section_exist->paperID . '?section_alt=' . '');
        } elseif ($paper_exist && !$section_exist) { // Only theory was set
          // Insert Objedctive params with same paperID as theory
          $data['paperID'] = $paper_exist->paperID;
          $data['section_alt'] = '';
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add/' . $paper_exist->paperID . '?section_alt=' . '');
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
          redirect('posts/add/' . $data['paperID'] . '?section_alt=' . '');
        }
      } elseif ($param == 'custom') {

        if ($paper_exist) {
          redirect('posts/custom/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          // Add to params 
          $data['section_alt'] = '';
          $this->postModel->addExamParams($data);
          $data['content'] = '';
          $this->postModel->setCustom($data);
          redirect('posts/custom/' . $data['paperID']);
        }
      } elseif ($param == 'others') {
        if ($paper_exist && $section_exist) {
          redirect('posts/add/' . $section_exist->paperID . '?section_alt=' . val_entry($_POST['section_alt']));
        } elseif ($paper_exist && !$section_exist) {
          $data['paperID'] = $paper_exist->paperID;
          $data['section_alt'] = val_entry($_POST['section_alt']);
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question
          redirect('posts/add/' . $paper_exist->paperID . '?section_alt=' . val_entry($_POST['section_alt']));
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
          redirect('posts/add/' . $data['paperID'] . '?section_alt=' . val_entry($_POST['section_alt']));
        }
      }
    } else { // Not a post request
      redirect('users/set_questions');
    }
  } // End set question method

  public function review($param)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $data = [
        'paperID' => val_entry($_POST['paperID']),
        'class' => val_entry($_POST['class']),
        'subject' => val_entry($_POST['subject']),
        'tag' => val_entry($_POST['section_tag']),
        'section' => $param,
        'num_rows' => val_entry($_POST['num_rows']),
        'instruction' => val_entry($_POST['instruction']),
      ];
      if ($param == 'objectives_questions') {
        $paramsID = $this->postModel->getParamsByPaperID($data['paperID'], 'objectives_questions');
        $data['id'] = $paramsID->id;
        $data['section_alt'] = '';
        $this->postModel->updateParams($data);
        flash('msg', 'Update successful');
        redirect('posts/add/' . $data['paperID'] . '?section_alt=' . '');
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
        redirect('posts/add/' . $data['paperID'] . '?section_alt=' . val_entry($_POST['section_alt']));
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

  public function set2($param)
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
        if ($paper_exist && $section_exist) {
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
        // Exam questions has not been initiated
        //Generate paperID
        $data['paperID'] = substr(md5(time()), 22);
        $data['section_alt'] = '';
        //Initiate exam paper on the core table
        $this->postModel->addExamCore($data);
        //Initiate exam question on the params table
        $this->postModel->addExamParams($data);
        // Redirect to continue with set question 1
        redirect('posts/add/' . $data['paperID'] . '?section_alt=' . '');
      } elseif ($param == 'custom') {

        if ($paper_exist) {
          redirect('posts/custom/' . $paper_exist->paperID);
        } else {
          // Exam questions has not been initiated
          //Generate paperID
          $data['paperID'] = substr(md5(time()), 22);
          //Initiate exam paper on the core table
          $this->postModel->addExamCore($data);
          // Add to params 
          $data['section_alt'] = '';
          $this->postModel->addExamParams($data);
          $data['content'] = '';
          $this->postModel->setCustom($data);
          redirect('posts/custom/' . $data['paperID']);
        }
      } elseif ($param == 'others') {
        //Generate paperID
        $data['paperID'] = substr(md5(time()), 22);
        $data['section_alt'] = val_entry($_POST['section_alt']);
        //Initiate exam paper on the core table
        $this->postModel->addExamCore($data);
        //Initiate exam question on the params table
        $this->postModel->addExamParams($data);
        // Redirect to continue with set question 1
        redirect('posts/add/' . $data['paperID'] . '?section_alt=' . val_entry($_POST['section_alt']));
      }
    } else { // Not a post request
      redirect('users/set_questions');
    }
  } // End set question method

  public function core_paper_edit($paper_id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'id' => $paper_id,
        'duration' => val_entry($_POST['hr'] . ':' . $_POST['min']),
        'publishAS' => val_entry($_POST['publishAS']),
      ];
      if (!empty($data['publishAS'])) {
        $data['published'] = 1;
      } else {
        $data['published'] = 0;
      }
      if ($this->userModel->coreEdit($data)) {
        flash('msg', 'Success!');
        redirect('users/dashboard/');
      } else {
        die('Something went wrong');
      }
    }
  }
}
