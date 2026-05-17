<?php
// ─────────────────────────────────────────────
// app/controllers/ChatbotController.php
// ─────────────────────────────────────────────
declare(strict_types=1);

class ChatbotController extends Controller
{
    private string $apiKey;
    private string $apiUrl  = 'https://integrate.api.nvidia.com/v1/chat/completions';
    private string $model   = 'meta/llama-3.3-70b-instruct';

    public function __construct()
    {
        $this->apiKey = defined('NVIDIA_API_KEY') ? NVIDIA_API_KEY : '';
    }

    // ── POST /chatbot/message ──────────────────
    public function message(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $input   = json_decode(file_get_contents('php://input'), true);
        $userMsg = trim($input['message'] ?? '');
        $history = array_slice($input['history'] ?? [], -6);

        if ($userMsg === '') {
            echo json_encode(['error' => 'Empty message']);
            return;
        }

        if ($this->apiKey === '') {
            echo json_encode(['error' => 'NVIDIA API key not configured']);
            return;
        }

        echo json_encode($this->callNvidia($userMsg, $history));
    }

    // ── POST /chatbot/products ─────────────────
    public function products(): void
    {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $names = $input['names'] ?? [];

            $productModel = new Product();
            $products     = $productModel->getAll([], 100, 0);

            if (!empty($names)) {
                $products = array_filter($products, function ($p) use ($names) {
                    foreach ($names as $name) {
                        if (stripos($p['name'], trim($name)) !== false) {
                            return true;
                        }
                    }
                    return false;
                });
            }

            $formatted = array_map(function ($p) {
                return [
                    'id'       => $p['id'],
                    'name'     => $p['name'],
                    'price'    => '₱' . number_format((float) $p['price'], 2),
                    'raw_price'=> (float) $p['price'],
                    'stock'    => (int) $p['stock'],
                    'category' => $p['category_name'] ?? 'Uncategorized',
                    'image'    => !empty($p['image'])
                        ? APP_URL . '/images/products/' . $p['image']
                        : null,
                    'url'      => APP_URL . '/shop/' . $p['slug'],
                    'in_stock' => (int) $p['stock'] > 0,
                ];
            }, $products);

            echo json_encode(['products' => array_values($formatted)]);
        } catch (\Throwable $e) {
            echo json_encode(['products' => []]);
        }
    }

    // ── POST /chatbot/orders ───────────────────
    public function orders(): void
    {
        header('Content-Type: application/json');

        if (!Session::isLoggedIn()) {
            echo json_encode(['error' => 'not_logged_in']);
            return;
        }

        try {
            $db     = Database::getInstance();
            $userId = (int) Session::userId();

            $input       = json_decode(file_get_contents('php://input'), true);
            $message     = $input['message'] ?? '';
            $orderNumber = null;

            if (preg_match('/PS-[A-Z0-9]+-\d+/i', $message, $matches)) {
                $orderNumber = strtoupper($matches[0]);
            }

            if ($orderNumber) {
                $orders = $db->query(
                    "SELECT o.order_number, o.status, o.total_amount, o.created_at,
                            COUNT(oi.id) AS item_count
                     FROM orders o
                     LEFT JOIN order_items oi ON oi.order_id = o.id
                     WHERE o.user_id = ? AND o.order_number = ?
                     GROUP BY o.id
                     LIMIT 1",
                    [$userId, $orderNumber]
                );
            } else {
                $orders = $db->query(
                    "SELECT o.order_number, o.status, o.total_amount, o.created_at,
                            COUNT(oi.id) AS item_count
                     FROM orders o
                     LEFT JOIN order_items oi ON oi.order_id = o.id
                     WHERE o.user_id = ?
                     GROUP BY o.id
                     ORDER BY o.created_at DESC
                     LIMIT 5",
                    [$userId]
                );
            }

            $formatted = array_map(function ($o) {
                return [
                    'order_number' => $o['order_number'],
                    'status'       => ucfirst($o['status']),
                    'total'        => '₱' . number_format((float) $o['total_amount'], 2),
                    'items'        => (int) $o['item_count'],
                    'date'         => date('M d, Y', strtotime($o['created_at'])),
                ];
            }, $orders);

            echo json_encode(['orders' => $formatted]);
        } catch (\Throwable $e) {
            echo json_encode(['orders' => []]);
        }
    }

    // ── NVIDIA API Call ────────────────────────
    private function callNvidia(string $userMessage, array $history): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->getSystemPrompt()],
        ];

        foreach ($history as $turn) {
            if (isset($turn['role'], $turn['text'])) {
                $messages[] = [
                    'role'    => $turn['role'] === 'user' ? 'user' : 'assistant',
                    'content' => $turn['text'],
                ];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.4,
            'max_tokens'  => 700,
            'top_p'       => 0.95,
            'stream'      => false,
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $raw      = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return ['reply' => "Sorry po, I'm having trouble connecting. Please try again! 🌸"];
        }

        if ($httpCode === 429) {
            return [
                'reply' => "Medyo maraming nagtatanong ngayon po! 🌸 Please wait a moment and try again.",
            ];
        }

        $data = json_decode($raw, true);

        if (isset($data['choices'][0]['message']['content'])) {
            $reply = trim($data['choices'][0]['message']['content']);

            if (str_contains($reply, '[SHOW_PRODUCTS]')) {
                return ['action' => 'show_products'];
            }

            if (preg_match('/\[SHOW_PRODUCTS:([^\]]+)\]/', $reply, $m)) {
                $names      = array_map('trim', explode(',', $m[1]));
                $cleanReply = trim(preg_replace('/\[SHOW_PRODUCTS:[^\]]+\]/', '', $reply));
                return [
                    'action' => 'show_recommended',
                    'names'  => $names,
                    'reply'  => $cleanReply,
                ];
            }

            if (str_contains($reply, '[SHOW_ORDERS]')) {
                return ['action' => 'show_orders'];
            }

            return [
                'reply' => $reply,
                'ended' => $this->detectConversationEnd($reply),
            ];
        }

        return ['reply' => "I'm not sure about that po! For the best help, you can message us directly or visit our store in Baler, Aurora. 🌸"];
    }

    // ── Detect profanity-triggered end ────────
    private function detectConversationEnd(string $reply): bool
    {
        $endPhrases = [
            'unable to continue this conversation',
            'keep our chat respectful',
            'cannot continue this conversation',
            'end this conversation',
        ];

        $lower = strtolower($reply);
        foreach ($endPhrases as $phrase) {
            if (str_contains($lower, $phrase)) {
                return true;
            }
        }

        return false;
    }

    // ── Build live product list for prompt ────
    private function getProductList(): string
    {
        $cacheKey = 'chatbot_products_' . date('Hi');

        if (isset($_SESSION[$cacheKey])) {
            return $_SESSION[$cacheKey];
        }

        try {
            $productModel = new Product();
            $products     = $productModel->getAll([], 100, 0);

            if (empty($products)) {
                return "No products are currently available.";
            }

            $grouped = [];
            foreach ($products as $p) {
                $cat = $p['category_name'] ?? 'Uncategorized';
                $grouped[$cat][] = $p;
            }

            $lines = [];
            foreach ($grouped as $category => $items) {
                $lines[] = "\n[{$category}]";
                foreach ($items as $p) {
                    $stock = (int) $p['stock'];
                    if ($stock <= 0) continue;
                    $price   = '₱' . number_format((float) $p['price'], 2);
                    $lines[] = "  • {$p['name']} | {$price} | In Stock ({$stock} available)";
                }
            }

            $result = implode("\n", $lines) ?: "No products currently in stock.";
            $_SESSION[$cacheKey] = $result;

            return $result;

        } catch (\Throwable $e) {
            return "Product list temporarily unavailable.";
        }
    }

    // ── System Prompt ─────────────────────────
    private function getSystemPrompt(): string
    {
        $productList = $this->getProductList();

        $customerContext = '';
        if (Session::isLoggedIn()) {
            $user      = Session::user();
            $firstName = trim($user['first_name'] ?? '');
            $lastName  = trim($user['last_name'] ?? '');
            $fullName  = trim("{$firstName} {$lastName}");
            if ($fullName !== '') {
                $customerContext = "\nCURRENT CUSTOMER: {$fullName} (logged in) — address them by first name occasionally.";
            }
        }

        return <<<PROMPT
You are a friendly and helpful customer service assistant for "Petal and Soul", a flower and bouquet shop based in Baler, Aurora, Philippines.

Your personality:
- Warm, cheerful, and welcoming — like a flower shop attendant
- Keep responses short and helpful (2–4 sentences max unless listing items)
- Use light, friendly Taglish language. Occasionally use "po", "ate", or "kuya" for a Filipino touch
- Use flower/nature emojis occasionally 🌸🌺💐
{$customerContext}

STORE INFORMATION:
- Store name: Petal and Soul
- Location: Baler, Aurora, Philippines
- Products: Fresh flowers, bouquets, and floral arrangements
- Payment methods: Cash on Delivery (COD) and Online payment

AVAILABLE PRODUCTS (live from our store — in stock only):
{$productList}

DELIVERY POLICY:
- Same-day delivery within Baler, Aurora only
- Orders must be placed before 12:00 PM noon for same-day delivery
- No delivery outside Baler, Aurora at this time
- Delivery fee may apply depending on distance within Baler

RETURN & REFUND POLICY:
- We do not accept returns due to the perishable nature of flowers
- If your order arrives damaged or incorrect, contact us within 24 hours with a photo
- We will offer a replacement or refund depending on the situation

WHAT YOU CAN HELP WITH:
1. Answer questions about flowers and bouquets
2. Help customers check their recent orders
3. Explain delivery coverage and schedule
4. Explain return/refund policy
5. Recommend bouquets for occasions (birthdays, anniversaries, funerals, graduations, weddings, etc.)
6. Explain payment methods

SPECIAL COMMANDS — use ONLY in these exact situations:

[SHOW_PRODUCTS]
→ ONLY when customer explicitly wants to browse/see all products
→ Triggers: "show me your flowers", "what do you have", "let me see your bouquets", "browse products"
→ Do NOT use for recommendations

[SHOW_ORDERS]
→ ONLY when customer clearly asks about their order status/history OR provides an order number
→ Triggers: "where is my order", "check my orders", "track my order", "what is the status of PS-XXXXXX-XXXXXXXX"
→ NEVER use for: greetings, hellos, recommendations, general questions, or anything not explicitly about orders
→ "hello", "hi", "kumusta", "good morning" → these are greetings, reply warmly with NO commands

[SHOW_PRODUCTS:Name1,Name2]
→ ONLY when making a specific product recommendation
→ Append at the end of your reply text
→ Example: "For your anniversary, I suggest our **Rose Bouquet** 🌹 It's perfect! [SHOW_PRODUCTS:Rose Bouquet]"

RECOMMENDATION RULES:
- Suggest specific products from AVAILABLE PRODUCTS above
- Always mention the product name and price in your text
- Never recommend out of stock products
- Match the occasion to the best flower type (e.g. white flowers for weddings, bright colors for birthdays)
- Always append [SHOW_PRODUCTS:name1,name2] after any recommendation so cards are shown

IMPORTANT RULES:
- Only answer questions related to Petal and Soul and its products/services
- Never make up products or prices — only use what is listed in AVAILABLE PRODUCTS above
- If a customer seems upset, be empathetic and offer to connect them with the store team
- Do not discuss topics unrelated to the store (politics, other businesses, etc.)
- If a customer uses profanity, swear words, or offensive language in any language (English, Tagalog, or others), end the conversation immediately with exactly: "I'm sorry po, but I'm unable to continue this conversation. Please keep our chat respectful. 🌸"

If you don't know the answer, say: "I'm not sure about that po! For the best help, you can message us directly or visit our store in Baler, Aurora. 🌸"
PROMPT;
    }
}