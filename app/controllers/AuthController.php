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

        // CSRF check
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/login', 'Invalid request. Please try again.', 'error');

        // Rate limiting
        $limiter = new RateLimiter();
        $limiter->handle('throttle');

        // Validate input
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

        // Verify credentials
        $user = $this->userModel->verifyCredentials(
            $validator->get('email'),
            $this->post('password')
        );

        if (!$user) {
            // Record failed attempt for rate limiting
            RateLimiter::hit('login');

            Session::flash('message', 'Invalid email or password.', 'error');
            Session::flash('old', json_encode(['email' => $validator->get('email')]));
            $this->redirect('/login');
            return;
        }

        // Success — clear rate limit, start session
        RateLimiter::clear('login');
        Session::login($user);

        // Log the action
        Logger::info('User logged in', [
            'user_id' => $user['id'],
            'email'   => $user['email'],
            'role'    => $user['role'],
        ]);

        // Redirect based on role
        if (Session::isStaff()) {
            $this->flashRedirect('/admin/dashboard', 'Welcome back, ' . $user['name'] . '!');
        } else {
            $this->flashRedirect('/shop', 'Welcome back, ' . $user['name'] . '!');
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

        // CSRF check
        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/register', 'Invalid request. Please try again.', 'error');

        // Validate
        $validator = Validator::make($_POST, [
            'name'                  => 'required|min:2|max:100',
            'email'                 => 'required|email|max:150',
            'password'              => 'required|min:8|max:255',
            'password_confirmation' => 'required',
            'phone'                 => 'max:20',
        ]);

        // Check password confirmation manually
        if ($this->post('password') !== $this->post('password_confirmation')) {
            $validator->validate(['password' => 'confirmed']);
        }

        if ($validator->fails()) {
            Session::flash('errors', json_encode($validator->errors()));
            Session::flash('old', json_encode(array_diff_key($_POST, ['password' => '', 'password_confirmation' => ''])));
            $this->redirect('/register');
            return;
        }

        // Check email uniqueness
        if ($this->userModel->emailExists($validator->get('email'))) {
            Session::flash('errors', json_encode(['email' => ['This email is already registered.']]));
            Session::flash('old', json_encode(array_diff_key($_POST, ['password' => '', 'password_confirmation' => ''])));
            $this->redirect('/register');
            return;
        }

        // Create user
        $userId = $this->userModel->create([
            'name'     => $validator->get('name'),
            'email'    => $validator->get('email'),
            'password' => $this->post('password'),
            'phone'    => $validator->get('phone'),
            'role'     => ROLE_CUSTOMER,
        ]);

        if (!$userId) {
            $this->flashRedirect('/register', 'Registration failed. Please try again.', 'error');
            return;
        }

        // Auto-login after registration
        $user = $this->userModel->findById((int)$userId);
        Session::login($user);

        Logger::info('New user registered', ['user_id' => $userId, 'email' => $validator->get('email')]);

        $this->flashRedirect('/shop', 'Welcome to 404: Flower Not Found! 🌸');
    }

    // ── POST /logout ──────────────────────────
    public function logout(): void {
        $userId = Session::userId();
        Logger::info('User logged out', ['user_id' => $userId]);
        Session::logout();
        $this->flashRedirect('/login', 'You have been logged out.', 'info');
    }
}