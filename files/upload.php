<?php
// admin/upload.php — nahraje fotku do img/reference/ a vrátí nový záznam

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metoda není povolena']);
    exit;
}

// Konfigurace
define('MAX_SIZE',    8 * 1024 * 1024);   // 8 MB
define('ALLOWED',     ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('IMG_DIR',     dirname(__DIR__) . '/img/reference/');
define('IMG_WEB_PATH','img/reference/');  // relativní cesta z rootu webu

// Ověření nahrávky
if (empty($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    $codes = [1=>'Soubor přesahuje limit serveru',2=>'Soubor přesahuje limit formuláře',
              3=>'Soubor byl nahrán jen částečně',4=>'Žádný soubor nebyl nahrán',
              6=>'Chybí dočasná složka',7=>'Nelze zapsat na disk'];
    $msg = $codes[$_FILES['foto']['error'] ?? 0] ?? 'Chyba nahrávání';
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

$file = $_FILES['foto'];

// Kontrola velikosti
if ($file['size'] > MAX_SIZE) {
    echo json_encode(['ok' => false, 'error' => 'Soubor je příliš velký (max. 8 MB)']);
    exit;
}

// Kontrola MIME typu (čteme ze souboru, ne z hlavičky)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, ALLOWED)) {
    echo json_encode(['ok' => false, 'error' => 'Nepodporovaný formát. Povoleny jsou: JPG, PNG, WebP']);
    exit;
}

// Připravíme cílovou složku
if (!is_dir(IMG_DIR)) {
    if (!mkdir(IMG_DIR, 0755, true)) {
        echo json_encode(['ok' => false, 'error' => 'Nelze vytvořit složku img/reference/']);
        exit;
    }
}

// Bezpečné unikátní jméno souboru
$ext      = match($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg'
};
$basename = 'ref_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dest     = IMG_DIR . $basename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['ok' => false, 'error' => 'Nepodařilo se uložit soubor']);
    exit;
}

// Sestavíme nový záznam pro content.json
$item = [
    'id'        => time(),
    'src'       => IMG_WEB_PATH . $basename,
    'nazev'     => htmlspecialchars(trim($_POST['nazev']    ?? ''), ENT_QUOTES, 'UTF-8'),
    'misto'     => htmlspecialchars(trim($_POST['misto']    ?? ''), ENT_QUOTES, 'UTF-8'),
    'rok'       => (int)($_POST['rok']       ?? date('Y')),
    'popis'     => htmlspecialchars(trim($_POST['popis']    ?? ''), ENT_QUOTES, 'UTF-8'),
    'kategorie' => in_array($_POST['kategorie'] ?? '', ['zdeni','omitky','obklady','rekonstrukce'])
                   ? $_POST['kategorie'] : 'zdeni',
];

echo json_encode(['ok' => true, 'item' => $item]);
