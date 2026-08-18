<?php


/**
 * This file is uploaded to planoo.sy server.
 * Located in the httpdocs folder.
 * handles requests made from external server
 * to send request to syriatel servers.
 * */

declare(strict_types=1);
header('Content-Type: application/json');

$headers = getallheaders();
$headers = array_change_key_case($headers, CASE_LOWER);

$webhookHeader = 'x-webhook-token';
$webhookSecret = "\$2y\$12\$MJWB8ARZ6RW0Wd6F.Ga4/OnhI/q6gCzafQO1f3E5E73J6sj8n22K.";

if (!isset($headers[$webhookHeader])) {
    http_response_code(400);
    echo json_encode(['error' => "Missing required header: 'X-Webhook-Token'."]);
    exit;
}

if (! hash_equals($webhookSecret,$headers[$webhookHeader])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized. Invalid token value.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed.']);
    exit;
}

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);
if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or empty JSON structure.']);
    exit;
}

$action = $data['action'] ?? null;
if (! $action) {
    http_response_code(422);
    echo json_encode(['error' => 'Missing action parameter.']);
    exit;
}

switch ($action) {
    case 'msgRequestForward':
        SyriatelSMSRequest($data);
        break;

    default:
        http_response_code(422);
        echo json_encode(['error' => "Unsupported action: '$action'"]);
        exit;
}

exit;

function SyriatelSMSRequest(array $data): void
{
    $targetUrl = $data['target_url'] ?? null;
    if (! $targetUrl || ! filter_var($targetUrl, FILTER_VALIDATE_URL)) {
        http_response_code(422);
        echo json_encode(['error' => 'Missing target parameter']);
        exit;
    }

    $ch = curl_init($targetUrl);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true,
        CURLOPT_HTTPHEADER => ['Accept: application/json, text/plain'],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $errorMsg = curl_error($ch);
        unset($ch);

        http_response_code(500);
        echo json_encode(['error' => 'Failed to forward request', 'details' => $errorMsg]);
        exit;
    }

    unset($ch);

    $decodedResponse = json_decode((string) $response, true);
    $smsReqId = (json_last_error() === JSON_ERROR_NONE) ? $decodedResponse : $response;

    logWebhookActivity('SUCCESS', extractInfoFromUrl($targetUrl), $httpStatusCode, $smsReqId);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'forward_status_code' => $httpStatusCode,
        'sms_req_id' => $smsReqId,
    ]);
}

/**
 * logger to record webhook transactions to a file.
 *
 * @param  string  $status  The entry type (e.g., SUCCESS, FAILED)
 * @param  string  $data  The sent data
 * @param  int  $statusCode  The HTTP status code returned by the remote server
 * @param  mixed  $responseBody  The raw string or parsed array payload from the server
 */
function logWebhookActivity(string $status, string|array $data, int $statusCode, mixed $responseBody): void
{
    $logFile = __DIR__.'/webhook_history.log';

    // Flatten array payloads to a single string line if needed
    $data = is_array($data) ? json_encode($data) : trim((string) $data);
    $responseString = is_array($responseBody) ? json_encode($responseBody) : trim((string) $responseBody);

    $logEntry = sprintf(
        "[%s] %s | Status: %d | Response: %s | Data: %s \n",
        date('Y-m-d H:i:s'),
        mb_strtoupper($status),
        $statusCode,
        $responseString,
        $data,
    );

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

function extractInfoFromUrl(string $url)
{
    $queryString = parse_url($url, PHP_URL_QUERY);

    $paramList = null;
    $to = null;

    if ($queryString) {
        parse_str($queryString, $queryParams);
        $paramList = $queryParams['param_list'] ?? null;
        $to = $queryParams['to'] ?? null;
    }

    return ['param_list' => $paramList, 'to' => $to];
}
