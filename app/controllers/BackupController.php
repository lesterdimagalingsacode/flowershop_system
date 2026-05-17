<?php
// ─────────────────────────────────────────────
//  app/controllers/BackupController.php
//  Pure PHP backup/restore — works on InfinityFree
//  No mysqldump, no shell commands
// ─────────────────────────────────────────────

declare(strict_types=1);

class BackupController extends Controller {

    private string $backupDir;
    private string $imagesDir;

    // ── Change this password or move to config ─
    private string $zipPassword = 'PetalSoul@2025';

    public function __construct() {
        $this->backupDir = __DIR__ . '/../../storage/backups/';
        $this->imagesDir = __DIR__ . '/../../public/images/';
    }

    // ── Index ─────────────────────────────────
    public function index(): void {
        $this->requireStaff();

        $backups = $this->getBackupList();

        $this->view('admin/backup', [
            'title'       => 'Backup & Restore',
            'backups'     => $backups,
            'zipPassword' => $this->zipPassword,
        ], 'admin');
    }

    // ── Download backup ───────────────────────
    // Creates a password-protected .zip containing:
    //   - database.sql  (full DB export via PDO)
    //   - images/       (all product + hero images)
    public function download(): void {
        $this->requireStaff();

        if (!class_exists('ZipArchive')) {
            Session::flash('error', 'ZipArchive extension is not available on this server.');
            $this->redirect('/admin/backup');
            return;
        }

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }

        $timestamp  = date('Y-m-d_H-i-s');
        $sqlFile    = $this->backupDir . 'db_' . $timestamp . '.sql';
        $zipFile    = $this->backupDir . 'petalsoul_backup_' . $timestamp . '.zip';

        // ── Step 1: Export DB to .sql ─────────
        try {
            $sql = $this->exportDatabase();
            file_put_contents($sqlFile, $sql);
        } catch (\Throwable $e) {
            Logger::error('Backup DB export failed: ' . $e->getMessage());
            Session::flash('error', 'Database export failed: ' . $e->getMessage());
            $this->redirect('/admin/backup');
            return;
        }

        // ── Step 2: Create password-protected zip ──
        $zip = new \ZipArchive();

