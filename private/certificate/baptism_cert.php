<?php
require_once 'connection.php'; // provides $dbh (PDO)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$baptism_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$baptism_id) {
    die('Invalid baptism ID');
}

/* ===============================
   FETCH BAPTISM RECORD
================================ */
$stmt = $dbh->prepare("SELECT * FROM baptisms WHERE id = :id");
$stmt->execute([':id' => $baptism_id]);
$baptism = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$baptism) {
    die('Baptism record not found');
}

/* ===============================
   FETCH SPONSORS
================================ */
function fetchAll(PDO $dbh, string $sql, int $id): array {
    $stmt = $dbh->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$main_ninong = fetchAll(
    $dbh,
    "SELECT * FROM ninong_tbl WHERE baptism_id = :id LIMIT 1",
    $baptism_id
);

$main_ninang = fetchAll(
    $dbh,
    "SELECT * FROM ninang_tbl WHERE baptism_id = :id LIMIT 1",
    $baptism_id
);

$as_ninong = fetchAll(
    $dbh,
    "SELECT * FROM as_ninong_tbl WHERE baptism_id = :id",
    $baptism_id
);

$as_ninang = fetchAll(
    $dbh,
    "SELECT * FROM as_ninang_tbl WHERE baptism_id = :id",
    $baptism_id
);

/* ===============================
   FORMAT VALUES
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

$childName  = fullName($baptism, [
    'child_firstname', 'child_middlename', 'child_lastname', 'child_suffix'
]);

$fatherName = fullName($baptism, [
    'father_firstname', 'father_middlename', 'father_lastname', 'father_suffix'
]);

$motherName = fullName($baptism, [
    'mother_firstname', 'mother_middlename', 'mother_lastname'
]);

$birthDate   = date("jS \\d\\a\\y \\o\\f F Y", strtotime($baptism['birth_date']));
$baptismDate = date("jS \\d\\a\\y \\o\\f F, Y", strtotime($baptism['date']));
$dateIssued  = date("F j, Y");

$mainNinong = !empty($main_ninong)
    ? fullName($main_ninong[0], ['ninong_firstname', 'ninong_middlename', 'ninong_lastname', 'ninong_suffix'])
    : '';

$mainNinang = !empty($main_ninang)
    ? fullName($main_ninang[0], ['ninang_firstname', 'ninang_middlename', 'ninang_lastname'])
    : '';

// Load mPDF
require_once __DIR__ . '/vendor/autoload.php';
/* ===============================
   MPDF CONFIGURATION
================================ */
$mpdf = new \Mpdf\Mpdf([
    'tempDir' => __DIR__ . "/tmp",
    'format' => 'A4',
    'margin_top' => 10,
    'margin_bottom' => 10,
    'margin_left' => 10,
    'margin_right' => 10
]);

$mpdf->AddFontDirectory('../fonts');

$mpdf->fontdata['oldenglish'] = [
    'R' => 'oldenglish.ttf'
];

$mpdf->SetFont('times', '', 12);

/* ===============================
   PAGE 1 – CERTIFICATE
================================ */
$bg = __DIR__ . '/bg_bapt.png';
$logoLeft  = __DIR__ . '/dioceseofnovaliches.jpg';
$logoRight = __DIR__ . '/stakrusparish.png';

$html = <<<HTML
<style>

/* ===== PAGE BACKGROUND ===== */
@page {
    background-image: url('$bg');
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
}

/* ===== CERTIFICATE FRAME ===== */
.certificate {
    margin: auto;
    padding: 30px;
    border: 1px solid #000;
    text-align: center;
    font-family: times;
}

.header {
    display: flex;
    flex-direction: row;          /* horizontal layout */
    justify-content: space-between; /* logos at edges, content in center */
    align-items: center;          /* vertically center all */
    margin-bottom: 25px;
}

.header-logo {
    width: 80px;    /* equal size for both logos */
    height: auto;
    object-fit: contain;
}

.header-content {
    flex: 1;                 /* take remaining space */
    text-align: center;      /* center title/subtitle */
    padding: 0 20px;         /* optional spacing from logos */
}

.title {
    font-family: oldenglish;
    font-size: 36px;
    font-weight: bold;
    margin: 0;
}

.subtitle {
    font-size: 16px;
    line-height: 1.3;
    margin-top: 5px;
}


.big-name {
    font-size: 29px;
    font-weight: bold;
    margin: 20px 0;
}

.section {
    font-size: 19px;
    line-height: 2;
}

.footer {
    margin-top: 40px;
    font-size: 15px;
}

</style>

<div class="certificate">

    <table width="100%" style="margin-bottom: 20px; text-align: center;">
        <tr>
            <!-- Left logo -->
            <td width="15%" style="text-align: left;">
                <img src="$logoLeft" style="width:80px; height:auto;">
            </td>

            <!-- Center title + subtitle -->
            <td width="70%" style="text-align: center;">
                <div style="font-family: oldenglish; font-size: 36px; font-weight: bold;">Baptismal Certificate</div>
                <div style="font-size:16px; line-height:1.3; margin-top:5px;">
                    DIOCESE OF NOVALICHES<br>
                    <span style="color:red">Santa Krus Parish</span><br>
                    A-814 Bagumbong Road, Brgy. 171, Caloocan City<br>
                    Telephone No. 8-7325924
                </div>
            </td>

            <!-- Right logo -->
            <td width="15%" style="text-align: right;">
                <img src="$logoRight" style="width:80px; height:auto;">
            </td>
        </tr>
    </table>

    <p class="section"><strong>This is to certify that</strong></p>

    <div class="big-name">$childName</div>

    <p class="section">
        Child of <strong>$fatherName</strong><br>
        and <strong>$motherName</strong><br><br>
        Born in <strong>{$baptism['birth_place']}</strong>,<br>
        in the $birthDate<br>
        has received
    </p>

    <div class="big-name">The Holy Sacrament of Baptism</div>

    <p class="section">
        according to the rite of the Roman Catholic Church<br><br>
        on the $baptismDate<br>
        By the {$baptism['presiding_priest']}<br><br>
        The sponsors being <strong>$mainNinong</strong><br>
        and <strong>$mainNinang</strong>
    </p>

    <div class="footer">

    <table width="100%" style="margin-bottom: 20px; text-align: center;">
        <tr>
            <td width="20%" style="text-align: left;">
                Date Issued: $dateIssued
            </td>
            <td width="50%" style="text-align:right;">
                ____________________________________<br>
                <strong>{$baptism['presiding_priest']}</strong><br>
                Parish Priest<br><br>
            </td>
        </tr>
    </table>
    </div>

</div>
HTML;



$mpdf->WriteHTML($html);

/* ===============================
   PAGE 2 – ADDITIONAL SPONSORS
================================ */
if (!empty($as_ninong) || !empty($as_ninang)) {
    $mpdf->AddPage();
    $mpdf->SetFont('times', '', 14);

    $mpdf->WriteHTML("<h2 style='text-align:center'>Additional Sponsors</h2><hr>");

    if (!empty($as_ninong)) {
        $mpdf->WriteHTML("<h3>Ninong</h3><ul>");
        foreach ($as_ninong as $n) {
            $mpdf->WriteHTML("<li>" . fullName($n, [
                'as_ninong_firstname',
                'as_ninong_middlename',
                'as_ninong_lastname',
                'as_ninong_suffix'
            ]) . "</li>");
        }
        $mpdf->WriteHTML("</ul>");
    }

    if (!empty($as_ninang)) {
        $mpdf->WriteHTML("<h3>Ninang</h3><ul>");
        foreach ($as_ninang as $n) {
            $mpdf->WriteHTML("<li>" . fullName($n, [
                'as_ninang_firstname',
                'as_ninang_middlename',
                'as_ninang_lastname'
            ]) . "</li>");
        }
        $mpdf->WriteHTML("</ul>");
    }
}

/* ===============================
   OUTPUT PDF
================================ */
$mpdf->Output(
    'Baptism_Certificate_' . preg_replace('/[^A-Za-z0-9]/', '_', $childName) . '.pdf',
    'I'
);
