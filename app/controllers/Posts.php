<?php
class Posts extends Controller
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

  // Load All Posts
  public function index()
  {
    $activities = $this->postModel->getAllActivities($_COOKIE['sch_id'], $_SESSION['user_id']);
    $data = [
      'activities' => $activities
    ];

    $this->view('posts/index', $data);
  }






  // Show Single Post
  public function show($paperID)
  {

    if ($_GET['class'] && $_GET['subject'] && $_GET['term'] && $_GET['year']) {
      $obj = $this->postModel->getObjectives($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID);
      $data = [
        'class' => $_GET['class'],
        'subject' => $_GET['subject'],
        'term' => $_GET['term'],
        'year' => $_GET['year'],
        'obj' => $obj,
        'params' => $params,
      ];
      $this->view('posts/show', $data);
    } // get request

    else {
      $obj = $this->postModel->getObjectives($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID);
      $data = [
        'obj' => $obj,
        'params' => $params,
      ];
      $this->view('posts/show', $data);
    }
  }








  // Add Post
  public function add()
  {
    if ($_GET['class'] && $_GET['subject'] && $_GET['term'] && $_GET['year']) {
    } else {
      $data = [
        'num_rows' => $_SESSION['num_rows'],
        'year' => $_SESSION['year'],
        'class' => $_SESSION['class'],
        'term' => $_SESSION['term'],
        'section' => $_SESSION['section'],
        'subject' => $_SESSION['subject'],
        'total_subject_num_rows' => $_SESSION['total_subject_num_rows']
      ];

      $this->view('posts/add', $data);
    }
  }

  // Add Post
  public function add2()
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      // Pull the set num_rows for the particular subject
      $expected_num_rows = $this->postModel->checkSubjectNumRows($_POST['class'], $_SESSION['user_id'], $_COOKIE['sch_id']);
      $data = [
        'paperID' => $_POST['paperID'],
        'class' => $_POST['class'],
        'subject' => $_POST['subject'],
        'term' => $_POST['term'],
        'section' => 'theory_questions',
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'num_rows' => '',
        'question' => $_POST['question'],
        'total_subject_num_rows' => $expected_num_rows->num_rows2
      ];

      if ($this->postModel->setQuestions2($data)) {
        // Update num rows
        $num_rows = $this->postModel->checkTheoryNumRows($_SESSION['paperID'], $_COOKIE['sch_id']);
        flash('msg', 'Theory question ' . $num_rows . ' is set successfully');
        redirect('posts/add2');
      }
    } else {
      $data = [
        'num_rows' => $_SESSION['num_rows'],
        'year' => $_SESSION['year'],
        'class' => $_SESSION['class'],
        'term' => $_SESSION['term'],
        'section' => $_SESSION['section'],
        'subject' => $_SESSION['subject'],
      ];

      $this->view('posts/add2', $data);
    }
  }

  // Edit Post
  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'id' => $id,
        'question' => trim($_POST['question']),
        'opt1' => trim($_POST['opt1']),
        'opt4' => trim($_POST['opt4']),
        'opt2' => trim($_POST['opt2']),
        'opt3' => trim($_POST['opt3'])
      ];

      if ($this->postModel->updateObj($data)) {
        // Redirect to login
        flash('msg', 'Question is Updated');
        redirect('posts/show/' . $_POST['paperID']);
      } else {
        die('Something went wrong');
      }
    } else {
      // Get post from model
      $post = $this->postModel->getObjById($id);
      $params = $this->postModel->getParamsByPaperID($post->paperID);
      $data = [
        'post' => $post,
        'params' => $params
      ];

      $this->view('posts/edit', $data);
    }
  }

  // Delete single obj question
  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deleteObj($id)) {
        // Redirect to login
        flash('msg', 'Question Removed', 'alert alert-danger');
        redirect('posts/show/' . $_POST['paperID']);
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }
  // Delete all  obj question
  public function delete_obj_all($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deleteObjall($id)) {
        // Delete Params aswell
        $this->postModel->deleteParamsData($id);
        flash('msg', 'Questions Removed', 'alert alert-danger');
        redirect('users/dashboard');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }
}
