<?php
// ─────────────────────────────────────────────
//  core/Paginator.php — Pagination Helper
// ─────────────────────────────────────────────

declare(strict_types=1);

class Paginator {

    private int $total;
    private int $perPage;
    private int $currentPage;
    private int $lastPage;
    private string $baseUrl;

    public function __construct(int $total, int $perPage, int $currentPage, string $baseUrl = '') {
        $this->total       = $total;
        $this->perPage     = $perPage;
        $this->currentPage = max(1, $currentPage);
        $this->lastPage    = max(1, (int) ceil($total / $perPage));
        $this->baseUrl     = $baseUrl ?: strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    }

    public function hasPages(): bool {
        return $this->lastPage > 1;
    }

    public function hasPrev(): bool {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool {
        return $this->currentPage < $this->lastPage;
    }

    public function prevUrl(): string {
        return $this->pageUrl($this->currentPage - 1);
    }

    public function nextUrl(): string {
        return $this->pageUrl($this->currentPage + 1);
    }

    public function pageUrl(int $page): string {
        $params = $_GET;
        $params['page'] = $page;
        return $this->baseUrl . '?' . http_build_query($params);
    }

    // ── Render Tailwind pagination links ──────
    public function render(): string {
        if (!$this->hasPages()) return '';

        $html  = '<nav class="flex items-center justify-between mt-6">';
        $html .= '<p class="text-sm text-gray-600">Showing page ' . $this->currentPage . ' of ' . $this->lastPage . ' (' . $this->total . ' total)</p>';
        $html .= '<div class="flex gap-1">';

        // Prev
        if ($this->hasPrev()) {
            $html .= '<a href="' . e($this->prevUrl()) . '" class="px-3 py-1 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">&laquo; Prev</a>';
        }

        // Page numbers
        $range = $this->getPageRange();
        foreach ($range as $page) {
            if ($page === '...') {
                $html .= '<span class="px-3 py-1 text-sm text-gray-400">…</span>';
            } elseif ($page === $this->currentPage) {
                $html .= '<span class="px-3 py-1 text-sm rounded-lg bg-pink-600 text-white font-medium">' . $page . '</span>';
            } else {
                $html .= '<a href="' . e($this->pageUrl((int)$page)) . '" class="px-3 py-1 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">' . $page . '</a>';
            }
        }

        // Next
        if ($this->hasNext()) {
            $html .= '<a href="' . e($this->nextUrl()) . '" class="px-3 py-1 text-sm rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">Next &raquo;</a>';
        }

        $html .= '</div></nav>';
        return $html;
    }

    // ── Generate page range with ellipsis ─────
    private function getPageRange(): array {
        $pages = [];
        $delta = 2;
        $left  = $this->currentPage - $delta;
        $right = $this->currentPage + $delta;

        for ($i = 1; $i <= $this->lastPage; $i++) {
            if ($i === 1 || $i === $this->lastPage || ($i >= $left && $i <= $right)) {
                $pages[] = $i;
            }
        }

        $result = [];
        $prev   = null;
        foreach ($pages as $page) {
            if ($prev && $page - $prev > 1) {
                $result[] = '...';
            }
            $result[] = $page;
            $prev = $page;
        }
        return $result;
    }

    // ── Getters ───────────────────────────────
    public function total(): int        { return $this->total; }
    public function perPage(): int      { return $this->perPage; }
    public function currentPage(): int  { return $this->currentPage; }
    public function lastPage(): int     { return $this->lastPage; }
}
