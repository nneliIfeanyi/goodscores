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
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'classname' => val_entry($_POST['classname']),
        'obj_num_rows' => val_entry($_POST['obj_num_rows']),
        'theory_num_rows' => val_entry($_POST['theory_num_rows']),
        'user_id' => $_SESSION['user_id'],
        'sch_id' => $_COOKIE['sch_id']
      ];

      $data['classname'] = strtolower($data['classname']);
      $data['classname'] = preg_replace('/\s+/', '-', $data['classname']);

      if ($this->postModel->addClass($data)) {
        // Redirect to login
        flash('msg', 'Class is added successfully');
        redirect('users/classes');
      } else {
        die('Something went wrong');
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

      if ($this->postModel->addSubject($data)) {
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
      if ($this->postModel->deleteSubject($id)) {
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
      if ($this->postModel->deleteClass($id)) {
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
      // Pull the set num_rows for the particular subject
      //$expected_num_rows = $this->postModel->checkSubjectNumRows($_POST['class'], $_SESSION['user_id'], $_COOKIE['sch_id']);

      $data = [
        'paperID' => '',
        'class' => $_POST['class'],
        'subject' => $_POST['subject'],
        'term' => $_POST['term'],
        'section' => $param,
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'year' => $_POST['year'],
        //'total_subject_num_rows' => $expected_num_rows->num_rows
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
          //Generate paperID
          $data['paperID'] = $exam_exits->paperID;
          //Initiate exam question on the params table
          $this->postModel->addExamParams($data);
          // Redirect to continue with set question 1
          redirect('posts/add2/' . $data['paperID']);
        } elseif ($exam_exits && $exam_exits2) { // found both obj and theory in param
          redirect('posts/add2/' . $exam_exits->paperID);
        }
      } elseif ($param == 'objectives_questions') {
        // Coming from users/set_question
        $exam_exits = $this->postModel->checkExamParams($data);
        $exam_exits2 = $this->postModel->checkExamParams2($data);
        if ($exam_exits && $exam_exits2) { // Objectives has been set
          // Exam has been set
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























  // Edit Post
  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'id' => $id,
        'title' => trim($_POST['title']),
        'body' => trim($_POST['body']),
        'user_id' => $_SESSION['user_id'],
        'title_err' => '',
        'body_err' => ''
      ];

      // Validate email
      if (empty($data['title'])) {
        $data['title_err'] = 'Please enter name';
        // Validate name
        if (empty($data['body'])) {
          $data['body_err'] = 'Please enter the post body';
        }
      }

      // Make sure there are no errors
      if (empty($data['title_err']) && empty($data['body_err'])) {
        // Validation passed
        //Execute
        if ($this->postModel->updatePost($data)) {
          // Redirect to login
          flash('post_message', 'Post Updated');
          redirect('posts');
        } else {
          die('Something went wrong');
        }
      } else {
        // Load view with errors
        $this->view('posts/edit', $data);
      }
    } else {
      // Get post from model
      $post = $this->postModel->getPostById($id);

      // Check for owner
      if ($post->user_id != $_SESSION['user_id']) {
        redirect('posts');
      }

      $data = [
        'id' => $id,
        'title' => $post->title,
        'body' => $post->body,
      ];

      $this->view('posts/edit', $data);
    }
  }

  // Delete Post
  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deletePost($id)) {
        // Redirect to login
        flash('post_message', 'Post Removed');
        redirect('posts');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }
}
