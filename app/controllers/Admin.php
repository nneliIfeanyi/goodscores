<?php
class Pages extends Controller
{
    public function __construct()
    {
    }

    // Load Homepage
    public function index()
    {
        // If logged in, redirect to posts
        if (isset($_SESSION['user_id'])) {
            redirect('posts');
        }

        //Set Data
        $data = [];

        // Load homepage/index view
        $this->view('users/login', $data);
    }
}
