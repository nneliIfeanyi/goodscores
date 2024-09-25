<?php
class Output extends Controller
{
  public $postModel;
  public function __construct()
  {
    $this->postModel = $this->model('Post');
  }

  // Load Homepage
  public function index($paperID)
  {
      $obj = $this->postModel->getObjectives($paperID);
      $theory = $this->postModel->getTheory($paperID);
      $params = $this->postModel->getParamsByPaperID($paperID);
      $data = [
        'obj' => $obj,
        'params' => $params,
        'theory' => $theory
      ];
    $this->view('output/index', $data);
  }


}
