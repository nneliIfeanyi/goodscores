<?php
class Output extends Controller
{
  public $postModel;
  public $pageModel;
  public $userModel;
  public function __construct()
  {
    $this->postModel = $this->model('Post');
    $this->pageModel = $this->model('Page');
    $this->userModel = $this->model('User');
  }

  // Load Homepage
  public function index($paperID)
  {
    $params = $this->postModel->getParamsByPaperID2($paperID);
    $params1 = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
    $params2 = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
    $data = [
      'params1' => $params1,
      'params2' => $params2,
      'params' => $params,
    ];
    redirect('output/review_params/' . $paperID);
  }


  public function pdf($paperID)
  {
    //$params = $this->postModel->getParamsByPaperID2($paperID);
    //$params1 = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
    //$params2 = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
    $data = [
      //  'params1' => $params1,
      // 'params2' => $params2,
      // 'params' => $params
    ];
    $this->view('output/pdf', $data);
  }

  public function print($paperID)
  {
    $obj = $this->postModel->getObjectives($paperID);
    $theory = $this->postModel->getTheory($paperID);
    $params = $this->postModel->getParamsFromCore($paperID);
    $params1 = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
    $params2 = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
    $sch = $this->pageModel->getSchool($params->sch_id);
    $data = [
      'paperID' => $paperID,
      'params' => $params,
      'params1' => $params1,
      'params2' => $params2,
      'theory' => $theory,
      'obj' => $obj,
      'sch' => $sch
    ];
    $this->view('output/print', $data);
  }

  public function review_params($paperID)
  {
    $obj = $this->postModel->getObjectives($paperID);
    $theory = $this->postModel->getTheory($paperID);
    $params = $this->postModel->getParamsByPaperID2($paperID);
    $params1 = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
    $params2 = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
    $sch = $this->pageModel->getSchool($params->sch_id);
    $data = [
      'paperID' => $paperID,
      'params' => $params,
      'params1' => $params1,
      'params2' => $params2,
      'term' => $params->term,
      'year' => $params->year,
      'theory' => $theory,
      'obj' => $obj,
      'sch' => $sch
    ];
    if (!empty($data['params2']->instruction) || !empty($data['params1']->instruction) && !empty($data['params1']->duration)) {
      $this->view('output/index', $data);
    } else {
      $this->view('output/review_params', $data);
    }
  }
}
