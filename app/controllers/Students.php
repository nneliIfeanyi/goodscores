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
        if (!$this->isLoggedIn2()) {
            redirect('users/login');
        }
        $count = $this->studentModel->getStudentsCount();
        $classes = $this->pageModel->getClasses($_COOKIE['sch_id']);
        $class = $this->userModel->getUserClasses($_SESSION['user_id']);
        $data = [
            'count' => $count,
            'classes' => $classes,
            'class' => $class
        ];

        // Load view
        $this->view('students/index', $data);
    }

    public function add()
    {
        if (!$this->isLoggedIn2()) {
            redirect('users/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = [
                'sch_id' => $_COOKIE['sch_id'],
                'firstname' => val_entry($_POST['firstname']),
                'middlename' => val_entry($_POST['middlename']),
                'surname' => val_entry($_POST['surname']),
                'fullname' => val_entry($_POST['surname'] . ' ' . $_POST['firstname'] . ' ' . $_POST['middlename']),
                'gender' => val_entry($_POST['gender']),
                'class' => val_entry($_POST['class']),
                'regNo' => $_POST['regNo']
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
            if (!$this->isLoggedIn() && !$this->isLoggedIn2()) {
                redirect('users/login');
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
            if (!isset($_COOKIE['sch_id'])) {
                redirect('pages/login');
            }
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
        $params = $this->studentModel->getCbtParams($details->class); //From Core where published is true
        $data = [
            'recent' => $params,
        ];

        // Load view
        $this->view('students/dashboard', $data);
    }
    public function cbt($paper_id)
    {
        if (!$this->isLoggedIn()) {
            redirect('students/login');
        }
        $duration = $this->studentModel->pullTime($paper_id);
        $core = $this->studentModel->getCbtCore($paper_id);
        $param = $this->studentModel->getCbtParam($paper_id);
        $cbt = $this->studentModel->getCbtQuestions($paper_id);
        $data = [
            'sch_id' => $_COOKIE['sch_id'],
            'student_id' => $_SESSION['student_id'],
            'paperID' => $paper_id,
            'core' => $core,
            'param' => $param,
            'cbt' => $cbt
        ];

        // Load view
        // Pull Time Records
        $loggedEndTime = $duration->endTime;
        $data['duration'] = $loggedEndTime;
        $now = date('H:i:s');
        if ($now < $loggedEndTime) {
            $this->view('students/cbt', $data);
        } else {
            $this->view('students/timeUp', $data);
        }
        //
    }
    public function timeCalc($paper_id)
    {
        $core = $this->studentModel->getCbtCore($paper_id);
        $data = [
            'sch_id' => $_COOKIE['sch_id'],
            'student_id' => $_SESSION['student_id'],
            'paperID' => $paper_id
        ];
        // Exam Time Manipulate
        $duration = explode(':', $core->duration);
        $hr = $duration[0];
        $min = $duration[1];
        $time = strtotime('+' . $hr . ' hours ' . $min . ' minutes');
        $data['endTime'] = date('H:i:s', $time);
        $data['startTime'] = date('H:i:s');
        // Insert Time Records
        $this->studentModel->initTime($data);
        redirect('students/cbt/' . $paper_id);
    }
    public function submit_cbt($paper_id)
    {
        $cbtRowCount = $this->studentModel->getCbtRowCount($paper_id);
        $core = $this->studentModel->getCbtCore($paper_id);
        $total = 0;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!$this->studentModel->checkIfResponseExist($paper_id)) {
                for ($i = 1; $i <= $cbtRowCount; $i++) {
                    $ans = strtolower($_POST['ans' . $i]);
                    $data = [
                        'sch_id' => $_COOKIE['sch_id'],
                        'student_id' => $_SESSION['student_id'],
                        'paperID' => $_POST['paperID'],
                        'response' => trim($ans),
                        'question' => $_POST['question' . $i],
                        'opt1' => $_POST[$i . 'optA'],
                        'opt2' => $_POST[$i . 'optB'],
                        'opt3' => $_POST[$i . 'optC'],
                        'opt4' => $_POST[$i . 'optD'],
                        'img' => $_POST['img' . $i],
                        'isSubjective' => $_POST['isSubjective' . $i],
                        'subInstruction' => $_POST['subInstruction' . $i],
                        'ans' => $_POST['default' . $i],
                    ];
                    // Insert || Set Response
                    $this->studentModel->setResponse($data);

                    if ($_POST['ans' . $i] === $_POST['default' . $i]) {
                        $score = 1;
                    } else {
                        $score = 0;
                    }
                    $total += $score;
                } //// End For Loop
                if ($total <= 0) {
                    $result = 0;
                } else {
                    $result =  round($total / $cbtRowCount * 100, 1);
                }
                $cbt = $this->studentModel->getResponse($paper_id);
                $data = [
                    'sch_id' => $_COOKIE['sch_id'],
                    'student_id' => $_SESSION['student_id'],
                    'subject' => $_POST['subject'],
                    'class' => $_POST['class'],
                    'paperID' => $_POST['paperID'],
                    'percent' => $result,
                    'cbtTag' => $_POST['cbtTag'],
                    'term' => $core->term,
                    'cbt' => $cbt,
                    'response' => $_POST['ans' . $i],
                    'rowcount' => $cbtRowCount
                ];
                $row = $this->studentModel->checkRowExist($data['subject'], $data['class'], $data['term']);
                if ($row) {
                    //update row
                    if ($data['cbtTag'] == 'CA1') {
                        $score = ($data['percent'] / 100 * 20);
                        $this->studentModel->updateScoreRow1($row->id, $score);
                    } elseif ($data['cbtTag'] == 'CA2') {
                        $score = ($data['percent'] / 100 * 20);
                        $this->studentModel->updateScoreRow2($row->id, $score);
                    } elseif ($data['cbtTag'] == 'exam') {
                        $score = ($data['percent'] / 100 * 20);
                        $this->studentModel->updateScoreRow3($row->id, $score);
                    }
                    $examTaken = $this->studentModel->checkRowExist($core->subject, $core->class, $core->term);
                    $data['score'] = $examTaken;
                    $this->view('students/success', $data);
                } else {
                    // Insert Score To DB
                    $this->studentModel->insertScore($data);
                    $examTaken = $this->studentModel->checkRowExist($core->subject, $core->class, $core->term);
                    $data['score'] = $examTaken;
                    $this->view('students/success', $data);
                }
            }
            redirect('students/dashboard');
        } else { // Post Request Ends ................................
            $cbt = $this->studentModel->getResponse($paper_id);
            $examTaken = $this->studentModel->checkRowExist($core->subject, $core->class, $core->term);
            $data = [
                'sch_id' => $_COOKIE['sch_id'],
                'student_id' => $_SESSION['student_id'],
                'subject' => $core->subject,
                'paperID' => $paper_id,
                'score' => $examTaken,
                'cbtTag' => $core->publishedAS,
                'cbt' => $cbt,
                'rowcount' => $cbtRowCount
            ];
            $this->view('students/success', $data);
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
    public function assessment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
            $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
            $data = [
                'term' => val_entry($_POST['term']),
                'subject' => val_entry($_POST['subject']),
                'class' => val_entry($_POST['class']),
                'subjects' => $subjects,
                'classes' => $classes,
            ];
            if ($scores = $this->studentModel->getScores($data['class'], $data['term'], $data['subject'])) {
                $data['scores'] = $scores;
                $this->view('students/assessment', $data);
            } else {
                $data = [
                    'term' => val_entry($_POST['term']),
                    'subject' => val_entry($_POST['subject']),
                    'class' => val_entry($_POST['class']),
                    'subjects' => $subjects,
                    'classes' => $classes,
                    'scores' => ''
                ];
                // Load view
                $this->view('students/assessment', $data);
            }
        } else {
            $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
            $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
            $data = [
                'subjects' => $subjects,
                'classes' => $classes,
                'scores' => ''
            ];
            // Load view
            $this->view('students/assessment', $data);
        }
    }
    public function scoring($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'id' => $id,
                'CA1' => val_entry($_POST['CA1']),
                'CA2' => val_entry($_POST['CA2']),
                'exam' => val_entry($_POST['exam']),
            ];
            if ($this->studentModel->updateScores($data)) {
                flash('msg', 'Scores updated!');
                echo "<script>
                history.go(-2)
          </script>";
            }
        } else {
            $scores = $this->studentModel->getSingleScores($id);
            $data = [
                'id' => $id,
                'CA1' => $scores->CA1,
                'CA2' => $scores->CA2,
                'exam' => $scores->exam
            ];

            // Load view
            $this->view('students/scoring', $data);
        }
    }

    public function delete($id)
    {
        if (!$this->isLoggedIn2()) {
            redirect('users/login');
        }
        if ($this->studentModel->deleteStudent($id)) {
            flash('msg', 'Successfull!');
            echo "
            <script>
                history.back();
            </script>
            ";
        } else {
            die('Soomething Went Wrong');
        }
    }
    public function profile($id)
    {
        if (!$this->isLoggedIn() && !$this->isLoggedIn2()) {
            // Student And Teacher Not Logged In
            redirect('users/login');
        } else if (!$this->isLoggedIn() && $this->isLoggedIn2()) {
            // Student Not Logged In But Teacher Is Logged In
            $details = $this->studentModel->findStudentById($id);
            $data = [
                'user' => $details,
            ];

            // Load view
            $this->view('students/profile', $data);
        } else if ($this->isLoggedIn() && !$this->isLoggedIn2()) {
            // Student Not Logged In But Teacher Is Logged In
            $details = $this->studentModel->findStudentById($id);
            $data = [
                'user' => $details,
            ];

            // Load view
            $this->view('students/profile', $data);
        } else {
            // Both is LOgged in
            $details = $this->studentModel->findStudentById($id);
            $data = [
                'user' => $details,
            ];

            // Load view
            $this->view('students/profile', $data);
        }
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

            if (!$this->isLoggedIn() && !$this->isLoggedIn2()) {
                // Student And Teacher Not Logged In
                redirect('users/login');
            } else if (!$this->isLoggedIn() && $this->isLoggedIn2()) {
                // Student Not Logged In But Teacher Is Logged In
                $user = $this->studentModel->findStudentById($id);
                $data = [
                    'user' => $user
                ];

                $this->view('students/upload', $data);
            } else if ($this->isLoggedIn() && !$this->isLoggedIn2()) {
                // Student Not Logged In But Teacher Is Logged In
                $user = $this->studentModel->findStudentById($id);
                $data = [
                    'user' => $user
                ];

                $this->view('students/upload', $data);
            } else {
                // Both is LOgged in
                $user = $this->studentModel->findStudentById($id);
                $data = [
                    'user' => $user
                ];

                $this->view('students/upload', $data);
            }
        }
    }

    // Remove | delete student profile image
    public function remove_img($id)
    {
        if (!$this->isLoggedIn() && !$this->isLoggedIn2()) {
            redirect('users/login');
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

    public function isLoggedIn2()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            return false;
        }
    }
}
