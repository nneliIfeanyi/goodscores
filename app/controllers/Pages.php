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
    redirect('pages/login');
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
        $data['username_err'] = 'Kindly enter your school username';
        $this->view('pages/login', $data);
      } elseif (!$this->userModel->findSchByUsername($data['username'])) {
        $data['username_err'] = 'User not found | Username incorrect!';
        $this->view('pages/login', $data);
      } elseif ($sch = $this->userModel->findSchByUsername($data['username'])) {
        setcookie('sch_id', $sch->id, time() + (86400), '/');
        setcookie('sch_name', $sch->name, time() + (86400), '/');
        setcookie('sch_username', $sch->username, time() + (86400), '/');
        flash('msg', 'School authentication approved');
        redirect('users/login');
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
      if ($this->userModel->findSchByEmail($data['email'])) {
        $data['email_err'] = 'Email already in use!';
        $this->view('pages/register', $data);
      } elseif ($this->userModel->findSchByUsername($data['username'])) {
        $data['username_err'] = 'Username' . $data['username'] . 'is taken!';
        $this->view('pages/register', $data);
      } else {
        $register = $this->userModel->registerSch($data);
        if ($register) {
          flash('msg', 'Registration is successful');
          redirect('pages/verify_email/' . $data['email']);
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
    //Set Data
    $data = [
      'email' => $email
    ];

    // Load about view
    $this->view('pages/verify_email', $data);
  }


  public function logout()
  {
    $id = $_COOKIE['sch_id'];
    $name = $_COOKIE['sch_name'];
    $username = $_COOKIE['sch_username'];

    setcookie('sch_id', $id, time() - 3, '/');
    setcookie('sch_name', $name, time() - 3, '/');
    setcookie('sch_username', $username, time() - 3, '/');
    session_unset();
    session_destroy();
    redirect('pages/login');
  }

  public function about()
  {
    //Set Data
    $data = [
      'version' => '1.0.0'
    ];

    // Load about view
    $this->view('pages/about', $data);
  }


  public function profile($id)
  {
    // If post 
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

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
      //Set Data
      $data = [
        'user' => $school,
      ];

      // Load about view
      $this->view('pages/profile', $data);
    }
  }
}
