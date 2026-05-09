<?php

// app/controllers/MessageController.php

class MessageController extends Controller
{
    // =========================================================================
    // CUSTOMER SIDE
    // =========================================================================

    // GET /contact
    public function contact(): void
    {
        $this->view('shop/contact', [], 'main');
    }

    // POST /contact
    public function store(): void
    {
        CSRFMiddleware::verify();

        $name    = trim($this->post('name', ''));
        $email   = trim($this->post('email', ''));
        $orderId = $this->post('order_id', '') ?: null;
        $message = trim($this->post('message', ''));

        // ── Validation ──────────────────────────────────────────────────────
        $errors = [];

        if (empty($name))                      $errors[] = 'Name is required.';
        if (empty($email))                     $errors[] = 'Email is required.';
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (empty($message))                   $errors[] = 'Message is required.';
        if (strlen($message) < 10)             $errors[] = 'Message must be at least 10 characters.';

        if ($orderId !== null) {
            $orderId = (int) $orderId;
            if ($orderId <= 0) $orderId = null;
        }

        if ($errors) {
            Session::flash('message', implode(' ', $errors), 'error');
            $this->redirect('/contact');
            return;
        }

        // ── Save ─────────────────────────────────────────────────────────────
        $userId = $_SESSION['user_id'] ?? null;

        $id = Message::create([
            'user_id'  => $userId,
            'name'     => $name,
            'email'    => $email,
            'order_id' => $orderId,
            'message'  => $message,
        ]);

        if ($id) {
            $this->flashRedirect('/contact', 'Your message has been sent! We\'ll get back to you shortly.', 'success');
        } else {
            $this->flashRedirect('/contact', 'Something went wrong. Please try again.', 'error');
        }
    }

    // =========================================================================
    // ADMIN SIDE
    // =========================================================================

    // GET /admin/inquiries
    public function index(): void
    {
        $this->requireStaff();

        $status  = $this->get('status', '');
        $isRead  = $this->get('is_read', '');
        $search  = trim($this->get('search', ''));
        $page    = max(1, (int) $this->get('page', 1));
        $perPage = 20;

        $filters = compact('status', 'isRead', 'search');
        // Normalize key for model
        $filters['is_read'] = $isRead;

        $total      = Message::countAll($filters);
        $messages   = Message::getAll($filters, $page, $perPage);
        $totalPages = (int) ceil($total / $perPage);
        $unread     = Message::unreadCount();

        $this->view('admin/inquiries', compact(
            'messages', 'total', 'page', 'perPage', 'totalPages',
            'status', 'isRead', 'search', 'unread'
        ), 'admin');
    }

    // GET /admin/inquiries/{id}
    public function show(int $id): void
    {
        $this->requireStaff();

        $message = Message::find($id);
        if (! $message) {
            $this->flashRedirect('/admin/inquiries', 'Inquiry not found.', 'error');
            return;
        }

        // Mark as read when admin opens it
        if (! $message['is_read']) {
            Message::markRead($id);
            $message['is_read'] = 1;
        }

        $replies = Message::getReplies($id);

        $this->view('admin/inquiry-detail', compact('message', 'replies'), 'admin');
    }

    // POST /admin/inquiries/{id}/reply
    public function reply(int $id): void
    {
        $this->requireStaff();
        CSRFMiddleware::verify();

        $message = Message::find($id);
        if (! $message) {
            $this->jsonError('Inquiry not found.');
            return;
        }

        $body      = trim($this->post('body', ''));
        $sendEmail = (bool) $this->post('send_email', false);

        if (empty($body)) {
            $this->jsonError('Reply cannot be empty.');
            return;
        }

        $adminId   = (int) $_SESSION['user_id'];
        $emailSent = false;

        // ── Send email if requested ───────────────────────────────────────────
        if ($sendEmail) {
            $mailer = new Mailer();
            $emailSent = $mailer->send(
                to:      $message['customer_email'],
                toName:  $message['customer_name'],
                subject: 'Re: Your message to Petal & Soul',
                view:    'emails/inquiry-reply',
                data:    [
                    'customerName' => $message['customer_name'],
                    'originalMsg'  => $message['message'],
                    'replyBody'    => $body,
                ]
            );
        }

        // ── Save reply ────────────────────────────────────────────────────────
        $replyId = Message::addReply($id, $adminId, $body, $emailSent);

        if (! $replyId) {
            $this->jsonError('Failed to save reply.');
            return;
        }

        // Auto-resolve on reply (optional — remove if you don't want this)
        Message::setStatus($id, 'resolved');

        $adminName = $_SESSION['user_name'] ?? 'Admin';

        $this->jsonSuccess([
            'reply' => [
                'id'         => $replyId,
                'body'       => htmlspecialchars($body),
                'admin_name' => htmlspecialchars($adminName),
                'sent_email' => $emailSent,
                'created_at' => date('M j, Y g:i A'),
            ],
        ], $emailSent ? 'Reply sent and email delivered.' : 'Reply saved.');
    }

    // POST /admin/inquiries/{id}/status
    public function updateStatus(int $id): void
    {
        $this->requireStaff();
        CSRFMiddleware::verify();

        $status = $this->post('status', '');
        $ok     = Message::setStatus($id, $status);

        if ($ok) {
            $this->jsonSuccess(['status' => $status], 'Status updated.');
        } else {
            $this->jsonError('Invalid status.');
        }
    }
}