        if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            Session::flash('error', 'Could not create zip file.');
            @unlink($sqlFile);
            $this->redirect('/admin/backup');
            return;
        }

        // Set zip password
        $zip->setPassword($this->zipPassword);

        // Add database.sql
        $zip->addFile($sqlFile, 'database.sql');
        $zip->setEncryptionName('database.sql', \ZipArchive::EM_AES_256);

        // Add images folder recursively
        if (is_dir($this->imagesDir)) {
            $this->addFolderToZip($zip, $this->imagesDir, 'images');
        }

        $zip->close();

        // Clean up temp sql file
        @unlink($sqlFile);

        if (!file_exists($zipFile)) {
            Session::flash('error', 'Zip file was not created.');
            $this->redirect('/admin/backup');
            return;
        }

        // ── Step 3: Stream zip to browser ─────
        $filename = 'petalsoul_backup_' . $timestamp . '.zip';

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($zipFile));
        header('Pragma: no-cache');
        header('Expires: 0');
        Logger::audit('backup.downloaded', [
            'model'    => 'Backup',
            'model_id' => null,
            'new'      => ['filename' => $filename],
        ]);
        readfile($zipFile);
        exit;
    }

    // ── Restore from uploaded .zip ────────────
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

        // Validate .zip extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'zip') {
            Session::flash('error', 'Only .zip backup files are allowed.');
            $this->redirect('/admin/backup');
            return;
        }

        // Validate file size (max 20MB — InfinityFree upload limit)
        if ($file['size'] > 20 * 1024 * 1024) {
            Session::flash('error', 'File too large. Maximum size is 20MB.');
            $this->redirect('/admin/backup');
            return;
        }

        // Get password from form
        $password = trim($this->post('zip_password', ''));
        if (!$password) {
            Session::flash('error', 'Please enter the backup password.');
            $this->redirect('/admin/backup');
            return;
        }

        if (!class_exists('ZipArchive')) {
            Session::flash('error', 'ZipArchive extension is not available on this server.');
            $this->redirect('/admin/backup');
            return;
        }

        $tmpZip = $file['tmp_name'];
        $zip    = new \ZipArchive();

        if ($zip->open($tmpZip) !== true) {
            Session::flash('error', 'Could not open zip file. It may be corrupted.');
            $this->redirect('/admin/backup');
            return;
        }

        // Set password for decryption
        $zip->setPassword($password);

        // ── Step 1: Extract database.sql ──────
        $sqlContent = $zip->getFromName('database.sql');

        if ($sqlContent === false) {
            $zip->close();
            Session::flash('error', 'Invalid backup file — database.sql not found. Check your password.');
            $this->redirect('/admin/backup');
            return;
        }

        // ── Step 2: Import SQL via PDO ────────
        try {
            $this->importDatabase($sqlContent);
        } catch (\Throwable $e) {
            $zip->close();
            Logger::error('Restore DB import failed: ' . $e->getMessage());
            Session::flash('error', 'Database restore failed: ' . $e->getMessage());
            $this->redirect('/admin/backup');
            return;
        }

        // ── Step 3: Restore images ────────────
        $imageCount = 0;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            // Only extract files inside images/
            if (strpos($name, 'images/') !== 0) continue;

            // Skip directory entries
            if (substr($name, -1) === '/') continue;

            // Build destination path
            $destPath = $this->imagesDir . substr($name, strlen('images/'));
            $destDir  = dirname($destPath);

            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            $content = $zip->getFromIndex($i);
            if ($content !== false) {
                file_put_contents($destPath, $content);
                $imageCount++;
            }
        }

        $zip->close();
        Logger::audit('backup.restored', [
            'model'    => 'Backup',
            'model_id' => null,
            'new'      => ['filename' => $file['name'], 'images_restored' => $imageCount],
        ]);
        
        Session::flash('success', 'Restore successful! Database imported and ' . $imageCount . ' images restored.');
        $this->redirect('/admin/backup');
    }

    // ── Export entire DB to SQL string ────────
    private function exportDatabase(): string {
        $db  = Database::getInstance();
        $pdo = $db->getPdo();

        $sql  = "-- Petal & Soul Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- -----------------------------------------------\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        // Get all tables
        $tables = $pdo->query("SHOW TABLES")->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // ── DROP + CREATE TABLE ───────────
            $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`")
                              ->fetch(\PDO::FETCH_ASSOC);

            $sql .= "-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $createStmt['Create Table'] . ";\n\n";

            // ── INSERT rows ───────────────────
            $rows = $pdo->query("SELECT * FROM `{$table}`")->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $columns = '`' . implode('`, `', array_keys($rows[0])) . '`';
                $sql .= "INSERT INTO `{$table}` ({$columns}) VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $escaped = array_map(function ($val) use ($pdo) {
                        if ($val === null) return 'NULL';
                        return $pdo->quote((string)$val);
                    }, array_values($row));

                    $values[] = '(' . implode(', ', $escaped) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    // ── Import SQL string via PDO ─────────────
    private function importDatabase(string $sqlContent): void {
        $db  = Database::getInstance();
        $pdo = $db->getPdo();

        // Split into individual statements
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");

        // Remove comments and split by semicolon
        $lines = explode("\n", $sqlContent);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--')) continue;
            $cleanLines[] = $trimmed;
        }

        $statements = explode(';', implode(' ', $cleanLines));

        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement)) continue;

            try {
                $pdo->exec($statement);
            } catch (\PDOException $e) {
                // Log but continue — some statements may fail on duplicates
                Logger::error('SQL import error: ' . $e->getMessage() . ' | Statement: ' . substr($statement, 0, 100));
            }
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }

    // ── Add folder recursively to zip ─────────
    private function addFolderToZip(\ZipArchive $zip, string $folder, string $zipPath): void {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isFile()) continue;

            $filePath   = $file->getRealPath();
            $relativePath = $zipPath . '/' . substr($filePath, strlen(realpath($folder)) + 1);
            $relativePath = str_replace('\\', '/', $relativePath);

            $zip->addFile($filePath, $relativePath);
            $zip->setEncryptionName($relativePath, \ZipArchive::EM_AES_256);
        }
    }

    // ── List saved backups ────────────────────
    private function getBackupList(): array {
        if (!is_dir($this->backupDir)) return [];

        $files = glob($this->backupDir . '*.zip');
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

        usort($backups, fn($a, $b) => filemtime($b['filepath']) <=> filemtime($a['filepath']));

        return $backups;
    }

    private function formatBytes(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}