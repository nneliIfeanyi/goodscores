<?php
class Users extends Controller
{
  public $userModel;
  public $postModel;
  public function __construct()
  {
    $this->userModel = $this->model('User');
    $this->postModel = $this->model('Post');
  }

  public function index()
  {
    redirect('welcome');
  }


  public function dashboard()
  {

    // Check if logged in
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }

    $subjects = $this->userModel->getUserSubjectsRowCount($_SESSION['user_id']);
    $classes = $this->userModel->getUserClassesRowCount($_SESSION['user_id']);
    $params = $this->postModel->getParams();
    //$all = $this->postModel->getParamsRowCount();
    $data = [
      'subjects' => $subjects,
      'classes' => $classes,
      'recent' => $params,
     // 'all' => $all,
    ];

    $this->view('users/dashboard', $data);
  }

// Dashboard View Ends

  public function set($param)
  {

    // Check if logged in
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }

    $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
    $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
    $data = [
      'param'=> $param,
      'subjects' => $subjects,
      'classes' => $classes
    ];

    $this->view('users/set', $data);
  }

  // Set Question View Ends

  public function classes()
  {
    $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
    $data = [
      'classes' => $classes
    ];

    $this->view('users/classes', $data);
  }

  public function subjects()
  {
    $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
    $data = [
      'subjects' => $subjects
    ];

    $this->view('users/subjects', $data);
  }

  public function register()
  {
    // Check if sch logged in
    if (!isset($_COOKIE['sch_id'])) {
      redirect('pages/login');
    }
    // Check if teacher logged in
    if ($this->isLoggedIn()) {
      redirect('users/dashboard');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'name' => val_entry($_POST['name']),
        'email' => val_entry($_POST['email']),
        'phone' => val_entry($_POST['phone']),
        'username' => val_entry($_POST['username']),
        'password' => val_entry($_POST['password']),
        'confirm_password' => val_entry($_POST['confirm_password']),
        'sch_id' => $_COOKIE['sch_id'],
        'username_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Register Form Validation
      if ($this->userModel->findTeacherByUsername($data['username'])) {
        $data['username_err'] = 'Username is already taken.';
        $this->view('users/register', $data);
      } elseif (strlen($data['password']) < 6) {
        $data['password_err'] = 'Password must have at least 6 characters.';
        $this->view('users/register', $data);
      } elseif ($data['password'] != $data['confirm_password']) {
        $data['confirm_password_err'] = 'Password do not match.';
        $this->view('users/register', $data);
      } else {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        //Execute
        if ($this->userModel->registerTeacher($data)) {
          // Redirect to login
          flash('msg', 'You are now registered and can log in');
          redirect('users/login');
        } else {
          die('Something went wrong');
        }
      } // Register Form Validation Ends..
    } else {
      // IF NOT A POST REQUEST

      // Init data
      $data = [
        'name' => '',
        'phone' => '',
        'email' => '',
        'username' => '',
        'password' => '',
        'confirm_password' => '',

        'username_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Load View
      $this->view('users/register', $data);
    }
  } // Register Method Ends..

  public function login()
  {
    // Check if sch logged in
    if (!isset($_COOKIE['sch_id'])) {
      redirect('pages/login');
    }
    // Check if teacher logged in
    if ($this->isLoggedIn()) {
      redirect('users/dashboard');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'sch_id' => $_COOKIE['sch_id'],
        'username' => val_entry($_POST['username']),
        'password' => val_entry($_POST['password']),
        'username_err' => '',
        'password_err' => '',
      ];

      // User Login Validation
      if (empty($data['username'])) {
        $data['username_err'] = 'Please enter your username.';
        $this->view('users/login', $data);
      } elseif (empty($data['password'])) {
        $data['password_err'] = 'Please enter your password.';
        $this->view('users/login', $data);
      } elseif (!$this->userModel->findTeacherByUsername($data['username'])) {
        // User Not Found
        $data['username_err'] = 'User Not Found.';
        $this->view('users/login', $data);
      } else {

        $loggedInUser = $this->userModel->login($data['username'], $data['sch_id'], $data['password']);

        if ($loggedInUser) {
          // User Authenticated!
          $this->createUserSession($loggedInUser);
          flash('msg', 'Login Successfull!');
          redirect('users/dashboard');
        } else {
          $data['password_err'] = 'Password incorrect.';
          // Load View
          $this->view('users/login', $data);
        }
      }
    } else {
      // If NOT a POST

      // Init data
      $data = [
        'username' => '',
        'password' => '',
        'username_err' => '',
        'password_err' => '',
      ];

      // Load View
      $this->view('users/login', $data);
    }
  }

  // Create Session With User Info
  public function createUserSession($user)
  {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['name'] = $user->name;
    $_SESSION['username'] = $user->username;
  }

  // Logout & Destroy Session
  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['name']);
    unset($_SESSION['username']);
    session_destroy();
    redirect('users/login');
  }

  // Check Logged In
  public function isLoggedIn()
  {
    if (isset($_SESSION['user_id'])) {
      return true;
    } else {
      return false;
    }
  }
}
