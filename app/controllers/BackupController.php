<?php
// ─────────────────────────────────────────────
//  app/controllers/BackupController.php
// ─────────────────────────────────────────────

declare(strict_types=1);

class BackupController extends Controller {

    public function index(): void {
        $this->requireStaff();

        $backups = $this->getBackupList();

        $this->view('admin/backup', [
            'title'   => 'Backup & Restore',
            'backups' => $backups,
        ], 'admin');
    }

    // ── Download a fresh DB backup ────────────
    public function download(): void {
        $this->requireStaff();

        $dbHost = DB_HOST ?? 'localhost';
        $dbName = DB_NAME;
        $dbUser = DB_USER;
        $dbPass = DB_PASS;

        $filename  = 'petalsoul_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backupDir = __DIR__ . '/../../storage/backups/';

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filepath = $backupDir . $filename;

        // Build mysqldump command
        $mysqldump = 'C:\\xampps\\mysql\\bin\\mysqldump.exe';
        $command = sprintf(
            $mysqldump . ' --host=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($filepath)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
            Session::flash('error', 'Backup failed. Make sure mysqldump is available on the server.');
            $this->redirect('/admin/backup');
            return;
        }

        // Stream file to browser for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Pragma: no-cache');
        readfile($filepath);
        exit;
    }

    // ── Restore from uploaded .sql file ───────
    public function restore(): void {
        $this->requireStaff();

        CSRFMiddleware::verify($this->post(CSRF_TOKEN_NAME, ''))
            ?: $this->flashRedirect('/admin/backup', 'Invalid request.', 'error');

        $file = $_FILES['backup_file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'No file uploaded or upload error.');
            $this->redirect('/admin/backup');
            return;
        }

        // Validate .sql extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            Session::flash('error', 'Only .sql files are allowed.');
            $this->redirect('/admin/backup');
            return;
        }

        // Validate file size (max 50MB)
        if ($file['size'] > 50 * 1024 * 1024) {
            Session::flash('error', 'File too large. Maximum size is 50MB.');
            $this->redirect('/admin/backup');
            return;
        }

        $tmpPath = $file['tmp_name'];
        $dbHost  = DB_HOST ?? 'localhost';
        $dbName  = DB_NAME;
        $dbUser  = DB_USER;
        $dbPass  = DB_PASS;

        // Run mysql restore command
        $mysql = 'C:\\xampps\\mysql\\bin\\mysql.exe';
        $command = sprintf(
            $mysql . ' --host=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($tmpPath)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $error = implode(' ', $output);
            Session::flash('error', 'Restore failed: ' . $error);
            $this->redirect('/admin/backup');
            return;
        }

        Session::flash('success', 'Database restored successfully from ' . e($file['name']) . '.');
        $this->redirect('/admin/backup');
    }

    // ── List saved backups in storage/backups/ ─
    private function getBackupList(): array {
        $backupDir = __DIR__ . '/../../storage/backups/';

        if (!is_dir($backupDir)) return [];

        $files = glob($backupDir . '*.sql');
        if (!$files) return [];

        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'name'     => basename($file),
                'size'     => $this->formatBytes(filesize($file)),
                'created'  => date('M j, Y g:i A', filemtime($file)),
                'filepath' => $file,
            ];
        }

        // Newest first
        usort($backups, fn($a, $b) => filemtime($b['filepath']) <=> filemtime($a['filepath']));

        return $backups;
    }

    private function formatBytes(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}