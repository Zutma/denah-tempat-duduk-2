<?php
require_once __DIR__ . '/BaseController.php';

class DashboardController extends BaseController {
    public function index($conn) {
        $this->checkAuth();

        $pageTitle = 'Dashboard';
        $this->render('dashboard', [
            'pageTitle' => $pageTitle
        ]);
    }
}
