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
    redirect('users/dashboard');
  }






  // Show Single Post
  public function show($paperID)
  {

    if ($_GET['class'] && $_GET['subject']) {
      if (empty($_GET['section_alt'])) {
        $obj = $this->postModel->getObjectives($paperID, $_GET['section_alt']);
        $params = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
        $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
        $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
        $data = [
          'section' => 'objectives_questions',
          'class' => $_GET['class'],
          'subject' => $_GET['subject'],
          'term' => TERM,
          'year' => SCH_SESSION,
          'obj' => $obj,
          'params' => $params,
          'subjects' => $subjects,
          'classes' => $classes,
          'section_alt' => $params->section_alt
        ];
      } else {
        $obj = $this->postModel->getObjectives($paperID, $_GET['section_alt']);
        $params = $this->postModel->getParamsByPaperID($paperID, 'others');
        $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
        $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
        $data = [
          'section' => 'objectives_questions',
          'class' => $_GET['class'],
          'subject' => $_GET['subject'],
          'term' => TERM,
          'year' => SCH_SESSION,
          'obj' => $obj,
          'params' => $params,
          'subjects' => $subjects,
          'classes' => $classes,
          'section_alt' => $params->section_alt
        ];
      }
      $this->view('posts/show', $data);
    } // get request ends
    else {
      die('Something went wrong');
    }
  }

  public function show2($paperID)
  {

    if ($_GET['class'] && $_GET['subject']) {
      $theory = $this->postModel->getTheory($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
      $subjects = $this->userModel->getUserSubjects($_SESSION['user_id']);
      $classes = $this->userModel->getUserClasses($_SESSION['user_id']);
      $data = [
        'class' => $_GET['class'],
        'subject' => $_GET['subject'],
        'term' => TERM,
        'year' => SCH_SESSION,
        'theory' => $theory,
        'params' => $params,
        'subjects' => $subjects,
        'classes' => $classes,
        'section' => 'theory_questions'
      ];
      if (empty($params->num_rows)) {
        $this->view('users/review_params', $data);
      } else {
        $this->view('posts/show2', $data);
      }
    } // get request

    else {
      die('Something went wrong');
    }
  }

  // Add Comprehension 
  public function custom($paper_id)
  {
    $params = $this->postModel->getParamsFromCore($paper_id);
    $content = $this->postModel->getCustomContent($paper_id);
    $data = [
      'sch_id' => $_COOKIE['sch_id'],
      'paperID' => $paper_id,
      'year' => $params->year,
      'class' => $params->class,
      'term' => $params->term,
      'subject' => $params->subject,
      'content' => $content
    ];
    $this->view('posts/custom', $data);
  }







  // Add Objectives Question
  public function add($paper_id)
  {
    if (empty($_GET['section_alt'])) {
      $params = $this->postModel->getParamsByPaperID($paper_id, 'objectives_questions');
      $num_rows = $this->postModel->checkObjectivesNumRows($params->paperID, $_GET['section_alt'], $_COOKIE['sch_id']);

      $data = [
        'params' => $params,
        'sch_id' => $_COOKIE['sch_id'],
        'section' => 'objectives_questions',
        'paperID' => $paper_id,
        'num_rows' => $num_rows,
        'year' => $params->year,
        'class' => $params->class,
        'term' => $params->term,
        'subject' => $params->subject,
        'section_alt' => $params->section_alt,
        'total_subject_num_rows' => $params->num_rows,
        'tag' => $params->tag
      ];
    } else {
      $params = $this->postModel->getParamsByPaperID($paper_id, 'others');
      $num_rows = $this->postModel->checkObjectivesNumRows($params->paperID, $_GET['section_alt'], $_COOKIE['sch_id']);

      $data = [
        'params' => $params,
        'sch_id' => $_COOKIE['sch_id'],
        'section' => 'objectives_questions',
        'paperID' => $paper_id,
        'num_rows' => $num_rows,
        'year' => $params->year,
        'class' => $params->class,
        'term' => $params->term,
        'subject' => $params->subject,
        'section_alt' => $params->section_alt,
        'total_subject_num_rows' => $params->num_rows,
        'tag' => $params->tag
      ];
    }
    $this->view('posts/add', $data);
  }


  public function daigram($paperID)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (is_array($_FILES)) {
        $file = $_FILES['photo']['tmp_name'];
        $source_properties = getimagesize($file);
        $image_type = $source_properties[2];
        if ($image_type == IMAGETYPE_JPEG) {
          $image_resource_id = imagecreatefromjpeg($file);
          $target_layer = fn_resize($image_resource_id, $source_properties[0], $source_properties[1]);
          imagejpeg($target_layer, "daigrams/" . $_FILES['photo']['name']);
          $db_image_file =  "daigrams/" . $_FILES['photo']['name'];
          $_SESSION['daigram'] = $db_image_file;
          echo "<script>
                history.go(-2)
          </script>";
        } elseif ($image_type == IMAGETYPE_PNG) {
          $image_resource_id = imagecreatefrompng($file);
          $target_layer = fn_resize($image_resource_id, $source_properties[0], $source_properties[1]);
          imagepng($target_layer, "daigrams/" . $_FILES['photo']['name']);
          $db_image_file =  "daigrams/" . $_FILES['photo']['name'];
          $_SESSION['daigram'] = $db_image_file;
          echo "<script>
                history.go(-2)
          </script>";
        }
      } // End if is_array
    } else {
      $data = [
        'paperID' => $paperID,
      ];

      $this->view('posts/daigram', $data);
    }
  }

  // Add theory view
  public function add2($paper_id)
  {
    $params = $this->postModel->getParamsByPaperID($paper_id, 'theory_questions');
    $num_rows = $this->postModel->checkTheoryNumRows($paper_id, $_COOKIE['sch_id']);
    // Not post request method
    $data = [
      'sch_id' => $_COOKIE['sch_id'],
      'section' => 'theory_questions',
      'paperID' => $paper_id,
      'num_rows' => $num_rows,
      'year' => $params->year,
      'class' => $params->class,
      'term' => $params->term,
      'subject' => $params->subject,
      'params' => $params,
      'total_subject_num_rows' => $params->num_rows
    ];
    $this->view('posts/add2', $data);
  }

  // Edit Post
  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      //$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      $ans = strtolower($_POST['ans']);
      $data = [
        'id' => $id,
        'question' => trim($_POST['question']),
        'opt1' => trim($_POST['opt1']),
        'opt4' => trim($_POST['opt4']),
        'opt2' => trim($_POST['opt2']),
        'opt3' => trim($_POST['opt3']),
        'ans' => trim($ans),
        'isSubjective' => $_POST['isSubjective'],
        'sub_ins' => trim($_POST['sub_ins'])
      ];

      if (empty($_SESSION['daigram'])) { // Update has no diagram
        $data['daigram'] = $_POST['daigram'];
        if ($this->postModel->updateObj($data)) {
          flash('msg', 'Question is Updated');
          redirect('posts/edit/' . $id . '?section_alt=' . $_POST['section_alt']);
        } else {
          die('Something went wrong');
        }
      } else {
        $data['daigram'] = $_SESSION['daigram'];
        if ($this->postModel->updateObj($data)) {
          unset($_SESSION['daigram']);
          flash('msg', 'Question is Updated');
          redirect('posts/edit/' . $id . '?section_alt=' . $_POST['section_alt']);
        } else {
          die('Something went wrong');
        }
      }
    } else {
      // Get post from model
      if (empty($_GET['section_alt'])) {
        $post = $this->postModel->getObjById($id);
        $params = $this->postModel->getParamsByPaperID($post->paperID, 'objectives_questions');
        $data = [
          'post' => $post,
          'params' => $params
        ];
      } else {
        $post = $this->postModel->getObjById($id);
        $params = $this->postModel->getParamsByPaperID($post->paperID, 'others');
        $data = [
          'post' => $post,
          'params' => $params
        ];
      }
      $this->view('posts/edit', $data);
    }
  }

  // Edit Post
  public function edit2($id)
  {
    // Get post from model
    $post = $this->postModel->getTheoryById($id);
    $params = $this->postModel->getParamsByPaperID($post->paperID, 'theory_questions');
    $data = [
      'post' => $post,
      'params' => $params
    ];

    $this->view('posts/edit2', $data);
  }



  // Delete single obj question
  public function delete($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deleteObj($id)) {
        // Redirect to login
        flash('msg', 'Question Removed', 'alert alert-danger');
        redirect('posts/show/' . $_POST['paperID'] . '?class=' . $_POST['class'] . '&subject=' . $_POST['subject'] . '&section_alt=' . $_POST['section_alt']);
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }


  // Delete single theory question
  public function delete2($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deleteTheory($id)) {
        // Redirect to login
        flash('msg', 'Question Removed', 'alert alert-danger');
        redirect('posts/show2/' . $_POST['paperID'] . '?class=' . $_POST['class'] . '&subject=' . $_POST['subject']);
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }

  // Delete all  obj question
  public function delete_section($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($_POST['section'] == 'objectives_questions') {
        if ($this->postModel->deleteObjAll($id, '')) {
          // Delete Params aswell
          $this->postModel->deleteParamsData($id, $_POST['section']);
          flash('msg', 'Section Removed Successfully', 'alert alert-danger');
          redirect('users/dashboard');
        } else {
          die('Something went wrong');
        }
      } elseif ($_POST['section'] == 'theory_questions') {
        if ($this->postModel->deleteTheoryAll($id)) {
          // Delete Params aswell
          $this->postModel->deleteParamsData($id, $_POST['section']);
          flash('msg', 'Section Removed Successfully', 'alert alert-danger');
          redirect('users/dashboard');
        } else {
          die('Something went wrong');
        }
      } elseif ($_POST['section'] == 'custom') {
        if ($this->postModel->deleteCustomAll($id)) {
          // Delete Params aswell
          $this->postModel->deleteParamsData($id, $_POST['section']);
          flash('msg', 'Section Removed Successfully', 'alert alert-danger');
          redirect('users/dashboard');
        } else {
          die('Something went wrong');
        }
      } elseif ($_POST['section'] == 'others') {
        if ($this->postModel->deleteObjAll($id, $_POST['section_alt'])) {
          // Delete Params aswell
          $this->postModel->deleteParamsData($id, $_POST['section']);
          flash('msg', 'Section Removed Successfully', 'alert alert-danger');
          redirect('users/dashboard');
        } else {
          die('Something went wrong');
        }
      }
    } else {
      redirect('posts');
    }
  }
}
