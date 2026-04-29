<?php
declare(strict_types=1);

class LandingController extends Controller {
    public function index(): void {
        $this->view('landing', [], 'main');
    }
}