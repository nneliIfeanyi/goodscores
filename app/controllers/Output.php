<?php
class Output extends Controller
{
  public $postModel;
  public $pageModel;
  public function __construct()
  {
    $this->postModel = $this->model('Post');
    $this->pageModel = $this->model('Page');
  }

  // Load Homepage
  public function index($paperID)
  {
    $obj = $this->postModel->getObjectives($paperID);
    $theory = $this->postModel->getTheory($paperID);
    $params = $this->postModel->getParamsByPaperID2($paperID);
    $params1 = $this->postModel->getParamsByPaperID($paperID, 'objectives_questions');
    $params2 = $this->postModel->getParamsByPaperID($paperID, 'theory_questions');
    $sch = $this->pageModel->getSchool($params2->sch_id);
    $data = [
      'obj' => $obj,
      'params1' => $params1,
      'params2' => $params2,
      'params' => $params,
      'theory' => $theory,
      'sch' => $sch
    ];
    $this->view('output/index', $data);
  }
}
