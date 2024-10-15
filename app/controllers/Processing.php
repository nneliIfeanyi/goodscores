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
            $num_rows = $this->postModel->checkObjectivesNumRows($paper_id, $_POST['section_alt'], $_COOKIE['sch_id']);
            $data = [
                'paperID' => $paper_id,
                'question' => val_entry($_POST['question']),
                'opt1' => val_entry($_POST['opt1']),
                'opt2' => val_entry($_POST['opt2']),
                'opt3' => val_entry($_POST['opt3']),
                'opt4' => val_entry($_POST['opt4']),
                'sch_id' => $_COOKIE['sch_id'],
                'user_id' => $_SESSION['user_id'],
                'num_rows' => $num_rows,
                'section_alt' => $_POST['section_alt']
            ];

            if (empty($_SESSION['daigram'])) { // Question has no daigram
                $data['daigram'] = '';
                if ($this->postModel->setQuestions($data)) {
                    $num_rows = $num_rows + 1;
                    flash("msg", "Question $num_rows  is set successfully");
                    redirect('posts/add/' . $paper_id . '?section_alt=' . $_POST['section_alt']);
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
                    redirect('posts/add/' . $paper_id . '?section_alt=' . $_POST['section_alt']);
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

            $data = [
                'paperID' => $paper_id,
                'questionID' => $_POST['questionID'],
                'section' => 'theory_questions',
                'sch_id' => $_COOKIE['sch_id'],
                'user_id' => $_SESSION['user_id'],
                'question-A' => val_entry($_POST['question-A']),
                'A-i' => val_entry($_POST['A-i']),
                'A-ii' => val_entry($_POST['A-ii']),
                'A-iii' => val_entry($_POST['A-iii']),
                'A-iv' => val_entry($_POST['A-iv']),
                'question-B' => val_entry($_POST['question-B']),
                'B-i' => val_entry($_POST['B-i']),
                'B-ii' => val_entry($_POST['B-ii']),
                'B-iii' => val_entry($_POST['B-iii']),
                'B-iv' => val_entry($_POST['B-iv']),
                'question-C' => val_entry($_POST['question-C']),
                'C-i' => val_entry($_POST['C-i']),
                'C-ii' => val_entry($_POST['C-ii']),
                'C-iii' => val_entry($_POST['C-iii']),
                'question-D' => val_entry($_POST['question-D']),
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
            $data = [
                'content' => val_entry($_POST['content']),
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
