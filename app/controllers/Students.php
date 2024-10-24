<?php
class Students extends Controller
{
    public $userModel;
    public $pageModel;
    public $postModel;
    public $studentModel;
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->pageModel = $this->model('Page');
        $this->postModel = $this->model('Post');
        $this->studentModel = $this->model('Student');
    }

    // Load Homepage
    public function index()
    {
        if (!$this->isLoggedIn()) {
            redirect('users/login');
        }
        $count = $this->studentModel->getStudentsCount();
        $classes = $this->pageModel->getClasses($_COOKIE['sch_id']);
        $data = [
            'count' => $count,
            'classes' => $classes
        ];

        // Load view
        $this->view('students/index', $data);
    }

    public function add()
    {
        if (!$this->isLoggedIn()) {
            redirect('users/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $prefix = 'prefix_';
            $data = [
                'sch_id' => $_COOKIE['sch_id'],
                'firstname' => val_entry($_POST['firstname']),
                'middlename' => val_entry($_POST['middlename']),
                'surname' => val_entry($_POST['surname']),
                'fullname' => val_entry($_POST['surname'] . ' ' . $_POST['firstname'] . ' ' . $_POST['middlename']),
                'gender' => val_entry($_POST['gender']),
                'class' => val_entry($_POST['class']),
                'regNo' => $prefix . $_POST['regNo']
            ];
            if ($this->studentModel->addStudent($data)) {
                flash('msg', 'Success');
                redirect('students/add');
            } else {
                die('Something went wrong');
            }
        } else {
            $reg_no = substr(md5(time()), 27);
            $classes = $this->pageModel->getClasses($_COOKIE['sch_id']);
            $data = [
                'classes' => $classes,
                'regNo' => $reg_no
            ];

            // Load view
            $this->view('students/add', $data);
        }
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'firstname' => val_entry($_POST['firstname']),
                'middlename' => val_entry($_POST['middlename']),
                'surname' => val_entry($_POST['surname']),
                'fullname' => val_entry($_POST['surname'] . ' ' . $_POST['firstname'] . ' ' . $_POST['middlename']),
                'dob' => val_entry($_POST['dob']),
                'gender' => val_entry($_POST['gender']),
                'email' => val_entry($_POST['email']),
                'phone' => val_entry($_POST['phone']),
                'class' => val_entry($_POST['class']),
                'address' => val_entry($_POST['address']),
                'state' => val_entry($_POST['state']),
                'country' => val_entry($_POST['country']),
                'religion' => val_entry($_POST['religion']),
            ];
            if ($this->studentModel->editStudent($data)) {
                flash('msg', 'Success');
                redirect('students/profile/' . $id);
            } else {
                die('Something went wrong');
            }
        } else {
            if (!$this->isLoggedIn()) {
                redirect('students/login');
            }
            $classes = $this->pageModel->getClasses($_COOKIE['sch_id']);
            $details = $this->studentModel->findStudentById($id);
            $data = [
                'student' => $details,
                'classes' => $classes,
            ];

            // Load view
            $this->view('students/edit', $data);
        }
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'sch_id' => $_COOKIE['sch_id'],
                'passkey' => val_entry($_POST['passkey']),
            ];

            // User Login Validation
            if (empty($data['passkey'])) {
                echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;Please enter your passkey!
          </p>
        ";
            } else {

                $loggedInUser = $this->studentModel->findStudent($data['passkey']);
                if ($loggedInUser) {
                    // User Authenticated!
                    $this->createUserSession($loggedInUser);
                    // Redirect to verify email page
                    $redirect = URLROOT . '/students/dashboard';
                    echo "<meta http-equiv='refresh' content='0; $redirect'>
        ";
                    flash('msg', 'Login Successfull!');
                } else {
                    // User Not Found
                    echo "<p class='alert alert-danger flash-msg fade show' role='alert'>
            <i class='bi bi-check-circle'></i>  &nbsp;User not found | incorrect passkey!
          </p>
        ";
                }
            }
        } else {
            if ($this->isLoggedIn()) {
                redirect('students/dashboard');
            }
            $data = [];

            // Load view
            $this->view('students/login', $data);
        }
    }

    public function dashboard()
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $details = $this->studentModel->findStudentById($_SESSION['student_id']);
        $params = $this->studentModel->getCbtParams($details->class);
        $param = $this->studentModel->getCbtParam($details->class);
        $data = [
            'recent' => $params,
            'param' => $param
        ];

        // Load view
        $this->view('students/dashboard', $data);
    }
    public function cbt($paper_id)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $details = $this->studentModel->findStudentById($_SESSION['student_id']);
        $core = $this->studentModel->getCbtCore($details->class);
        $param = $this->studentModel->getCbtParam($details->class);
        $cbt = $this->studentModel->getCbtQuestions($paper_id);
        $data = [
            'core' => $core,
            'param' => $param,
            'cbt' => $cbt
        ];

        // Load view
        if (!$this->studentModel->checkIfExamTaken($paper_id)) {
            $this->view('students/cbt', $data);
        } else {
            flash('msg', 'You already sat for this exam');
            $details = $this->studentModel->checkIfExamTaken($paper_id);
            if ($details->score >= 50) {
                redirect('students/success/' . $details->score);
            } else {
                redirect('students/failed/' . $details->score);
            }
        }
    }
    public function submit_cbt($paper_id)
    {
        $cbtRowCount = $this->studentModel->getCbtRowCount($paper_id);
        $total = 0;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            for ($i = 1; $i <= $cbtRowCount; $i++) {

                if ($_POST['ans' . $i] === $_POST['default' . $i]) {
                    $score = 1;
                } else {
                    $score = 0;
                }
                // echo $_POST['ans' . $i] . '|' . $_POST['default' . $i] . ' = ' . $score . '<br>';
                $total += $score;
            }
            $result =  round($total / $cbtRowCount * 100, 1);
            $data = [
                'sch_id' => $_COOKIE['sch_id'],
                'student_id' => $_SESSION['student_id'],
                'subject' => $_POST['subject'],
                'paperID' => $_POST['paperID'],
                'score' => $result
            ];
            if ($this->studentModel->insertScore($data)) {
                if ($result >= 50) {
                    redirect('students/success/' . $result);
                } else {
                    redirect('students/failed/' . $result);
                }
            } else {
                die('Something went wrong!');
            }
        } else {
            die('Something went wrong!');
        }
    }
    public function success($score)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $details = $this->studentModel->findStudentById($_SESSION['student_id']);
        $data = [
            'user' => $details,
            'sccore' => $score
        ];

        // Load view
        $this->view('students/success', $data);
    }
    public function failed($score)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $details = $this->studentModel->findStudentById($_SESSION['student_id']);
        $data = [
            'user' => $details,
            'sccore' => $score
        ];

        // Load view
        $this->view('students/failed', $data);
    }
    public function profile($id)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $details = $this->studentModel->findStudentById($id);
        $data = [
            'user' => $details,
        ];

        // Load view
        $this->view('students/profile', $data);
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
                    imagejpeg($target_layer, "assets/photos/" . $_FILES['photo']['name']);
                    $db_image_file =  "assets/photos/" . $_FILES['photo']['name'];
                    if ($this->studentModel->editPhoto($id, $db_image_file)) {
                        //setcookie('photo', $db_image_file, time() + (86400 * 365), '/');
                        $_SESSION['profile_photo'] = $db_image_file;
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
                    imagepng($target_layer, "assets/photos/" . $_FILES['photo']['name']);
                    $db_image_file =  "assets/photos/" . $_FILES['photo']['name'];
                    if ($this->studentModel->editPhoto($id, $db_image_file)) {
                        // setcookie('photo', $db_image_file, time() + (86400 * 365), '/');
                        $_SESSION['profile_photo'] = $db_image_file;
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
            if (!$this->isLoggedIn()) {
                redirect('students/login');
            }
            $user = $this->studentModel->findStudentById($id);
            $data = [
                'user' => $user
            ];

            $this->view('students/upload', $data);
        }
    }

    // Remove | delete student profile image
    public function remove_img($id)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $user = $this->studentModel->findStudentById($id);
        if ($this->studentModel->removePhoto($id)) {
            unlink($user->img);
            // setcookie('photo', $user->img, time() - (3), '/');
            unset($_SESSION['profile_photo']);
            flash('msg', 'Profile photo removed..', 'alert alert-danger bg-danger text-light border-0 alert-dismissible');
            echo "<script>
                history.go(-1)
          </script>";
        } else {
            die('Something went wrong');
        }
    } // End remove | delete student profile image

    // Create Session With User Info
    public function createUserSession($user)
    {
        $_SESSION['student_id'] = $user->id;
        $_SESSION['fullname'] = $user->fullname;
        $_SESSION['profile_photo'] = $user->img;
    }

    // Logout & Destroy Session
    public function logout()
    {
        unset($_SESSION['student_id']);
        unset($_SESSION['fullname']);
        unset($_SESSION['profile_photo']);
        session_destroy();
        redirect('students/login');
    }

    // Check Student Logged In
    public function isLoggedIn()
    {
        if (isset($_SESSION['student_id'])) {
            return true;
        } else {
            return false;
        }
    }
}
