<?php

require_once 'dbconfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch data from wedding_reservations table
    $stmt = $dbh->prepare("SELECT * FROM weddings WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Map DB fields to POST-like variables
        $_POST["bride_first"] = $data["bride"];
        $_POST["groom_first"] = $data["groom"];
        $_POST["wedding_date"] = $data["date"];
        $_POST["wedding_time"] = $data["time"];
        $_POST["reservation_type"] = $data["reservation_type"];

        // Dummy defaults to avoid errors
        $_POST["bride_middle"] = $_POST["bride_last"] = $_POST["bride_suffix"] = "";
        $_POST["groom_middle"] = $_POST["groom_last"] = $_POST["groom_suffix"] = "";

        $_POST["submit"] = true;
    }
}

if (isset($_POST["submit"])) {

    // Assign form data
    $brideName = $_POST["bride_first"] . " " . $_POST["bride_middle"] . " " . $_POST["bride_last"] . " " . $_POST["bride_suffix"];
    $groomName = $_POST["groom_first"] . " " . $_POST["groom_middle"] . " " . $_POST["groom_last"] . " " . $_POST["groom_suffix"];
    $weddingDate = $_POST["wedding_date"];
    $weddingTime = $_POST["wedding_time"];
    $reservationType = $_POST["reservation_type"];

    $priestName = "Rev. Fr. Juan Dela Cruz";
    $churchName = "Santa Krus Parish";
    $parishLocation = "Diocese of Novaliches • Bagumbong, Caloocan City";

    // Path to background image
    $backgroundPath = __DIR__ . "/background.png";

    // Include mPDF
    require_once __DIR__ . '/vendor/autoload.php';
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4',
        'margin_left' => 0,
        'margin_right' => 0,
        'margin_top' => 0,
        'margin_bottom' => 0
    ]);

    // Set background image
    $mpdf->SetDefaultBodyCSS('background', "url('".$backgroundPath."')");
    $mpdf->SetDefaultBodyCSS('background-image-resize', 6);

    // Certificate content
    $html = '
    <style>
        body { font-family: "Times New Roman", serif; color: #000; margin: 0; padding: 0; }
        .certificate { text-align: center; padding: 160px 80px 60px 80px; }
        .diocese { font-size: 15px; font-weight: bold; text-transform: uppercase; }
        .church-name { font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .address { font-size: 14px; margin-bottom: 15px; }
        .title { font-size: 30px; font-family: "Old English Text MT", "Times New Roman", serif; margin-top: 15px; }
        .subtitle { font-style: italic; font-size: 16px; margin-bottom: 15px; }
        .content { font-size: 18px; line-height: 2.1; text-align: left; width: 85%; margin: 0 auto; }
        .spaced-text { letter-spacing: 2px; word-spacing: 4px; text-transform: uppercase; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 2px; }
        .signature-section { margin-top: 30px; text-align: center; }
        .sign-line { width: 250px; border-top: 1px solid #000; margin: 40px auto 5px auto; }
        p { margin: 10px 0; }
    </style>

    <div class="certificate">
        <div class="diocese">DIOCESE OF NOVALICHES</div>
        <div class="church-name">' . strtoupper($churchName) . '</div>
        <div class="address">' . $parishLocation . '</div>

        <div class="title">Wedding Certificate</div>
        <div class="subtitle">This is to Certify</div>

        <div class="content">
            <p>That  <span class="spaced-text">' . strtoupper($brideName) . '</span></p>
            <p>and  <span class="spaced-text">' . strtoupper($groomName) . '</span></p>
            <p>Were united in Holy Matrimony</p>
            <p>On the  <span class="spaced-text">' . date("jS", strtotime($weddingDate)) . ' day of ' . date("F", strtotime($weddingDate)) . ', Year ' . date("Y", strtotime($weddingDate)) . '</span></p>
            <p>At  <span class="spaced-text">' . strtoupper($churchName) . '</span> by  <span class="spaced-text">' . strtoupper($priestName) . '</span></p>
            <p>Reservation Type: <span class="spaced-text">' . strtoupper($reservationType) . '</span></p>
            <p>Date Issued:  <span class="spaced-text">' . strtoupper(date("F j, Y")) . '</span></p>
        </div>

        <div class="signature-section">
            <div class="sign-line"></div>
            <p>Parish Priest</p>
        </div>
    </div>
    ';

    // Write HTML and output
    $mpdf->WriteHTML($html);
    $mpdf->Output("Wedding_Certificate.pdf", "D");
}
?>
