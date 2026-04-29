<?php
declare(strict_types=1);

class AdminController extends Controller {
    public function users(): void { $this->requireAdmin(); echo "<h1>Users coming soon!</h1>"; }
    public function updateRole(array $params): void {}
    public function toggleActive(array $params): void {}
    public function destroy(array $params): void {}
}