<?php
// ─────────────────────────────────────────────
//  core/Validator.php — Input Validation & Sanitization
//  • Sanitize all input (XSS prevention)
//  • Validation rules (required, email, min, max, etc.)
//  • Returns errors array; empty = valid
// ─────────────────────────────────────────────

declare(strict_types=1);

class Validator {

    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data) {
        // Sanitize every field on intake (XSS prevention)
        foreach ($data as $key => $value) {
            $this->data[$key] = is_string($value)
                ? self::sanitize($value)
                : $value;
        }
    }

    // ── Static factory ────────────────────────
    public static function make(array $data, array $rules): self {
        $v = new self($data);
        $v->validate($rules);
        return $v;
    }

    // ── Run rules ─────────────────────────────
    public function validate(array $rules): self {
        foreach ($rules as $field => $ruleString) {
            $value    = $this->data[$field] ?? null;
            $ruleList = explode('|', $ruleString);

            foreach ($ruleList as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
        return $this;
    }

    private function applyRule(string $field, mixed $value, string $rule): void {
        // Split rule:parameter  e.g. min:3
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);
        $label = ucfirst(str_replace('_', ' ', $field));

        match ($ruleName) {
            'required' => (empty($value) && $value !== '0')
                ? $this->addError($field, "$label is required.")
                : null,

            'email' => (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL))
                ? $this->addError($field, "$label must be a valid email address.")
                : null,

            'min' => (!empty($value) && strlen((string)$value) < (int)$param)
                ? $this->addError($field, "$label must be at least $param characters.")
                : null,

            'max' => (!empty($value) && strlen((string)$value) > (int)$param)
                ? $this->addError($field, "$label must not exceed $param characters.")
                : null,

            'numeric' => (!empty($value) && !is_numeric($value))
                ? $this->addError($field, "$label must be a number.")
                : null,

            'integer' => (!empty($value) && filter_var($value, FILTER_VALIDATE_INT) === false)
                ? $this->addError($field, "$label must be a whole number.")
                : null,

            'min_value' => (!empty($value) && (float)$value < (float)$param)
                ? $this->addError($field, "$label must be at least $param.")
                : null,

            'max_value' => (!empty($value) && (float)$value > (float)$param)
                ? $this->addError($field, "$label must not exceed $param.")
                : null,

            'confirmed' => (($this->data[$field . '_confirmation'] ?? '') !== $value)
                ? $this->addError($field, "$label confirmation does not match.")
                : null,

            'in' => (!empty($value) && !in_array($value, explode(',', $param ?? ''), true))
                ? $this->addError($field, "$label has an invalid value.")
                : null,

            'url' => (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL))
                ? $this->addError($field, "$label must be a valid URL.")
                : null,

            'image' => $this->validateImage($field, $param),

            default => null,
        };
    }

    private function validateImage(string $field, ?string $maxKb): void {
        $file = $_FILES[$field] ?? null;
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) return;

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($file['type'], $allowed, true)) {
            $this->addError($field, ucfirst($field) . ' must be a JPG, PNG, WEBP, or GIF.');
            return;
        }

        if ($maxKb && $file['size'] > (int)$maxKb * 1024) {
            $this->addError($field, ucfirst($field) . " must not exceed {$maxKb}KB.");
        }
    }

    // ── Results ───────────────────────────────
    public function passes(): bool {
        return empty($this->errors);
    }

    public function fails(): bool {
        return !$this->passes();
    }

    public function errors(): array {
        return $this->errors;
    }

    public function firstError(string $field): ?string {
        return $this->errors[$field][0] ?? null;
    }

    public function getData(): array {
        return $this->data;
    }

    public function get(string $field, mixed $default = null): mixed {
        return $this->data[$field] ?? $default;
    }

    // ── Sanitization (XSS Prevention) ─────────
    /**
     * Strip tags + encode special chars.
     * Use htmlspecialchars() in views for output escaping too.
     */
    public static function sanitize(string $value): string {
        $value = trim($value);
        $value = strip_tags($value);
        return $value;
    }

    /**
     * Safe output escaping — use in all views.
     */
    public static function escape(mixed $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // ── Private helpers ───────────────────────
    private function addError(string $field, string $message): void {
        $this->errors[$field][] = $message;
    }
}

// ── Shorthand global escape helper ───────────
// Use e($var) in views instead of htmlspecialchars() every time
function e(mixed $value): string {
    return Validator::escape($value);
}
