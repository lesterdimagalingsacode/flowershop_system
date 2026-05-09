<?php
// app/controllers/VerificationController.php

declare(strict_types=1);

class VerificationController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ── GET /verify-email?token=xxx ───────────
    public function verify(): void {
        $token = trim($_GET['token'] ?? '');

        if (!$token) {
            $this->flashRedirect('/shop', 'Invalid verification link.', 'error');
            return;
        }

        $user = $this->userModel->findByVerificationToken($token);

        if (!$user) {
            $this->flashRedirect('/shop', 'This verification link is invalid or has already been used.', 'error');
            return;
        }

        $this->userModel->markEmailVerified((int)$user['id']);

        // Update session if this is the logged-in user
        if (Session::userId() === (int)$user['id']) {
            $updated = $this->userModel->findById((int)$user['id']);
            Session::login($updated);
        }

        Logger::info('Email verified', ['user_id' => $user['id'], 'email' => $user['email']]);

        $this->flashRedirect('/shop', '✅ Email verified! Your account is now fully active.');
    }
}