<?php
// admin/upload.php — nahraje fotku do img/reference/{slug}/ a vrátí nový záznam

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Metoda není povolena']);
    exit;
}

// Konfigurace
define('MAX_SIZE',    8 * 1024 * 1024);
define('ALLOWED',     ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('REF_DIR',     dirname(__DIR__) . '/img/reference/');

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

// Kontrola MIME typu
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, ALLOWED)) {
    echo json_encode(['ok' => false, 'error' => 'Nepodporovaný formát. Povoleny jsou: JPG, PNG, WebP']);
    exit;
}

$ext = match($mime) {
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
    default      => 'jpg'
};

// Slug z názvu projektu
$nazev = trim($_POST['nazev'] ?? '');
if (empty($nazev)) {
    echo json_encode(['ok' => false, 'error' => 'Vyplňte název projektu']);
    exit;
}

$slug = mb_strtolower($nazev, 'UTF-8');
$slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);
$slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
$slug = trim($slug, '-');
if (empty($slug)) $slug = 'reference';

// Pokud složka existuje, přidáme suffix
$folderName = $slug;
$i = 2;
while (is_dir(REF_DIR . $folderName) && $i < 100) {
    $folderName = $slug . '-' . $i++;
}

$folderPath = REF_DIR . $folderName . '/';
if (!mkdir($folderPath, 0755, true)) {
    echo json_encode(['ok' => false, 'error' => 'Nelze vytvořit složku']);
    exit;
}

// Soubor: {slug}-01.jpg
$filename = $folderName . '-01.' . $ext;
$dest     = $folderPath . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    echo json_encode(['ok' => false, 'error' => 'Nepodařilo se uložit soubor']);
    exit;
}

// Záznam v nové folder+images struktuře
$item = [
    'id'        => $folderName,
    'folder'    => 'img/reference/' . $folderName,
    'nazev'     => htmlspecialchars($nazev, ENT_QUOTES, 'UTF-8'),
    'misto'     => htmlspecialchars(trim($_POST['misto']    ?? ''), ENT_QUOTES, 'UTF-8'),
    'popis'     => htmlspecialchars(trim($_POST['popis']    ?? ''), ENT_QUOTES, 'UTF-8'),
    'kategorie' => in_array($_POST['kategorie'] ?? '', ['zdeni','omitky','obklady','rekonstrukce'])
                   ? $_POST['kategorie'] : 'zdeni',
    'images'    => [$filename],
];

echo json_encode(['ok' => true, 'item' => $item]);
