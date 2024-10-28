<?php
class Users extends Controller
{
  public $userModel;
  public $postModel;
  public $pageModel;
  public function __construct()
  {
    if (!isset($_COOKIE['sch_id']) && !isset($_COOKIE['user_id'])) {
      redirect('pages/login');
    } else {
      $_SESSION['user_id'] = $_COOKIE['user_id'];
      $_SESSION['name'] = $_COOKIE['name'];
      $_SESSION['username'] = $_COOKIE['user_name'];
      $_SESSION['photo'] = $_COOKIE['photo'];
      $_SESSION['role'] = $_COOKIE['role'];
    }
    $this->userModel = $this->model('User');
    $this->postModel = $this->model('Post');
    $this->pageModel = $this->model('Page');
  }

  public function index()
  {
    redirect('users/dashboard');
  }


  public function dashboard()
  {

    // Check if logged in
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }

    $subjects = $this->userModel->getUserSubjectsRowCount($_SESSION['user_id']);
    $classes = $this->userModel->getUserClassesRowCount($_SESSION['user_id']);
    $archive = $this->postModel->getArchive();
    $params = $this->postModel->getRecentParams();

    //$all = $this->postModel->getParamsRowCount();
    $data = [
      'subjects' => $subjects,
      'classes' => $classes,
      'recent' => $params,
      'archive' => $archive,
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
      'param' => $param,
      'subjects' => $subjects,
      'classes' => $classes
    ];

