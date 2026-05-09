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
        Logger::info('User logged out', ['user_id' => $userId]);
        Session::logout();
        $this->flashRedirect('/login', 'You have been logged out.', 'info');
    }
}