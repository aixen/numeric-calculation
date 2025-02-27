<?php
function executeCommand($input) {
    $parts = explode(' ', trim($input));
    $cmd = array_shift($parts);

    if ($cmd === 'repo-desc' && count($parts) === 1) {
        return getRepoDescription($parts[0]);
    }

    $numbers = array_map('floatval', $parts);
    asort($numbers);

    switch ($cmd) {
        case 'sum':
            return array_sum($numbers);
        case 'difference':
            return array_reduce($numbers, fn($a, $b) => $a - $b);
        case 'product':
            return array_product($numbers);
        case 'quotient':
            return array_reduce($numbers, fn($a, $b) => $b ? $a / $b : 'undefined');
        default:
            return 'Unknown command';
    }
}

function getRepoDescription($repo) {
    $url = "https://api.github.com/repos/$repo";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return "CURL Error: $error_msg";
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        return [
            'isText' => true,
            'description' => "Repository Not Found Code: $httpCode.",
        ];
    }

    $data = json_decode($response, true);
    return [
        'isText' => true,
        'description' => $data['description'] ?? "No Description.",
    ];
}

