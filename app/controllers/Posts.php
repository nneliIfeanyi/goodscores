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
      $obj = $this->postModel->getObjectives($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID);
      $data = [
        'class' => $_GET['class'],
        'subject' => $_GET['subject'],
        'term' => TERM,
        'year' => SCH_SESSION,
        'obj' => $obj,
        'params' => $params,
      ];
      $this->view('posts/show', $data);
    } // get request
    else {
      die('Something went wrong');
    }
  }

  public function show2($paperID)
  {

    if ($_GET['class'] && $_GET['subject']) {
      $theory = $this->postModel->getTheory($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID);
      $expected_num_rows = $this->postModel->checkSubjectNumRows($params->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
      $data = [
        'class' => $_GET['class'],
        'subject' => $_GET['subject'],
        'term' => TERM,
        'year' => SCH_SESSION,
        'theory' => $theory,
        'params' => $params,
        'expected_num_rows' => $expected_num_rows
      ];
      $this->view('posts/show2', $data);
    } // get request

    else {
      die('Something went wrong');
    }
  }








  // Add Post
  public function add($paper_id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $params = $this->postModel->getParamsByPaperID($paper_id);
      $num_rows = $this->postModel->checkObjectivesNumRows($paper_id, $_COOKIE['sch_id']);
      $expected_num_rows = $this->postModel->checkSubjectNumRows($params->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
      $data = [
        'paperID' => $paper_id,
        'question' => trim($_POST['question']),
        'opt1' => trim($_POST['opt1']),
        'opt2' => trim($_POST['opt2']),
        'opt3' => trim($_POST['opt3']),
        'opt4' => trim($_POST['opt4']),
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'num_rows' => $num_rows,
        'total_subject_num_rows' => $expected_num_rows->num_rows
      ];
      if (empty($_SESSION['daigram'])) { // Question has no daigram
        $data['daigram'] = '';
        if ($this->postModel->setQuestions($data)) {
          flash('msg', 'Question ' . $num_rows + (1) . ' is set successfully');
          redirect('posts/add/' . $paper_id);
        } else {
          die('Something went wrong..');
        }
      } else { // Question has daigram
        $data['daigram'] = $_SESSION['daigram'];
        if ($this->postModel->setQuestions($data)) {
          // Unset question diAGRAM
          unset($_SESSION['daigram']);
          flash('msg', 'Question ' . $num_rows + (1) . ' is set successfully');
          redirect('posts/add/' . $paper_id);
        }
      }
    } else {
      $params = $this->postModel->getParamsByPaperID($paper_id);
      $num_rows = $this->postModel->checkObjectivesNumRows($params->paperID, $_COOKIE['sch_id']);
      $expected_num_rows = $this->postModel->checkSubjectNumRows($params->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
      $data = [
        'paperID' => $paper_id,
        'num_rows' => $num_rows,
        'year' => $params->year,
        'class' => $params->class,
        'term' => $params->term,
        'section' => $params->section,
        'subject' => $params->subject,
        'total_subject_num_rows' => $expected_num_rows->num_rows
      ];

      $this->view('posts/add', $data);
    }
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

  // Add Post
  public function add2($paper_id)
  {
    $params = $this->postModel->getParamsByPaperID($paper_id);
    $num_rows = $this->postModel->checkTheoryNumRows($paper_id, $_COOKIE['sch_id']);
    $expected_num_rows = $this->postModel->checkSubjectNumRows($params->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
      // Pull the set num_rows for the particular subject

      $data = [
        'paperID' => $paper_id,
        'questionID' => $_POST['questionID'],
        'section' => 'theory_questions',
        'sch_id' => $_COOKIE['sch_id'],
        'user_id' => $_SESSION['user_id'],
        'question-A' => $_POST['question-A'],
        'A-i' => $_POST['A-i'],
        'A-ii' => $_POST['A-ii'],
        'A-iii' => $_POST['A-iii'],
        'A-iv' => $_POST['A-iv'],
        'question-B' => $_POST['question-B'],
        'B-i' => $_POST['B-i'],
        'B-ii' => $_POST['B-ii'],
        'B-iii' => $_POST['B-iii'],
        'B-iv' => $_POST['B-iv'],
        'question-C' => $_POST['question-C'],
        'C-i' => $_POST['C-i'],
        'C-ii' => $_POST['C-ii'],
        'C-iii' => $_POST['C-iii'],
        'question-D' => $_POST['question-D'],
        //'total_subject_num_rows' => $expected_num_rows->num_rows2
      ];

      if (empty($_SESSION['daigram'])) { // Question has no daigram
        $data['img'] = '';
        if ($this->postModel->setQuestions2($data)) {
          flash('msg', 'Theory Question ' . $num_rows + (1) . ' is set successfully');
          redirect('posts/add2/' . $paper_id);
        } else {
          die('Something went wrong..');
        }
      } else { // Question has daigram
        $data['img'] = $_SESSION['daigram'];
        if ($this->postModel->setQuestions2($data)) {
          // Unset question diAGRAM
          unset($_SESSION['daigram']);
          flash('msg', 'Theory Question ' . $num_rows + (1) . ' is set successfully');
          redirect('posts/add2/' . $paper_id);
        } else {
          die('Something went wrong..');
        }
      }
    } else { // Not post request method
      $data = [
        'paperID' => $paper_id,
        'num_rows' => $num_rows,
        'year' => $params->year,
        'class' => $params->class,
        'term' => $params->term,
        'subject' => $params->subject,
        'total_subject_num_rows' => $expected_num_rows->num_rows2
      ];

      $this->view('posts/add2', $data);
    }
  }

  // Edit Post
  public function edit($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Sanitize POST
      $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      $data = [
        'id' => $id,
        'question' => trim($_POST['question']),
        'opt1' => trim($_POST['opt1']),
        'opt4' => trim($_POST['opt4']),
        'opt2' => trim($_POST['opt2']),
        'opt3' => trim($_POST['opt3'])
      ];

      if (empty($_SESSION['daigram'])) { // Update has no diagram
        $data['daigram'] = $_POST['daigram'];
        if ($this->postModel->updateObj($data)) {
          flash('msg', 'Question is Updated');
          redirect('posts/edit/' . $id);
        } else {
          die('Something went wrong');
        }
      } else {
        $data['daigram'] = $_SESSION['daigram'];
        if ($this->postModel->updateObj($data)) {
          unset($_SESSION['daigram']);
          flash('msg', 'Question is Updated');
          redirect('posts/edit/' . $id);
        } else {
          die('Something went wrong');
        }
      }
    } else {
      // Get post from model
      $post = $this->postModel->getObjById($id);
      $params = $this->postModel->getParamsByPaperID($post->paperID);
      $data = [
        'post' => $post,
        'params' => $params
      ];

      $this->view('posts/edit', $data);
    }
  }


  // Edit Post
  public function edit2($id)
  {
      // Get post from model
      $post = $this->postModel->getTheoryById($id);
      $params = $this->postModel->getParamsByPaperID($post->paperID);
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
        redirect('posts/show/' . $_POST['paperID'] . '?class=' . $_POST['class'] . '&subject=' . $_POST['subject']);
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
  public function delete_obj_all($id)
  {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Execute
      if ($this->postModel->deleteObjall($id)) {
        // Delete Params aswell
        $this->postModel->deleteParamsData($id);
        flash('msg', 'Questions Removed', 'alert alert-danger');
        redirect('users/dashboard');
      } else {
        die('Something went wrong');
      }
    } else {
      redirect('posts');
    }
  }
}
