<?php
class Mailer {
    private static $lastError = '';

    public static function getLastError(){
        return self::$lastError;
    }

    /**
     * Resolve SMTP configuration dynamically from SiteSetting database,
     * falling back to config/mail.php constants.
     */
    public static function getConfig(){
        $host = '';
        $port = 587;
        $username = '';
        $password = '';
        $encryption = 'tls';
        $fromEmail = '';
        $fromName = defined('SITENAME') ? SITENAME : 'School ERP';
        $timeout = 15;

        // 1. Load from database via SiteSetting
        if (class_exists('SiteSetting')) {
            $host = SiteSetting::getGlobal('smtp_host', '');
            $port = (int)SiteSetting::getGlobal('smtp_port', 587);
            $username = SiteSetting::getGlobal('smtp_username', '');
            $password = SiteSetting::getGlobal('smtp_password', '');
            $encryption = strtolower(SiteSetting::getGlobal('smtp_encryption', 'tls'));
            $fromEmail = SiteSetting::getGlobal('smtp_from_email', '');
            $fromName = SiteSetting::getGlobal('smtp_from_name', '');
            $timeout = (int)SiteSetting::getGlobal('smtp_timeout', 15);
        }

        // 2. Fallback to mail.php constants
        if (empty($host) && defined('SMTP_HOST')) $host = SMTP_HOST;
        if (empty($username) && defined('SMTP_USERNAME')) $username = SMTP_USERNAME;
        if (empty($password) && defined('SMTP_PASSWORD')) $password = SMTP_PASSWORD;
        if (empty($fromEmail) && defined('SMTP_FROM_EMAIL')) $fromEmail = SMTP_FROM_EMAIL;
        if (empty($fromName)) {
            if (defined('SMTP_FROM_NAME') && !empty(SMTP_FROM_NAME)) {
                $fromName = SMTP_FROM_NAME;
            } elseif (defined('SITENAME')) {
                $fromName = SITENAME;
            }
        }
        if (defined('SMTP_PORT') && empty($port)) $port = (int)SMTP_PORT;
        if (defined('SMTP_ENCRYPTION') && empty($encryption)) $encryption = strtolower(SMTP_ENCRYPTION);
        if (defined('SMTP_TIMEOUT') && empty($timeout)) $timeout = (int)SMTP_TIMEOUT;

        if (empty($fromEmail) && !empty($username) && filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $fromEmail = $username;
        }

        // Enforce a minimum 15s timeout to prevent premature socket drops over remote WAN/TLS
        if ($timeout < 15) {
            $timeout = 15;
        }

        return [
            'host' => trim((string)$host),
            'port' => (int)($port ?: 587),
            'username' => trim((string)$username),
            'password' => trim((string)$password),
            'encryption' => $encryption ?: 'tls',
            'from_email' => trim((string)$fromEmail),
            'from_name' => trim((string)$fromName) ?: (defined('SITENAME') ? SITENAME : 'School ERP'),
            'timeout' => $timeout
        ];
    }

    public static function isConfigured(){
        $cfg = self::getConfig();
        return !empty($cfg['host']) && !empty($cfg['username']) && !empty($cfg['password']) && !empty($cfg['from_email']);
    }

    /**
     * Send email with optional branded HTML wrapper
     */
    public static function send($to, $subject, $htmlBody, $textBody = ''){
        $wrappedHtml = self::wrapHtmlTemplate($subject, $htmlBody);
        $result = self::sendRaw($to, $subject, $wrappedHtml, $textBody, false);
        return $result['success'] ?? false;
    }

