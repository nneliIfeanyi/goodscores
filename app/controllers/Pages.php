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
    $data = [];

    // Load about view
    $this->view('users/login', $data);
  }

  // Load Homepage
  public function welcome()
  {
    $data = [];

    // Load about view
    $this->view('pages/welcome', $data);
  }

  public function profile($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $data = [
        'name' => val_entry($_POST['name']),
        'motto' => val_entry($_POST['motto']),
        'address' => val_entry($_POST['address']),
        'id' => $id,
      ];
      if ($this->pageModel->editProfile($data)) {
        // Redirect to classes
        flash('msg', 'Changes saved successfully');
        redirect('users/setting/' . $_SESSION['user_id']);
      } else {
        die('Something went wrong');
      }
    } else { // Not a post request
      die('Something went wrong');
    }
  }
}
