<?php
class Portal extends Controller
{
    public $userModel;
    public $pageModel;
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->pageModel = $this->model('Page');
    }

    // Load Homepage
    public function index($username)
    {
        if (isset($_COOKIE['sch_id'])) {
            redirect('users/login');
        }
        if (!$this->pageModel->findSch($username)) {
            redirect('pages/welcome');
        } elseif ($sch = $this->pageModel->findSch($username)) {
            setcookie('sch_id', $sch->id, time() + (86400 * 365), '/');
            setcookie('sch_name', $sch->name, time() + (86400 * 365), '/');
            setcookie('sch_username', $sch->username, time() + (86400 * 365), '/');
            setcookie('cbt', $sch->isCbt, time() + (86400 * 365), '/');
            // Redirect to Teachers section
            $redirect = URLROOT . '/users/login/';
            echo "<meta http-equiv='refresh' content='0; $redirect'>
        ";
            flash('msg', 'School authentication approved');
            // redirect('users/login');
        }
    }
}
