<?php
class Users extends Controller
{
  public $userModel;
  public $postModel;
  public $pageModel;
  public function __construct()
  {

    $this->userModel = $this->model('User');
    $this->postModel = $this->model('Post');
    $this->pageModel = $this->model('Page');

    if (!$this->isLoggedIn()) {
      if (isset($_COOKIE['remember_token'])) {
        $user = $this->userModel->findByRememberToken($_COOKIE['remember_token']);
        if ($user) {
          // token valid, restore session
          $this->createUserSession($user);
          return true;
        }
      }
    }
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
    $recents = $this->postModel->getRecents($_SESSION['user_id']);
    $data = [
      'subjects' => $subjects,
      'classes' => $classes,
      'recent' => $recents
    ];

    $this->view('users/dashboard', $data);
  }

  // Dashboard View Ends

  public function archive()
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    //$archives = $this->postModel->getArchive(TERM, SCH_SESSION);
    $data = [
      // 'archives' => $archives
    ];

    $this->view('users/archive', $data);
  }


  public function setting($id)
  {
    if (!$this->isLoggedIn()) {
      redirect('users/login');
    }
    $paper_head = $this->pageModel->getSchool($id);
    if (!$paper_head) {
      $this->pageModel->registerSch([
        'user_id' => $_SESSION['user_id'],
        'name' => null,
        'motto' => null,
        'address' => null
      ]);
    }
    $data = [
      'header' => $paper_head
    ];

    $this->view('users/setting', $data);
  }



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
    $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
    $data = [
      'classes' => $classes
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
    // Check if teacher logged in
    if ($this->isLoggedIn()) {
      redirect('users/dashboard');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      // prepare form input data; we don't yet know the user_id because the
      // teacher hasn't been created/logged in. the value will be assigned
      // after successful registration and login.
      $data = [
        'username' => val_entry($_POST['username']),
        'password' => val_entry($_POST['password']),
        'confirm_password' => val_entry($_POST['confirm_password']),
        'username_err' => '',
        'password_err' => '',
        'confirm_password_err' => '',
        // user_id will be filled in later, avoid accessing undefined session key
        'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        'name' => 'Enter school name',
        'motto' => null,
        'address' => null
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
          // now that the teacher exists, log them in to retrieve their id
          $loggedInUser = $this->userModel->login($data['username'], val_entry($_POST['password']));
          if ($loggedInUser) {
            // assign the real user_id and persist the school record
            $data['user_id'] = $loggedInUser->id;
            $this->pageModel->registerSch($data);

            $this->createUserSession($loggedInUser);
            flash('msg', 'Login Successfull!');
            $redirect = URLROOT . '/users/dashboard';
            echo "<p class='alert alert-success flash-msg fade show' role='alert'>
              <i class='spinner-border spinner-border-sm text-primary'></i>  &nbsp;Registration Successfull!
            </p><meta http-equiv='refresh' content='4; $redirect'>
          ";
          } else {
            // this should not happen but guard anyway
            die('Unable to log in newly registered user');
          }
        } else {
          die('Something went wrong');
        }
      } // Register Form Validation Ends..
    } else {
      // IF NOT A POST REQUEST

      // Init data
      $data = [
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
    // Check if teacher logged in
    if ($this->isLoggedIn()) {
      redirect('users/dashboard');
    }

    // Check if POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
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

        $loggedInUser = $this->userModel->login($data['username'], $data['password']);

        if ($loggedInUser) {
          // User Authenticated!
          $this->createUserSession($loggedInUser);

          // if "remember me" was checked, persist a long‑lived cookie/token
          $remember = 'true';
          if ($remember) {
            // generate a random token, store it in the database and set a cookie for 30 days
            $token = bin2hex(random_bytes(16));
            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
            // update model (method added below)
            $this->userModel->updateRememberToken($loggedInUser->id, $token);
          }
          // Redirect to dashboard
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
    $_SESSION['user_id'] = $user->id;
    $_SESSION['role'] = 'staff';
    $_SESSION['name'] = $user->username;
    $_SESSION['photo'] = '';
    $_SESSION['username'] = $user->name;
  }

  // Logout & Destroy Session
  public function logout()
  {
    // clear any remember-me cookie and token in the database
    if (isset($_COOKIE['remember_token'])) {
      // if user id still available in session clear the DB value too
      if (isset($_SESSION['user_id'])) {
        $this->userModel->updateRememberToken($_SESSION['user_id'], null);
      }
      setcookie('remember_token', '', time() - 3600, '/');
    }

    session_unset();
    session_destroy();
    redirect('users/login');
  }

  // Check Logged In (also support remember‑me cookie if session expired)
  public function isLoggedIn()
  {
    if (isset($_SESSION['user_id'])) {
      return true;
    }
  }
}
