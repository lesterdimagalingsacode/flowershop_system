<?php
// ─────────────────────────────────────────────
//  core/Model.php — Base Model
//  All queries use PDO prepared statements.
//  Soft delete supported via deleted_at column.
// ─────────────────────────────────────────────

declare(strict_types=1);

abstract class Model {

    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected bool $softDelete   = true;   // use deleted_at

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ── Find by primary key ───────────────────
    public function find(int $id): array|false {
        $soft = $this->softDelete ? "AND deleted_at IS NULL" : "";
        return $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? $soft LIMIT 1",
            [$id]
        );
    }

    // ── Find or abort ─────────────────────────
    public function findOrFail(int $id): array {
        $record = $this->find($id);
        if (!$record) {
            throw new RuntimeException("Record not found in {$this->table} with id $id", 404);
        }
        return $record;
    }

    // ── Find by single column ─────────────────
    public function findBy(string $column, mixed $value): array|false {
        $soft = $this->softDelete ? "AND deleted_at IS NULL" : "";
        return $this->db->queryOne(
            "SELECT * FROM {$this->table} WHERE $column = ? $soft LIMIT 1",
            [$value]
        );
    }

    // ── Get all rows ──────────────────────────
    public function all(string $orderBy = 'id', string $dir = 'ASC'): array {
        $soft = $this->softDelete ? "WHERE deleted_at IS NULL" : "";
        $dir  = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        return $this->db->query(
            "SELECT * FROM {$this->table} $soft ORDER BY $orderBy $dir"
        );
    }

    // ── Paginate ──────────────────────────────
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $where = []): array {
        $offset = ($page - 1) * $perPage;
        $soft   = $this->softDelete ? "deleted_at IS NULL" : "1=1";

        $conditions = $soft;
        $params     = [];
        foreach ($where as $col => $val) {
            $conditions .= " AND $col = ?";
            $params[]    = $val;
        }

        $total = $this->db->queryOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE $conditions",
            $params
        )['count'] ?? 0;

        $items = $this->db->query(
            "SELECT * FROM {$this->table} WHERE $conditions ORDER BY {$this->primaryKey} DESC LIMIT ? OFFSET ?",
            [...$params, $perPage, $offset]
        );

        return [
            'data'         => $items,
            'total'        => (int) $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    // ── Insert ────────────────────────────────
    public function create(array $data): string {
        $data = $this->addTimestamps($data, 'create');
        $cols = implode(', ', array_keys($data));
        $vals = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->insert(
            "INSERT INTO {$this->table} ($cols) VALUES ($vals)",
            array_values($data)
        );
    }

    // ── Update ────────────────────────────────
    public function update(int $id, array $data): int {
        $data  = $this->addTimestamps($data, 'update');
        $sets  = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        return $this->db->execute(
            "UPDATE {$this->table} SET $sets WHERE {$this->primaryKey} = ?",
            [...array_values($data), $id]
        );
    }

    // ── Soft delete ───────────────────────────
    public function delete(int $id): int {
        if ($this->softDelete) {
            return $this->db->execute(
                "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = ?",
                [$id]
            );
        }
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    // ── Hard delete (admin only) ──────────────
    public function forceDelete(int $id): int {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    // ── Restore soft-deleted record ───────────
    public function restore(int $id): int {
        return $this->db->execute(
            "UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    // ── Count rows ────────────────────────────
    public function count(array $where = []): int {
        $soft   = $this->softDelete ? "deleted_at IS NULL" : "1=1";
        $params = [];
        foreach ($where as $col => $val) {
            $soft    .= " AND $col = ?";
            $params[] = $val;
        }
        return (int) ($this->db->queryOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE $soft",
            $params
        )['count'] ?? 0);
    }

    // ── Check existence ───────────────────────
    public function exists(string $column, mixed $value, ?int $excludeId = null): bool {
        $sql    = "SELECT COUNT(*) as count FROM {$this->table} WHERE $column = ?";
        $params = [$value];
        if ($excludeId) {
            $sql    .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }
        return (int) ($this->db->queryOne($sql, $params)['count'] ?? 0) > 0;
    }

    // ── Raw query passthrough ─────────────────
    protected function query(string $sql, array $params = []): array {
        return $this->db->query($sql, $params);
    }

    protected function queryOne(string $sql, array $params = []): array|false {
        return $this->db->queryOne($sql, $params);
    }

    protected function execute(string $sql, array $params = []): int {
        return $this->db->execute($sql, $params);
    }

    // ── Timestamps helper ─────────────────────
    private function addTimestamps(array $data, string $type): array {
        $now = date('Y-m-d H:i:s');
        if ($type === 'create') {
            $data['created_at'] = $now;
        }
        $data['updated_at'] = $now;
        return $data;
    }
}
