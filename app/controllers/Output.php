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
    $params = $this->postModel->getParamsByPaperID($paperID);
    $sch = $this->pageModel->getSchool($params->sch_id);
    $check = $this->postModel->checkSubjectNumRows($params->class, $_SESSION['user_id'], $_COOKIE['sch_id']);
    $data = [
      'obj' => $obj,
      'params' => $params,
      'theory' => $theory,
      'sch' => $sch,
      'check' => $check
    ];
    $this->view('output/index', $data);
  }
}