    $this->view('users/set', $data);
  }

  // Set Question View Ends

  public function classes()
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $sch = $this->pageModel->getSchool($_COOKIE['sch_id']);
    $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
    $classesAll = $this->pageModel->getClasses($_COOKIE['sch_id']);
    $data = [
      'classes' => $classes,
      'sch' => $sch,
      'classes2' => $classesAll
    ];

    $this->view('users/classes', $data);
  }

  public function review_params()
  {
    if ($_GET['paperID'] && $_GET['section']) {
      $params = $this->postModel->getParamsByPaperID($_GET['paperID'], $_GET['section']);
      $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
      $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
      $core = $this->postModel->getParamsFromCore($_GET['paperID']);
      $data = [
        'classes' => $classes,
        'subjects' => $subjects,
        'params' => $params,
        'section' => $params->section,
        'core' => $core
      ];

      $this->view('users/review_params', $data);
    } else {
      redirect('users/dashboard');
    }
  }

  public function edit_class($id)
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $class = $this->userModel->getSingleClass($id);
    $data = [
      'class' => $class
    ];

    $this->view('users/edit_class', $data);
  }

  public function subjects()
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
    $data = [
      'subjects' => $subjects
    ];

    $this->view('users/subjects', $data);
  }


  // Edit Subject included
  public function edit_subject($id)
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $subject = $this->userModel->getSingleSubject($id);
    $data = [
      'subject' => $subject
    ];

    $this->view('users/edit_subject', $data);
  }

  public function profile($id)
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $user = $this->userModel->findTeacherById($id);
    $data = [
      'err' => '',
      'user' => $user
    ];

    $this->view('users/profile', $data);
  }

  public function upload($id)
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (is_array($_FILES)) {
        $file = $_FILES['photo']['tmp_name'];
        $source_properties = getimagesize($file);
        $image_type = $source_properties[2];
        if ($image_type == IMAGETYPE_JPEG) {
          $image_resource_id = imagecreatefromjpeg($file);
          $target_layer = fn_resize($image_resource_id, $source_properties[0], $source_properties[1]);
          imagejpeg($target_layer, "assets/img/" . $_FILES['photo']['name']);
          $db_image_file =  "assets/img/" . $_FILES['photo']['name'];
          if ($this->userModel->editPhoto($id, $db_image_file)) {
            setcookie('photo', $db_image_file, time() + (86400 * 365), '/');
            //$_SESSION['photo'] = $db_image_file;
            flash('msg', 'Profile photo updated..');
            echo "<script>
                history.go(-2)
          </script>";
          } else {
            die('Something went wrong, try again later.');
          }
        } elseif ($image_type == IMAGETYPE_PNG) {
          $image_resource_id = imagecreatefrompng($file);
          $target_layer = fn_resize($image_resource_id, $source_properties[0], $source_properties[1]);
          imagepng($target_layer, "assets/img/" . $_FILES['photo']['name']);
          $db_image_file =  "assets/img/" . $_FILES['photo']['name'];
          if ($this->userModel->editPhoto($id, $db_image_file)) {
            setcookie('photo', $db_image_file, time() + (86400 * 365), '/');
            //$_SESSION['photo'] = $db_image_file;
            flash('msg', 'Profile photo updated..');
            echo "<script>
                history.go(-2)
          </script>";
          } else {
            die('Something went wrong, try again later.');
          }
        }
      } // End if is_array
    } else {
      $user = $this->userModel->findTeacherById($id);
      $data = [
        'user' => $user
      ];

      $this->view('users/upload', $data);
    }
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
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Username is already taken.
          </p>
        ";
        // $data['username_err'] = 'Username is already taken.';
        // $this->view('users/register', $data);
      } elseif (strlen($data['password']) < 6) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Password must have at least 6 characters.
          </p>
        ";
        // $data['password_err'] = 'Password must have at least 6 characters.';
        // $this->view('users/register', $data);
      } elseif ($data['password'] != $data['confirm_password']) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Password does not match.
          </p>
        ";
        // $data['confirm_password_err'] = 'Password do not match.';
        // $this->view('users/register', $data);
      } else {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        //Execute
        if ($this->userModel->registerTeacher($data)) {
          // Redirect to login
          // Redirect to verify email page
          $redirect = URLROOT . '/users/login';
          echo "<p class='alert alert-success flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Registration Successfull!
          </p><meta http-equiv='refresh' content='3; $redirect'>
        ";
          flash('msg', 'You are now registered and can log in');
          //redirect('users/login');
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

      $data = [
        'sch_id' => $_COOKIE['sch_id'],
        'username' => val_entry($_POST['username']),
        'password' => val_entry($_POST['password']),
        'username_err' => '',
        'password_err' => '',
      ];

      // User Login Validation
      if (empty($data['username'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Please enter your username!
          </p>
        ";
        // $data['username_err'] = 'Please enter your username.';
        // $this->view('users/login', $data);
      } elseif (empty($data['password'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Please enter your password!
          </p>
        ";
        // $data['password_err'] = 'Please enter your password.';
        // $this->view('users/login', $data);
      } elseif (!$this->userModel->findTeacher($data['username'])) {
        // User Not Found
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;User not found!
          </p>
        ";
        // $data['username_err'] = 'User Not Found.';
        // $this->view('users/login', $data);
      } else {

        $loggedInUser = $this->userModel->login($data['username'], $data['sch_id'], $data['password']);

        if ($loggedInUser) {
          // User Authenticated!
          $this->createUserSession($loggedInUser);
          // Redirect to verify email page
          $redirect = URLROOT . '/users/dashboard';
          echo "<p class='alert alert-success flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Login Successfull!
          </p><meta http-equiv='refresh' content='3; $redirect'>
        ";
          flash('msg', 'Login Successfull!');
          // redirect('users/dashboard');
        } else {
          echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Password incorrect!
          </p>
        ";
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
    setcookie('user_id', $user->id, time() + (86400 * 365), '/');
    setcookie('user_name', $user->name, time() + (86400 * 365), '/');
    setcookie('name', $user->username, time() + (86400 * 365), '/');
    setcookie('photo', $user->img, time() + (86400 * 365), '/');
    setcookie('role', $user->role, time() + (86400 * 365), '/');
  }

  // Logout & Destroy Session
  public function logout()
  {
    $id = $_COOKIE['user_id'];
    $user_name = $_COOKIE['user_name'];
    $name = $_COOKIE['name'];
    $role = $_COOKIE['role'];
    $photo = $_COOKIE['photo'];

    setcookie('user_id', $id, time() - 3, '/');
    setcookie('user_name', $user_name, time() - 3, '/');
    setcookie('name', $name, time() - 3, '/');
    setcookie('role', $role, time() - 3, '/');
    setcookie('photo', $photo, time() - 3, '/');
    session_unset();
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
