<?php

class HomeController extends Controller {
    public function index()
    {
        $data['judul'] = 'Home';
        $this->view('Templates/header', $data);
        $this->view('Home/index', $data);
        $this->view('Templates/footer');
    }
}
