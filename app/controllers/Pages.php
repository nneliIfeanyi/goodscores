<?php
class Pages extends Controller
{
  public $userModel;
  public $pageModel;
  public function __construct()
  {
    $this->userModel = $this->model('User');
    $this->pageModel = $this->model('Page');
  }

  // Load Homepage
  public function index()
  {
    if (isset($_COOKIE['sch_id'])) {
      redirect('users/login');
    }
    $data = [];

    // Load about view
    $this->view('pages/index', $data);
  }

  // Load Homepage
  public function welcome()
  {
    if (isset($_COOKIE['sch_id'])) {
      redirect('users/login');
    }
    $data = [];

    // Load about view
    $this->view('pages/welcome', $data);
  }


  public function login()
  {
    if (isset($_COOKIE['sch_id'])) {
      redirect('users/login');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'username' => val_entry($_POST['username']),
        'username_err' => ''
      ];
      if (empty($data['username'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Kindly enter your school username
          </p>
        ";
        //$data['username_err'] = 'Kindly enter your school username';
        //$this->view('pages/login', $data);
      } elseif (!$this->pageModel->findSch($data['username'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;User not found | Username or email incorrect!
          </p>
        ";
        // $data['username_err'] = 'User not found | Username or email incorrect!';
        // $this->view('pages/login', $data);
      } elseif ($sch = $this->pageModel->findSch($data['username'])) {
        setcookie('sch_id', $sch->id, time() + (86400 * 365), '/');
        setcookie('sch_name', $sch->name, time() + (86400 * 365), '/');
        setcookie('sch_username', $sch->username, time() + (86400 * 365), '/');
        // Redirect to Teachers section
        $redirect = URLROOT . '/users/login/';
        echo "<p class='alert alert-success flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;School authentication approved! Redirecting to teachers section
          </p><meta http-equiv='refresh' content='5; $redirect'>
        ";
        flash('msg', 'School authentication approved');
        // redirect('users/login');
      }
    } // End Post Request
    else {
      //Set Data
      $schools = $this->pageModel->getSchools();
      $data = [
        'schools' => $schools,
        'username' => '',
        'username_err' => ''
      ];

      // Load about view
      $this->view('pages/login', $data);
    }
  }




  public function register()
  {
    // Check if already logged in
    if (isset($_COOKIE['sch_id'])) {
      redirect('users/login');
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

      $data = [
        'name' => val_entry($_POST['name']),
        'email' => val_entry($_POST['email']),
        'phone' => val_entry($_POST['phone']),
        'username' => val_entry($_POST['username']),
        'username_err' => '',
        'email_err' => ''
      ];
      // Validation
      if ($this->pageModel->findSchByEmail($data['email'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Email is already taken! kindly login
          </p>
        ";
      } elseif ($this->pageModel->findSchByUsername($data['username'])) {
        echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i> &nbsp;Username is already taken! kindly login
          </p>
        ";
      } else {
        $register = $this->pageModel->registerSch($data);
        if ($register) {
          sendMail($data['email']);
          flash('msg', 'Registration successful, you can now login');
          // Redirect to verify email page
          $redirect = URLROOT . '/pages/verify_email/' . $data['email'];
          echo "<meta http-equiv='refresh' content='0; $redirect'>
        ";
        } else {
          die('Something went wrong!');
        }
      }
    } // End Post Method
    else {
      //Set Data
      $data = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'username' => '',
      ];

      // Load about view
      $this->view('pages/register', $data);
    }
  }

  public function verify_email($email)
  {
    if ($_GET['email']) {
      $email = $_GET['email'];
      flash('msg', 'Email verify successfull! Just click the login button.');
      redirect('pages/login?email=' . $email);
    } else {
      //Set Data
      $data = [
        'email' => $email
      ];

      // Load about view
      $this->view('pages/verify_email', $data);
    }
  }


  public function logout()
  {
    $id = $_COOKIE['sch_id'];
    $name = $_COOKIE['sch_name'];
    $username = $_COOKIE['sch_username'];
    $user_id = $_COOKIE['user_id'];
    $user_name = $_COOKIE['user_name'];
    $name = $_COOKIE['name'];
    $role = $_COOKIE['role'];
    $photo = $_COOKIE['photo'];

    setcookie('sch_id', $id, time() - 3, '/');
    setcookie('sch_name', $name, time() - 3, '/');
    setcookie('sch_username', $username, time() - 3, '/');
    setcookie('user_id', $user_id, time() - 3, '/');
    setcookie('user_name', $user_name, time() - 3, '/');
    setcookie('name', $name, time() - 3, '/');
    setcookie('role', $role, time() - 3, '/');
    setcookie('photo', $photo, time() - 3, '/');
    session_unset();
    session_destroy();
    redirect('pages/login');
  }

  public function about()
  {
    //Set Data
    $data = [
      "version" => "<div style='text-align:left;background-color:#f6f9ff;padding:0 10px 20px 10px;'>
            <h1 style='color:#0d6efd;padding: 0;border-radius:6px;'>A warm welcome to GoodScores</h1>
        <p><b>Hello there,</b><br>
                My name is Victor, and I'm the CEO of GoodScores.
                Its Fantastic to have you onboard!</p>
            <p style='font-size:16px;'>
                Please click the button below to register a user
                either your school administrator or teachers<br>
                <b>Note that: </b><span>You can register as many users as possible</span><br><br>
                <a style='text-decoration:none;padding: 7px 12px;background-color:#0d6efd;color:antiquewhite;border-radius:10px;' href='https://goodscores.stanvic.com.ng/users/register'>Verify your email</a
            </p><br><br><br>
            <p>It's fantastic to have you!<br>
            Nneli Ifeanyi Victor<br>
            CEO <b>GoodScores</b></p><br><br><br>
            </div>"
    ];

    // Load about view
    $this->view('pages/about', $data);
  }


  public function profile($id)
  {
    // If post 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST

      $data = [
        'name' => val_entry($_POST['name']),
        'username' => val_entry($_POST['username']),
        'phone' => val_entry($_POST['phone']),
        'motto' => val_entry($_POST['motto']),
        'id' => $id,
        'address' => val_entry($_POST['address'])
      ];
      if ($this->pageModel->editProfile($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        redirect('pages/profile/' . $id);
      } else {
        die('Something went wrong');
      }
    } else { // Not post request
      $school = $this->pageModel->getSchool($id);
      $user = $this->userModel->findTeacherById($_SESSION['user_id']);
      //Set Data
      $data = [
        'user' => $school,
        'role' => $user->role
      ];

      // Load about view
      $this->view('pages/profile', $data);
    }
  }
}
