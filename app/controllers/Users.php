<?php
class Users extends Controller
{
  public $userModel;
  public $postModel;
  public function __construct()
  {
    if (!isset($_COOKIE['sch_id'])) {
      redirect('pages/login');
    }
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
      'param' => $param,
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

  public function edit_class($id)
  {
    $class = $this->userModel->getSingleClass($id);
    $data = [
      'class' => $class
    ];

    $this->view('users/edit_class', $data);
  }

  public function subjects()
  {
    $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
    $data = [
      'subjects' => $subjects
    ];

    $this->view('users/subjects', $data);
  }

  public function profile($id)
  {
    $user = $this->userModel->findTeacherById($id);
    $data = [
      'err' => '',
      'user' => $user
    ];

    $this->view('users/profile', $data);
  }

  public function upload($id)
  {
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
            $_SESSION['photo'] = $db_image_file;
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
            $_SESSION['photo'] = $db_image_file;
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
    $_SESSION['photo'] = $user->img;
  }

  // Logout & Destroy Session
  public function logout()
  {

    $id = $_COOKIE['sch_id'];
    $name = $_COOKIE['sch_name'];
    $username = $_COOKIE['sch_username'];
    setcookie('sch_id', $id, time() - 3, '/');
    setcookie('sch_name', $name, time() - 3, '/');
    setcookie('sch_username', $username, time() - 3, '/');
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