    /**
     * Send email and return diagnostics with automatic port fallback
     */
    public static function sendRaw($to, $subject, $htmlBody, $textBody = '', $isTest = false, $overrideConfig = []){
        self::$lastError = '';

        $cfg = self::getConfig();
        if (!empty($overrideConfig)) {
            $cfg = array_merge($cfg, array_filter($overrideConfig));
        }

        if (empty($cfg['host']) || empty($cfg['username']) || empty($cfg['password'])) {
            self::$lastError = 'SMTP is not configured. Please enter your SMTP Username (e.g. your Gmail address) and Password.';
            return ['success' => false, 'message' => self::$lastError];
        }

        if (empty($cfg['from_email'])) {
            $cfg['from_email'] = $cfg['username'];
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::$lastError = "Invalid recipient email address: '{$to}'";
            return ['success' => false, 'message' => self::$lastError];
        }

        // Attempt 1: Using primary configured settings
        $result = self::executeSmtpTransaction($cfg, $to, $subject, $htmlBody, $textBody);
        if ($result['success']) {
            return $result;
        }

        // Attempt 2: If primary failed and host is Gmail, auto-try alternating port (587 TLS <-> 465 SSL)
        $primaryError = $result['message'];
        if (stripos($cfg['host'], 'gmail.com') !== false) {
            $fallbackCfg = $cfg;
            if ($cfg['port'] == 587) {
                $fallbackCfg['port'] = 465;
                $fallbackCfg['encryption'] = 'ssl';
            } elseif ($cfg['port'] == 465) {
                $fallbackCfg['port'] = 587;
                $fallbackCfg['encryption'] = 'tls';
            }

            if ($fallbackCfg['port'] !== $cfg['port']) {
                $fallbackResult = self::executeSmtpTransaction($fallbackCfg, $to, $subject, $htmlBody, $textBody);
                if ($fallbackResult['success']) {
                    // Auto-sync working port & encryption to database so subsequent emails don't incur retry lag
                    if (class_exists('SiteSetting')) {
                        try {
                            $settingModel = new SiteSetting();
                            $settingModel->updateSetting('smtp_port', (string)$fallbackCfg['port']);
                            $settingModel->updateSetting('smtp_encryption', $fallbackCfg['encryption']);
                            SiteSetting::clearCache();
                        } catch (Throwable $ignore) {}
                    }
                    return [
                        'success' => true,
                        'message' => "Email sent successfully to {$to} (auto-switched to Port {$fallbackCfg['port']} " . strtoupper($fallbackCfg['encryption']) . ")."
                    ];
                }
            }
        }

        self::$lastError = $primaryError;
        return ['success' => false, 'message' => self::$lastError];
    }

