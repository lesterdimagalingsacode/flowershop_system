<?php
declare(strict_types=1);

class LogController extends Controller {

    public function index(): void {
        $this->requireStaff();

        $db = Database::getInstance();

        $page    = max(1, (int)$this->get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        // ── Filters ───────────────────────────
        $action   = trim($this->get('action', ''));
        $model    = trim($this->get('model', ''));
        $dateFrom = trim($this->get('date_from', ''));
        $dateTo   = trim($this->get('date_to', ''));

        $where  = ['1=1'];
        $params = [];

        if ($action) {
            $where[]  = 'a.action LIKE ?';
            $params[] = '%' . $action . '%';
        }

        if ($model) {
            $where[]  = 'a.model = ?';
            $params[] = $model;
        }

        if ($dateFrom) {
            $where[]  = 'DATE(a.created_at) >= ?';
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $where[]  = 'DATE(a.created_at) <= ?';
            $params[] = $dateTo;
        }

        $whereSQL = implode(' AND ', $where);

        $logs = $db->query(
            "SELECT a.*, 
                    CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                    u.email AS user_email,
                    u.role  AS user_role
             FROM audit_logs a
             LEFT JOIN users u ON a.user_id = u.id
             WHERE {$whereSQL}
             ORDER BY a.created_at DESC
             LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );

        $totalRow = $db->queryOne(
            "SELECT COUNT(*) as total FROM audit_logs a WHERE {$whereSQL}",
            $params
        );

        $total      = (int)($totalRow['total'] ?? 0);
        $totalPages = (int)ceil($total / $perPage);

        // ── Distinct models for filter dropdown ──
        $models = $db->query(
            "SELECT DISTINCT model FROM audit_logs WHERE model IS NOT NULL ORDER BY model ASC",
            []
        );

        $this->view('admin/logs', [
            'title'      => 'Activity Logs',
            'logs'       => $logs,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
            'filters'    => compact('action', 'model', 'dateFrom', 'dateTo'),
            'models'     => array_column($models, 'model'),
        ], 'admin');
    }
}