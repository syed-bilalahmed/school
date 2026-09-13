<?php
/**
 * WhatsApp Agent Platform Service
 * ─────────────────────────────────────────────────────────────────────────────
 * Connects the school website Live Chat to a WhatsApp Third-Party Agent.
 *
 * When a visitor sends a message on the website, this service forwards it to
 * the WhatsApp Agent Platform API and returns the AI reply.
 *
 * SETUP:
 *   1. Go to Settings → WhatsApp & Live Chat tab
 *   2. Paste your API Key in "WhatsApp Agent API Key" field
 *   3. Save — the Live Chat widget will automatically use AI replies!
 *
 * API Reference:
 *   https://www.whatsapp.com/developer/WhatsApp-Agent-Platform-Developer-Manual.pdf
 * ─────────────────────────────────────────────────────────────────────────────
 */
class WhatsAppAgentService
{
    /** WhatsApp Agent Platform base URL */
    private const API_BASE = 'https://agent.whatsapp.com/api/v1';

    private string $apiKey;
    private string $agentId;
    private int    $timeout;

    public function __construct(string $apiKey, string $agentId = '', int $timeout = 10)
    {
        $this->apiKey  = $apiKey;
        $this->agentId = $agentId;
        $this->timeout = $timeout;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PUBLIC METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a user message to the WhatsApp Agent and get a reply.
     *
     * @param  string $message    The user's message text
     * @param  string $sessionId  Unique session/conversation identifier
     * @param  array  $context    Optional extra context (name, phone, etc.)
     * @return array  ['success'=>bool, 'reply'=>string, 'quick_replies'=>array]
     */
    public function sendMessage(string $message, string $sessionId = '', array $context = []): array
    {
        if (empty($this->apiKey)) {
            return $this->errorResponse('API key not configured.');
        }

        $payload = [
            'message'    => $message,
            'session_id' => $sessionId ?: $this->generateSessionId(),
            'context'    => $context,
        ];

        if (!empty($this->agentId)) {
            $payload['agent_id'] = $this->agentId;
        }

        $result = $this->post('/chat/message', $payload);

        if (!$result['success']) {
            return $this->errorResponse($result['error'] ?? 'API request failed.');
        }

        $data = $result['data'];

        return [
            'success'       => true,
            'reply'         => $data['reply'] ?? $data['message'] ?? $data['text'] ?? 'No reply received.',
            'quick_replies' => $data['quick_replies'] ?? $data['suggestions'] ?? [],
            'session_id'    => $data['session_id'] ?? $sessionId,
            'raw'           => $data,
        ];
    }

    /**
     * Test API connectivity — call this to verify the key is valid.
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'message' => 'API key is empty.'];
        }

        $result = $this->get('/ping');

        if ($result['success']) {
            return ['success' => true,  'message' => 'WhatsApp Agent API connected successfully!'];
        }

        return [
            'success' => false,
            'message' => 'Connection failed: ' . ($result['error'] ?? 'Unknown error'),
        ];
    }

    /**
     * Register webhook URL with WhatsApp Agent Platform.
     */
    public function registerWebhook(string $webhookUrl): array
    {
        $result = $this->post('/webhook/register', [
            'url'    => $webhookUrl,
            'events' => ['message.received', 'message.sent'],
        ]);

        if ($result['success']) {
            return ['success' => true, 'message' => 'Webhook registered: ' . $webhookUrl];
        }

        return ['success' => false, 'message' => 'Webhook registration failed: ' . ($result['error'] ?? '')];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STATIC HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build an instance from SiteSettings (reads API key from DB automatically).
     * Returns null if no API key is configured.
     */
    public static function fromSettings(): ?self
    {
        if (!class_exists('SiteSetting')) return null;

        $s      = SiteSetting::getGlobalSettings();
        $apiKey = trim($s['wa_agent_api_key'] ?? '');

        if (empty($apiKey)) return null;

        return new self(
            $apiKey,
            trim($s['wa_agent_id'] ?? ''),
            (int) ($s['wa_agent_timeout'] ?? 10)
        );
    }

    /**
     * Check if the WhatsApp Agent is configured (API key saved in settings).
     */
    public static function isConfigured(): bool
    {
        if (!class_exists('SiteSetting')) return false;
        $s = SiteSetting::getGlobalSettings();
        return !empty(trim($s['wa_agent_api_key'] ?? ''));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HTTP HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function post(string $endpoint, array $payload): array
    {
        return $this->request('POST', $endpoint, $payload);
    }

    private function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    private function request(string $method, string $endpoint, array $payload = []): array
    {
        $url = self::API_BASE . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Agent-Platform: SchoolMS/1.0',
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return ['success' => false, 'error' => 'cURL error: ' . $curlError];
        }

        if ($httpCode === 401 || $httpCode === 403) {
            return ['success' => false, 'error' => 'Invalid or expired API key (HTTP ' . $httpCode . ')'];
        }

        $decoded = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300 && $decoded) {
            return ['success' => true, 'data' => $decoded];
        }

        $errMsg = $decoded['error'] ?? $decoded['message'] ?? ('HTTP ' . $httpCode);
        return ['success' => false, 'error' => $errMsg];
    }

    private function generateSessionId(): string
    {
        return 'sess_' . md5(session_id() ?: uniqid('wa_', true));
    }

    private function errorResponse(string $message): array
    {
        return [
            'success'       => false,
            'reply'         => null,
            'quick_replies' => [],
            'error'         => $message,
        ];
    }
}
