<?php
require_once 'connection.php'; // provides $dbh (PDO)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$wedding_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$wedding_id) {
    die('Invalid wedding ID');
}

/* ===============================
   FETCH WEDDING RECORD
================================ */
$stmt = $dbh->prepare("SELECT * FROM weddings WHERE id = :id");
$stmt->execute([':id' => $wedding_id]);
$wedding = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wedding) {
    die('Wedding record not found');
}

/* ===============================
   FETCH WITNESSES
================================ */
function fetchAll(PDO $dbh, string $sql, int $id): array {
    $stmt = $dbh->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$main_ninong = fetchAll(
    $dbh,
    "SELECT * FROM ninong_tbl WHERE wedding_id = :id LIMIT 1",
    $wedding_id
);

$main_ninang = fetchAll(
    $dbh,
    "SELECT * FROM ninang_tbl WHERE wedding_id = :id LIMIT 1",
    $wedding_id
);

$as_ninong = fetchAll(
    $dbh,
    "SELECT * FROM as_ninong_tbl WHERE wedding_id = :id",
    $wedding_id
);

$as_ninang = fetchAll(
    $dbh,
    "SELECT * FROM as_ninang_tbl WHERE wedding_id = :id",
    $wedding_id
);

/* ===============================
   FORMAT HELPERS
================================ */
function fullName(array $data, array $keys): string {
    $parts = [];
    foreach ($keys as $key) {
        if (!empty($data[$key])) {
            $parts[] = $data[$key];
        }
    }
    return trim(implode(' ', $parts));
}

$groomName = fullName($wedding, [
    'groom_firstname',
    'groom_middlename',
    'groom_lastname',
    'groom_suffix'
]);

$brideName = fullName($wedding, [
    'bride_firstname',
    'bride_middlename',
    'bride_lastname'
]);

$dateObj = new DateTime($wedding['date']);
$nth   = $dateObj->format('jS');
$month = $dateObj->format('F');
$year  = $dateObj->format('Y');

$dateIssued = date("F j, Y");

$mainNinong = !empty($main_ninong)
    ? fullName($main_ninong[0], [
        'ninong_firstname',
        'ninong_middlename',
        'ninong_lastname',
        'ninong_suffix'
    ])
    : '';

$mainNinang = !empty($main_ninang)
    ? fullName($main_ninang[0], [
        'ninang_firstname',
        'ninang_middlename',
        'ninang_lastname'
    ])
    : '';

/* ===============================
   MPDF CONFIG
================================ */
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . "/tmp",
    'format' => 'A5',
    'margin_top' => 0,
    'margin_bottom' => 0,
    'margin_left' => 0,
    'margin_right' => 0
]);

$mpdf->AddFontDirectory('../fonts');
$mpdf->fontdata['oldenglish'] = ['R' => 'oldenglish.ttf'];
$mpdf->SetFont('times', '', 12);

/* ===============================
   PAGE 1 – CERTIFICATE
================================ */
$bg = __DIR__ . '/bg_wed.png';
$logoLeft  = __DIR__ . '/dioceseofnovaliches.jpg';
$logoRight = __DIR__ . '/stakrusparish.png';

$html = <<<HTML
<style>
@page {
    background-image: url('$bg');
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}

.certificate {
    margin: auto;
    padding: 30px;
    text-align: center;
    font-family: times;
}

.big-name {
    font-size: 22px;
    font-weight: bold;
    margin: 15px 0;
}

.section {
    font-size: 18px;
    line-height: 2;
}

.footer {
    margin-top: 40px;
    font-size: 15px;
}
</style>

<div class="certificate">

<table width="100%" style="margin-bottom: 25px;">
    <tr>
        <td width="15%" style="text-align:left;">
            <img src="$logoLeft" style="width:80px;">
        </td>

        <td width="70%" style="text-align:center;">
            <div style="font-family: oldenglish; font-size: 31px; font-weight: bold;">
                Marriage Certificate
            </div>
            <div style="font-size:16px; line-height:1.3; margin-top:5px;">
                DIOCESE OF NOVALICHES<br>
                <span style="color:red">Santa Krus Parish</span><br>
                A-814 Bagumbong Road, Brgy. 171, Caloocan City
            </div>
        </td>

        <td width="15%" style="text-align:right;">
            <img src="$logoRight" style="width:80px;">
        </td>
    </tr>
</table>

<p class="section"><strong>This certifies that</strong></p>

<div class="big-name">$groomName</div>
<div class="big-name">and</div>
<div class="big-name">$brideName</div>

<p class="section">
    were lawfully married<br>
    on the <strong>$nth</strong> day of <strong>$month</strong>, <strong>$year</strong><br>
    by <strong>{$wedding['presiding_priest']}</strong><br> 
    at Santa Krus Parish, Bagumbong, Caloocan City.
</p><br><br>

<table width="100%" style="margin-bottom: 20px; text-align: center;">
    <tr>
        <td width="30%" style="text-align:center;">
            ____________________________________<br>
            <strong>$mainNinong</strong><br>
            Witness<br>
        </td>
        <td width="30%" style="text-align:center;">
            
        </td>
        <td width="30%" style="text-align:center;">
            <br>____________________________________<br>
            <strong>$mainNinang</strong><br>
            Witness<br><br><br>
        </td>
    </tr>
</table>

<table width="100%" style="margin-bottom: 20px; text-align: center;">
    <tr>
        <td width="50%" style="text-align: left;">
            Date Issued: $dateIssued
        </td>
        <td width="30%" style="text-align:center;">
            
        </td>
        <td width="30%" style="text-align:center;">
            ____________________________________<br>
            <strong>{$wedding['presiding_priest']}</strong><br>
            Parish Priest<br><br>
        </td>
    </tr>
</table>

</div>
HTML;

$mpdf->WriteHTML($html);

/* ===============================
   OUTPUT
================================ */
$mpdf->Output(
    'Marriage_Certificate_' . preg_replace('/[^A-Za-z0-9]/', '_', $groomName . '_' . $brideName) . '.pdf',
    'I'
);
