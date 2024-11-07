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
            $ans = strtolower($_POST['ans']);
            $data = [
                'paperID' => $paper_id,
                'question' => trim($_POST['question']),
                'opt1' => trim($_POST['opt1']),
                'opt2' => trim($_POST['opt2']),
                'opt3' => trim($_POST['opt3']),
                'opt4' => trim($_POST['opt4']),
                'ans' => trim($ans),
                'sch_id' => $_COOKIE['sch_id'],
                'user_id' => $_SESSION['user_id'],
                'num_rows' => $num_rows,
                'isSubjective' => $_POST['isSubjective'],
                'section_alt' => $_POST['section_alt'],
                'sub_ins' => trim($_POST['sub_ins'])
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

        $num_rows = $this->postModel->checkTheoryNumRows($paper_id, $_COOKIE['sch_id']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'paperID' => $paper_id,
                'questionID' => $_POST['questionID'],
                'section' => 'theory_questions',
                'sch_id' => $_COOKIE['sch_id'],
                'user_id' => $_SESSION['user_id'],
                'question-A' => trim($_POST['question']),
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

    // Add comprehension
    public function custom($paper_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'content' => $_POST['content'],
                'paperID' => $paper_id
            ];

            if ($this->postModel->updateCustom($data)) {
                flash("msg", "Saved!");
                redirect('posts/custom/' . $paper_id);
            }
        } else { // Not post request
            die('Something went wrong');
        }
    }
}
