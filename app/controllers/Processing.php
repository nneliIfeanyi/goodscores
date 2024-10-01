<?php
class Processing extends Controller
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


    // Add Obj
    public function add($paper_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $num_rows = $this->postModel->checkObjectivesNumRows($paper_id, $_COOKIE['sch_id']);
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
            ];

            if (empty($_SESSION['daigram'])) { // Question has no daigram
                $data['daigram'] = '';
                if ($this->postModel->setQuestions($data)) {
                    $num_rows = $num_rows + 1;
                    flash("msg", "Question $num_rows  is set successfully");
                    redirect('posts/add/' . $paper_id);
                } else {
                    die('Something went wrong..');
                }
            } else { // Question has daigram
                $data['daigram'] = $_SESSION['daigram'];
                if ($this->postModel->setQuestions($data)) {
                    // Unset question diAGRAM
                    unset($_SESSION['daigram']);
                    $num_rows = $num_rows + 1;
                    flash("msg", "Question $num_rows  is set successfully");
                    redirect('posts/add/' . $paper_id);
                }
            }
        } else {
            die('Something went wrong');
        }
    }


    // Add Post
    public function add2($paper_id)
    {
        //$params = $this->postModel->getParamsByPaperID($paper_id);
        $num_rows = $this->postModel->checkTheoryNumRows($paper_id, $_COOKIE['sch_id']);
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
                    $num_rows = $num_rows + 1;
                    flash("msg", "Theory Question  $num_rows is set successfully");
                    redirect('posts/add2/' . $paper_id);
                } else {
                    die('Something went wrong..');
                }
            } else { // Question has daigram
                $data['img'] = $_SESSION['daigram'];
                if ($this->postModel->setQuestions2($data)) {
                    // Unset question diAGRAM
                    unset($_SESSION['daigram']);
                    $num_rows = $num_rows + 1;
                    flash("msg", "Theory Question $num_rows is set successfully");
                    redirect('posts/add2/' . $paper_id);
                } else {
                    die('Something went wrong..');
                }
            }
        } else {
            die('Something went wrong');
        }
    }
}