    /**
     * Executes the low-level SMTP RFC 5321 transaction
     */
    private static function executeSmtpTransaction($cfg, $to, $subject, $htmlBody, $textBody = ''){
        $host = $cfg['host'];
        $port = (int)$cfg['port'];
        $encryption = strtolower($cfg['encryption']);
        $timeout = max(15, (int)$cfg['timeout']);

        // Clean app passwords (remove spaces, tabs, dashes: "abcd efgh ijkl mnop" -> "abcdefghijklmnop")
        $cleanPassword = str_replace([' ', '-', "\t"], '', $cfg['password']);

        // Set SSL context options to avoid certificate chain validation stalls on local environments
        $contextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        $context = stream_context_create($contextOptions);

        $remote = ($encryption === 'ssl' || $port == 465)
            ? 'ssl://' . $host . ':' . $port
            : 'tcp://' . $host . ':' . $port;

        $errno = 0;
        $error = '';
        $socket = @stream_socket_client($remote, $errno, $error, $timeout, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            $hint = ($port == 587) ? ' (Port 587 may be blocked by your ISP/firewall; try Port 465 with SSL)' : '';
            $msg = "Could not connect to SMTP host {$host}:{$port} - {$error} (code {$errno}){$hint}";
            error_log('SMTP connection failed: ' . $msg);
            return ['success' => false, 'message' => $msg];
        }

        stream_set_timeout($socket, $timeout);

        try {
            self::expect($socket, 220);
            $clientHost = self::getClientHost();
            self::command($socket, 'EHLO ' . $clientHost, 250);

            if ($encryption === 'tls' || $port == 587) {
                self::command($socket, 'STARTTLS', 220);
                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }
                stream_set_blocking($socket, true);
                if (!stream_socket_enable_crypto($socket, true, $cryptoMethod)) {
                    throw new RuntimeException('Could not enable SMTP TLS encryption on port ' . $port . '.');
                }
                self::command($socket, 'EHLO ' . $clientHost, 250);
            }

            self::command($socket, 'AUTH LOGIN', 334);
            self::command($socket, base64_encode($cfg['username']), 334);
            self::command($socket, base64_encode($cleanPassword), 235);
            self::command($socket, 'MAIL FROM:<' . $cfg['from_email'] . '>', 250);
            self::command($socket, 'RCPT TO:<' . $to . '>', 250);
            self::command($socket, 'DATA', 354);

            $encodedSubject = function_exists('mb_encode_mimeheader')
                ? mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n")
                : '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $fromName = function_exists('mb_encode_mimeheader')
                ? mb_encode_mimeheader($cfg['from_name'], 'UTF-8', 'B', "\r\n")
                : $cfg['from_name'];

            $boundary = '=_school_' . bin2hex(random_bytes(8));
            $textBody = $textBody !== '' ? $textBody : strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], "\n", $htmlBody));

            $headers = [
                'Date: ' . date('r'),
                'From: ' . $fromName . ' <' . $cfg['from_email'] . '>',
                'To: <' . $to . '>',
                'Subject: ' . $encodedSubject,
                'MIME-Version: 1.0',
                'Content-Type: multipart/alternative; boundary="' . $boundary . '"'
            ];

            // Build MIME message body with explicit CRLF delimiters
            $message = implode("\r\n", $headers) . "\r\n\r\n";
            $message .= '--' . $boundary . "\r\n";
            $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $message .= $textBody . "\r\n\r\n";
            $message .= '--' . $boundary . "\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $message .= $htmlBody . "\r\n\r\n";
            $message .= '--' . $boundary . "--\r\n";

            // Normalize all line endings to CRLF
            $message = str_replace(["\r\n", "\r"], "\n", $message);
            $message = str_replace("\n", "\r\n", $message);

            // RFC 5321 Section 4.5.2 Dot-stuffing: Prepend '.' to any line starting with '.'
            $lines = explode("\r\n", $message);
            foreach ($lines as &$line) {
                if (isset($line[0]) && $line[0] === '.') {
                    $line = '.' . $line;
                }
            }
            unset($line);
            $cleanMessage = implode("\r\n", $lines);

            // Ensure message body ends cleanly with \r\n before transmitting final RFC end-of-data marker: ".\r\n"
            if (substr($cleanMessage, -2) !== "\r\n") {
                $cleanMessage .= "\r\n";
            }

            // Transmit message data followed strictly by <CRLF>.<CRLF>
            fwrite($socket, $cleanMessage . ".\r\n");
            self::expect($socket, 250);
            self::command($socket, 'QUIT', 221);
            @fclose($socket);

            return ['success' => true, 'message' => "Email sent successfully to {$to}."];
        } catch (Throwable $exception) {
            $msg = $exception->getMessage();
            if (strpos($msg, '535') !== false || strpos($msg, 'BadCredentials') !== false) {
                $msg = 'Google Login Rejected (535 Bad Credentials). For Gmail, 2-Step Verification must be ON and you must generate and use a 16-character Google App Password (not your regular account password).';
            } elseif (strpos($msg, 'Could not connect') !== false || strpos($msg, 'Connection refused') !== false || strpos($msg, 'timed out') !== false) {
                $msg .= '. Port ' . $port . ' may be blocked by your network or ISP. Try switching to Port 465 with SSL encryption.';
            }
            error_log('SMTP transaction failed: ' . $msg);
            if (is_resource($socket)) {
                @fclose($socket);
            }
            return ['success' => false, 'message' => $msg];
        }
    }

    /**
     * Send test email to verify credentials
     */
    public static function testConnection($testTo = null, $overrideConfig = []){
        $cfg = self::getConfig();
        if (!empty($overrideConfig)) {
            $cfg = array_merge($cfg, array_filter($overrideConfig));
        }

        $target = !empty($testTo) ? trim($testTo) : $cfg['from_email'];
        if (empty($target)) {
            $target = $cfg['username'];
        }

        $subject = 'SMTP Connection Test: ' . $cfg['from_name'];
        $body = '
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 18px; margin-bottom: 20px;">
                <h3 style="color: #065f46; margin: 0 0 8px 0; font-size: 1.15rem;">
                    &#10004; SMTP Verification Successful!
                </h3>
                <p style="color: #047857; margin: 0; font-size: 0.9rem;">
                    Your school management system outgoing email server has been connected and authenticated successfully.
                </p>
            </div>
            <table style="width: 100%; font-size: 0.88rem; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b; width: 140px;"><strong>SMTP Server:</strong></td>
                    <td style="padding: 8px 0; color: #0f172a;">' . htmlspecialchars($cfg['host']) . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;"><strong>Port &amp; Security:</strong></td>
                    <td style="padding: 8px 0; color: #0f172a;">' . (int)$cfg['port'] . ' (' . strtoupper($cfg['encryption']) . ')</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;"><strong>Authenticated As:</strong></td>
                    <td style="padding: 8px 0; color: #0f172a;">' . htmlspecialchars($cfg['username']) . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;"><strong>Sender Name:</strong></td>
                    <td style="padding: 8px 0; color: #0f172a;">' . htmlspecialchars($cfg['from_name']) . ' &lt;' . htmlspecialchars($cfg['from_email']) . '&gt;</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;"><strong>Timestamp:</strong></td>
                    <td style="padding: 8px 0; color: #0f172a;">' . date('d M Y, h:i:s A') . ' (PKT)</td>
                </tr>
            </table>
            <p style="font-size: 0.85rem; color: #64748b; line-height: 1.5; margin: 0;">
                All system notifications (Password Reset PINs, Staff Credentials, Student Admissions, Fee Alerts) will now be delivered via this outgoing email service.
            </p>
        ';

        return self::sendRaw($target, $subject, self::wrapHtmlTemplate('SMTP Test Successful', $body), 'SMTP verification successful.', true, $overrideConfig);
    }

    /**
     * Professional responsive HTML email card wrapper
     */
    public static function wrapHtmlTemplate($title, $contentHtml){
        $cfg = self::getConfig();
        $schoolName = !empty($cfg['from_name']) ? $cfg['from_name'] : (defined('SITENAME') ? SITENAME : 'School ERP');
        $siteUrl = defined('URLROOT') ? URLROOT : 'http://localhost/school';

        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed; background-color: #f1f5f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);">
                    <!-- Brand Banner Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0b2e73 0%, #1769e0 100%); padding: 32px 36px; text-align: left;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 1.4rem; font-weight: 700; letter-spacing: -0.3px;">
                                ' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '
                            </h1>
                            <p style="color: rgba(255, 255, 255, 0.78); margin: 6px 0 0 0; font-size: 0.85rem;">
                                Official Campus Portal Notification
                            </p>
                        </td>
                    </tr>
                    <!-- Main Body -->
                    <tr>
                        <td style="padding: 36px;">
                            ' . $contentHtml . '
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 22px 36px; text-align: center; font-size: 0.78rem; color: #64748b;">
                            <p style="margin: 0 0 6px 0;">
                                &copy; ' . date('Y') . ' ' . htmlspecialchars($schoolName, ENT_QUOTES, 'UTF-8') . '. All rights reserved.
                            </p>
                            <p style="margin: 0;">
                                <a href="' . htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8') . '" style="color: #1769e0; text-decoration: none; font-weight: 600;">Visit School Portal</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    private static function getClientHost(){
        $host = !empty($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost'
            ? $_SERVER['SERVER_NAME']
            : (gethostname() ?: '127.0.0.1');
        $clean = preg_replace('/[^a-zA-Z0-9\.\-]/', '', $host);
        return !empty($clean) ? $clean : '127.0.0.1';
    }

    private static function command($socket, $command, $expectedCode){
        fwrite($socket, $command . "\r\n");
        return self::expect($socket, $expectedCode);
    }

    private static function expect($socket, $expectedCode){
        $response = '';
        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;
            // In RFC 5321, continuation lines have '-' as 4th char (e.g., '250-').
            // The final reply line has ' ' or newline after the 3-digit code.
            if (strlen($line) >= 4) {
                if ($line[3] !== '-') {
                    break;
                }
            } elseif (strlen($line) >= 3) {
                break;
            }
        }

        if ($response === '') {
            $meta = stream_get_meta_data($socket);
            if (!empty($meta['timed_out'])) {
                throw new RuntimeException("SMTP connection timed out waiting for server response (expected code {$expectedCode}).");
            }
            if (feof($socket)) {
                throw new RuntimeException("SMTP server closed the connection unexpectedly (expected code {$expectedCode}). Check firewall, network, or Gmail App Password.");
            }
            throw new RuntimeException("Empty response received from SMTP server (expected code {$expectedCode}).");
        }

        $code = (int)substr($response, 0, 3);
        if ($code !== $expectedCode) {
            $cleaned = trim(preg_replace('/\s+/', ' ', $response));
            throw new RuntimeException("SMTP expected {$expectedCode}, received {$code} ({$cleaned}).");
        }
        return $response;
    }
}
