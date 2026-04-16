<?php
// admin/save.php — uloží obsah content.json
// Volá se POST požadavkem s parametrem "data" (JSON string)

header('Content-Type: application/json; charset=utf-8');

// Jednoduchá ochrana: přijímáme pouze POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metoda není povolena']);
    exit;
}

$raw = $_POST['data'] ?? '';
if (!$raw) {
    echo json_encode(['ok' => false, 'error' => 'Chybí data']);
    exit;
}

// Ověříme, že je to validní JSON
$decoded = json_decode($raw);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['ok' => false, 'error' => 'Neplatný JSON: ' . json_last_error_msg()]);
    exit;
}

// Cesta k content.json (o úroveň výš od složky admin/)
$target = dirname(__DIR__) . '/content.json';

// Záloha předchozí verze (přepíše se vždy jen jedna záloha)
if (file_exists($target)) {
    copy($target, $target . '.bak');
}

// Uložíme hezky naformátovaný JSON
$written = file_put_contents(
    $target,
    json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
);

if ($written === false) {
    echo json_encode(['ok' => false, 'error' => 'Nelze zapsat soubor. Zkontrolujte oprávnění složky.']);
    exit;
}

echo json_encode(['ok' => true]);
