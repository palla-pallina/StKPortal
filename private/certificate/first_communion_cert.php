<?php
require_once 'connection.php'; // provides $dbh (PDO)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fc_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$fc_id) {
    die('Invalid First Communion ID');
}

/* ===============================
   FETCH FIRST COMMUNION RECORD
================================ */
$stmt = $dbh->prepare("SELECT * FROM first_communion WHERE id = :id");
$stmt->execute([':id' => $fc_id]);
$fc = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$fc) {
    die('First Communion record not found');
}

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

$childName = fullName($fc, [
    'child_firstname',
    'child_middlename',
    'child_lastname',
    'child_suffix'
]);

$dateObj = new DateTime($fc['date']);
$nth   = $dateObj->format('jS');
$month = $dateObj->format('F');
$year  = $dateObj->format('Y');

$dateIssued = date("F j, Y");

/* ===============================
   BACKGROUND BY GENDER
================================ */
$gender = strtolower($fc['child_gender']);
$bg = ($gender === 'female')
    ? __DIR__ . '/bg_fc_female.png'
    : __DIR__ . '/bg_fc_male.png';

/* ===============================
   MPDF CONFIG
================================ */
require_once __DIR__ . '/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . "/tmp",
    'format' => 'A5',
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_left' => 10,
    'margin_right' => 10
]);

$mpdf->AddFontDirectory('../fonts');
$mpdf->fontdata['oldenglish'] = ['R' => 'oldenglish.ttf'];
$mpdf->SetFont('times', '', 12);

/* ===============================
   PAGE – CERTIFICATE
================================ */
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
    border: 1px solid #000;
    text-align: center;
    font-family: times;
}

.big-name {
    font-size: 30px;
    font-weight: bold;
    margin: 25px 0;
}

.section {
    font-size: 20px;
    line-height: 2;
}

.footer {
    margin-top: 50px;
    font-size: 15px;
}
</style>

<div class="certificate">

<table width="100%" style="margin-bottom: 30px;">
    <tr>
        <td width="15%" style="text-align:left;">
            <img src="$logoLeft" style="width:80px;">
        </td>

        <td width="70%" style="text-align:center;">
            <div style="font-family: oldenglish; font-size: 36px; font-weight: bold;">
                First Holy Communion
            </div>
            <div style="font-size:16px; line-height:1.3; margin-top:5px;">
                DIOCESE OF NOVALICHES<br>
                <span style="color:red">Santa Krus Parish</span><br>
                A-814 Bagumbong Road, Brgy. 171, Caloocan City<br>
                Telephone No. 8-7325924
            </div>
        </td>

        <td width="15%" style="text-align:right;">
            <img src="$logoRight" style="width:80px;">
        </td>
    </tr>
</table>

<p class="section"><strong>This certifies that</strong></p>

<div class="big-name">$childName</div>

<p class="section">
    was first nourished<br> 
    at the Lord's Table on the<br>
    <strong>$nth</strong>
    of <strong>$month</strong> <br>
    in the Year of our Lord <strong>$year</strong>
</p>

<div class="footer">

<table width="100%" style="margin-bottom: 20px; text-align: center;">
    <tr>
        <td width="28%" style="text-align: left;font-size:12px;">
            Date Issued: $dateIssued
        </td>
        <td width="30%" style="text-align:right;">
            __________________________<br>
            {$fc['presiding_priest']}<br>
            Parish Priest<br><br>
        </td>
    </tr>
</table>
</div>

</div>
HTML;

$mpdf->WriteHTML($html);

/* ===============================
   OUTPUT
================================ */
$mpdf->Output(
    'First_Communion_' . preg_replace('/[^A-Za-z0-9]/', '_', $childName) . '.pdf',
    'I'
);
