<?php
// ─────────────────────────────────────────────
//  app/controllers/AuthController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class AuthController extends Controller {

    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // ── GET /login ────────────────────────────
    public function loginForm(): void {
        $this->requireGuest();
        $this->view('auth/login', [
            'oldData' => json_decode(Session::getFlash('old')['message'] ?? '[]', true) ?? [],
            'errors'  => json_decode(Session::getFlash('errors')['message'] ?? '[]', true) ?? [],
        ], 'main');
    }

    // ── POST /login ───────────────────────────
    public function login(): void {
        $this->requireGuest();

        RateLimiter::purgeOld();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/login', 'Invalid request. Please try again.', 'error');

        $limiter = new RateLimiter();
        $limiter->handle('throttle');

        $validator = Validator::make($_POST, [
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            Session::flash('old', json_encode($_POST));
            $this->redirect('/login');
            return;
        }

        $user = $this->userModel->verifyCredentials(
            $validator->get('email'),
            $this->post('password')
        );

        if (!$user) {
            RateLimiter::hit('login');
            Session::flash('message', 'Invalid email or password.', 'error');
            Session::flash('old', json_encode(['email' => $validator->get('email')]));
            $this->redirect('/login');
            return;
        }

        RateLimiter::clear('login');
        Session::login($user);

        Logger::audit('user.login', [
            'model'    => 'User',
            'model_id' => $user['id'],
            'new'      => ['email' => $user['email'], 'role' => $user['role']],
        ]);

        Logger::info('User logged in', [
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ]);

        if (Session::isStaff()) {
            $this->flashRedirect('/admin/dashboard', 'Welcome back, ' . $user['first_name'] . '!');
        } else {
            $this->flashRedirect('/shop', 'Welcome back, ' . $user['first_name'] . '!');
        }
    }

    // ── GET /register ─────────────────────────
    public function registerForm(): void {
        $this->requireGuest();
        $this->view('auth/register', [
            'oldData' => json_decode(Session::getFlash('old')['message'] ?? '[]', true) ?? [],
            'errors'  => json_decode(Session::getFlash('errors')['message'] ?? '[]', true) ?? [],
        ], 'main');
    }

    // ── POST /register ────────────────────────
    public function register(): void {
        $this->requireGuest();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/register', 'Invalid request. Please try again.', 'error');

        $validator = Validator::make($_POST, [
            'first_name'            => 'required|min:2|max:50',
            'middle_name'           => 'max:50',
            'last_name'             => 'required|min:2|max:50',
            'email'                 => 'required|email|max:150',
            'password' => 'required|strong_password|max:255',
            'password_confirmation' => 'required',
            'phone'                 => 'max:20',
        ]);

        if ($this->post('password') !== $this->post('password_confirmation')) {
            $validator->validate(['password' => 'confirmed']);
        }

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            Session::flash('old', json_encode(array_diff_key($_POST, [
                'password'              => '',
                'password_confirmation' => '',
            ])));
            $this->redirect('/register');
            return;
        }

        if ($this->userModel->emailExists($validator->get('email'))) {
            Session::flash('errors', json_encode(['email' => ['This email is already registered.']]));
            Session::flash('old', json_encode(array_diff_key($_POST, [
                'password'              => '',
                'password_confirmation' => '',
            ])));
            $this->redirect('/register');
            return;
        }

        $userId = $this->userModel->create([
            'first_name'  => $validator->get('first_name'),
            'middle_name' => $this->post('middle_name') ?: null,
            'last_name'   => $validator->get('last_name'),
            'email'       => $validator->get('email'),
            'password'    => $this->post('password'),
            'phone'       => $validator->get('phone') ?: null,
            'role'        => ROLE_CUSTOMER,
        ]);

        if (!$userId) {
            $this->flashRedirect('/register', 'Registration failed. Please try again.', 'error');
            return;
        }

        // Generate and save verification token
        $token = bin2hex(random_bytes(32));
        $this->userModel->setVerificationToken((int)$userId, $token);

        // Send verification email
        $user   = $this->userModel->findById((int)$userId);
        $mailer = new Mailer();
        $mailer->send(
            $user['email'],
            $user['first_name'],
            'Verify your Petal & Soul account',
            'emails/verify-email',
            [
                'name' => $user['first_name'],
                'link' => APP_URL . '/verify-email?token=' . $token,
            ]
        );

        // Auto-login
        Session::login($user);

        Logger::audit('user.registered', [
            'model'    => 'User',
            'model_id' => $userId,
            'new'      => ['email' => $validator->get('email')],
        ]);

        Logger::info('New user registered', [
            'user_id' => $userId,
            'email'   => $validator->get('email'),
        ]);

        $this->flashRedirect('/shop', 'Welcome to Petal & Soul, ' . $user['first_name'] . '! 🌸 Check your email to verify your account.');
    }

    // ── POST /resend-verification ─────────────
    public function resendVerification(): void {
        $this->requireAuth();

        $userId = Session::userId();
        $user   = $this->userModel->findById($userId);

        if (!$user) {
            $this->jsonError('User not found.');
            return;
        }

        if ($this->userModel->isEmailVerified($userId)) {
            $this->jsonSuccess([], 'Your email is already verified.');
            return;
        }

        $token = bin2hex(random_bytes(32));
        $this->userModel->setVerificationToken($userId, $token);

        $mailer = new Mailer();
        $sent   = $mailer->send(
            $user['email'],
            $user['first_name'],
            'Verify your Petal & Soul account',
            'emails/verify-email',
            [
                'name' => $user['first_name'],
                'link' => APP_URL . '/verify-email?token=' . $token,
            ]
        );

        if ($sent) {
            $this->jsonSuccess([], 'Verification email sent! Check your inbox.');
        } else {
            $this->jsonError('Failed to send email. Please try again.');
        }
    }

    // ── POST /logout ──────────────────────────
    public function logout(): void {
        $userId = Session::userId();
        Logger::audit('user.logout', [
            'model'    => 'User',
            'model_id' => $userId,
        ]);
        Logger::info('User logged out', ['user_id' => $userId]);
        Session::logout();
        $this->flashRedirect('/login', 'You have been logged out.', 'info');
    }

    // ── GET /profile ──────────────────────────
    public function profileForm(): void {
        $this->requireAuth();

        $user = $this->userModel->findById(Session::userId());
        if (!$user) {
            $this->flashRedirect('/login', 'Session expired. Please log in again.', 'error');
            return;
        }

        $this->view('auth/profile', [
            'user'    => $user,
            'errors'  => json_decode(Session::getFlash('errors')['message'] ?? '[]', true) ?? [],
            'oldData' => json_decode(Session::getFlash('old')['message']    ?? '[]', true) ?? [],
        ], 'main');
    }

    // ── POST /profile ─────────────────────────
    public function updateProfile(): void {
        $this->requireAuth();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/profile', 'Invalid request. Please try again.', 'error');

        $validator = Validator::make($_POST, [
            'first_name'  => 'required|min:2|max:50',
            'middle_name' => 'max:50',
            'last_name'   => 'required|min:2|max:50',
            'phone'       => 'max:20',
        ]);

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            Session::flash('old',    json_encode($_POST));
            $this->redirect('/profile');
            return;
        }

        $userId = Session::userId();

        $this->userModel->update($userId, [
            'first_name'  => $validator->get('first_name'),
            'middle_name' => $this->post('middle_name') ?: null,
            'last_name'   => $validator->get('last_name'),
            'phone'       => $validator->get('phone') ?: null,
            'address'     => $this->post('address')   ?: null,
        ]);

        // Refresh session with updated name
        $updated = $this->userModel->findById($userId);
        Session::login($updated);

        Logger::audit('user.profile_updated', [
            'model'    => 'User',
            'model_id' => $userId,
            'new'      => [
                'first_name' => $validator->get('first_name'),
                'last_name'  => $validator->get('last_name'),
            ],
        ]);

        $this->flashRedirect('/profile', 'Profile updated successfully! ✅');
    }

    // ── POST /profile/password ────────────────
    public function updatePassword(): void {
        $this->requireAuth();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/profile', 'Invalid request. Please try again.', 'error');

        $userId = Session::userId();
        $user   = $this->userModel->findById($userId);

        // Verify current password
        if (!password_verify($this->post('current_password', ''), $user['password'])) {
            Session::flash('errors', json_encode(['current_password' => ['Current password is incorrect.']]));
            $this->redirect('/profile');
            return;
        }

        $validator = Validator::make($_POST, [
            'new_password' => 'required|strong_password|max:255',
        ]);

        if ($this->post('new_password') !== $this->post('new_password_confirmation')) {
            Session::flash('errors', json_encode(['new_password' => ['Passwords do not match.']]));
            $this->redirect('/profile');
            return;
        }

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            $this->redirect('/profile');
            return;
        }

        $this->userModel->updatePassword($userId, $this->post('new_password'));

        Logger::audit('password.changed', [
            'model'    => 'User',
            'model_id' => $userId,
        ]);

        $this->flashRedirect('/profile', 'Password changed successfully! 🔒');
    }

    public function forgotPasswordForm(): void
    {
        $this->view('auth/forgot-password', [], 'main');
    }
 
