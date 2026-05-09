<?php
// ─────────────────────────────────────────────
//  app/controllers/PsgcController.php
//  Server-side proxy for the PSGC API.
//  Avoids CSP connect-src issues by fetching
//  from PHP instead of the browser.
// ─────────────────────────────────────────────

declare(strict_types=1);

class PsgcController extends Controller {

    private const BASE    = 'https://psgc.gitlab.io/api';
    private const TIMEOUT = 10;

    // ── GET /api/psgc/municipalities?province=037700000 ───
    public function municipalities(): void {
        $provinceCode = preg_replace('/[^0-9]/', '', $this->get('province', ''));

        if (!$provinceCode) {
            $this->jsonError('Province code required.', 400);
            return;
        }

        $data = $this->fetch(self::BASE . "/provinces/{$provinceCode}/municipalities/");

        if ($data === null) {
            $this->jsonError('Failed to fetch municipalities.', 502);
            return;
        }

        // Return only what the frontend needs
        $simplified = array_map(fn($m) => [
            'code' => $m['code'],
            'name' => $m['name'],
        ], $data);

        usort($simplified, fn($a, $b) => strcmp($a['name'], $b['name']));

        header('Content-Type: application/json');
        echo json_encode($simplified);
    }

    // ── GET /api/psgc/barangays?municipality=037701000 ────
    public function barangays(): void {
        $muniCode = preg_replace('/[^0-9]/', '', $this->get('municipality', ''));

        if (!$muniCode) {
            $this->jsonError('Municipality code required.', 400);
            return;
        }

        $data = $this->fetch(self::BASE . "/municipalities/{$muniCode}/barangays/");

        if ($data === null) {
            $this->jsonError('Failed to fetch barangays.', 502);
            return;
        }

        $simplified = array_map(fn($b) => [
            'code' => $b['code'],
            'name' => $b['name'],
        ], $data);

        usort($simplified, fn($a, $b) => strcmp($a['name'], $b['name']));

        header('Content-Type: application/json');
        echo json_encode($simplified);
    }

    // ── cURL helper ───────────────────────────
    private function fetch(string $url): ?array {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => self::TIMEOUT,
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }
}