// ── POST /forgot-password ─────────────────────────────────────────────────
    public function forgotPassword(): void
    {
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/forgot-password', 'Invalid request. Please try again.', 'error');
    
        $email = trim(strtolower($this->post('email', '')));
    
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashRedirect('/forgot-password', 'Please enter a valid email address.', 'error');
            return;
        }
    
        // Always show success — never leak whether the email exists
        $user = $this->userModel->findByEmail($email);
    
        if ($user) {
            $token     = bin2hex(random_bytes(32));           // 64-char hex token
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 60 minutes
    
            $this->userModel->setResetToken((int) $user['id'], $token, $expiresAt);
    
            $resetLink = APP_URL . '/reset-password?token=' . $token;
    
            $mailer = new Mailer();
            $mailer->send(
                $user['email'],
                $user['name'],
                'Reset your Petal & Soul password',
                'emails/reset-password',
                [
                    'name' => $user['first_name'],
                    'link' => $resetLink,
                ]
            );
    
            Logger::info('Password reset email sent', ['user_id' => $user['id'], 'email' => $user['email']]);
        }
    
        // Same message regardless — prevents email enumeration
        Session::flash('message', 'If that email is registered, you\'ll receive a reset link shortly.');
        $this->redirect('/forgot-password');
    }
    
    // ── GET /reset-password?token=xxx ────────────────────────────────────────
    public function resetPasswordForm(): void
    {
        $token = trim($_GET['token'] ?? '');
    
        if (!$token) {
            $this->flashRedirect('/forgot-password', 'Invalid or missing reset link.', 'error');
            return;
        }
    
        $user = $this->userModel->findByResetToken($token);
    
        if (!$user) {
            $this->flashRedirect('/forgot-password', 'This reset link has expired or already been used. Please request a new one.', 'error');
            return;
        }
    
        $this->view('auth/reset-password', ['token' => $token], 'main');
    }
    
    // ── POST /reset-password ──────────────────────────────────────────────────
    public function resetPassword(): void
    {
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/forgot-password', 'Invalid request. Please try again.', 'error');
    
        $token    = trim($this->post('token', ''));
        $password = $this->post('password', '');
        $confirm  = $this->post('password_confirmation', '');
    
        if (!$token) {
            $this->flashRedirect('/forgot-password', 'Invalid reset link.', 'error');
            return;
        }
    
        $user = $this->userModel->findByResetToken($token);
    
        if (!$user) {
            $this->flashRedirect('/forgot-password', 'This reset link has expired or already been used. Please request a new one.', 'error');
            return;
        }
    
        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }
    
        $validator = Validator::make(
            ['password' => $password],
            ['password' => 'required|strong_password|max:255']
        );
    
        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            $this->redirect('/reset-password?token=' . urlencode($token));
            return;
        }
    
        $this->userModel->updatePassword((int) $user['id'], $password);
        $this->userModel->clearResetToken((int) $user['id']);
    
        Logger::audit('password.reset', [
            'model'    => 'User',
            'model_id' => $user['id'],
        ]);
    
        $this->flashRedirect('/login', '🔒 Password reset successfully! You can now log in with your new password.');
    }
}