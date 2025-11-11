<?php



require_once 'dbconfig.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch data from database
    $stmt = $dbh->prepare("SELECT * FROM baptism_reservations WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        // Map the DB fields to POST-like variables
        $_POST["child_first"] = $data["child_name"];
        $_POST["birthdate"] = $data["birth_date"];
        $_POST["birthplace"] = $data["birth_place"];
        $_POST["father_first"] = $data["father_name"];
        $_POST["mother_first"] = $data["mother_name"];
        $_POST["preferred_date"] = $data["preferred_date"];

        // dummy defaults to avoid errors (optional)
        $_POST["child_middle"] = $_POST["child_last"] = $_POST["child_suffix"] = "";
        $_POST["mother_middle"] = $_POST["mother_last"] = $_POST["mother_suffix"] = "";
        $_POST["father_middle"] = $_POST["father_last"] = $_POST["father_suffix"] = "";
        $_POST["main_ninong_first"] = $_POST["main_ninong_middle"] = $_POST["main_ninong_last"] = $_POST["main_ninong_suffix"] = "";
        $_POST["main_ninang_first"] = $_POST["main_ninang_middle"] = $_POST["main_ninang_last"] = $_POST["main_ninang_suffix"] = "";

        // simulate submit
        $_POST["submit"] = true;
    }
}
if (isset($_POST["submit"])) {

    // Assign form data
    $childName = $_POST["child_first"] . " " . $_POST["child_middle"] . " " . $_POST["child_last"] . " " . $_POST["child_suffix"];
    $birthdate = $_POST["birthdate"];
    $birthplace = $_POST["birthplace"];
    $motherName = $_POST["mother_first"] . " " . $_POST["mother_middle"] . " " . $_POST["mother_last"] . " " . $_POST["mother_suffix"];
    $fatherName = $_POST["father_first"] . " " . $_POST["father_middle"] . " " . $_POST["father_last"] . " " . $_POST["father_suffix"];
    $mainNinong = $_POST["main_ninong_first"] . " " . $_POST["main_ninong_middle"] . " " . $_POST["main_ninong_last"] . " " . $_POST["main_ninong_suffix"];
    $mainNinang = $_POST["main_ninang_first"] . " " . $_POST["main_ninang_middle"] . " " . $_POST["main_ninang_last"] . " " . $_POST["main_ninang_suffix"];

    $preferredDate = $_POST["preferred_date"];
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

    // ✅ Set background image properly
    $mpdf->SetDefaultBodyCSS('background', "url('".$backgroundPath."')");
    $mpdf->SetDefaultBodyCSS('background-image-resize', 6); // stretch to fit page

    // Certificate content
    $html = '
    <style>
        body {
            font-family: "Times New Roman", serif;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .certificate {
            text-align: center;
            padding: 160px 80px 60px 80px;
        }
        .diocese {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .church-name {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .address {
            font-size: 14px;
            margin-bottom: 15px;
        }
        .title {
            font-size: 30px;
            font-family: "Old English Text MT", "Times New Roman", serif;
            margin-top: 15px;
        }
        .subtitle {
            font-style: italic;
            font-size: 16px;
            margin-bottom: 15px;
        }
        .content {
            font-size: 18px;
            line-height: 2.1;
            text-align: left;
            width: 85%;
            margin: 0 auto;
        }
        /* ✨ Balanced, elegant spacing for names */
        .spaced-text {
            letter-spacing: 2px;
            word-spacing: 4px;
            text-transform: uppercase;
            display: inline-block;
            border-bottom: 1px solid #000; /* ✨ Underline added */
            padding-bottom: 2px;
        }
        .signature-section {
            margin-top: 30px;
            text-align: center;
        }
        .sign-line {
            width: 250px;
            border-top: 1px solid #000;
            margin: 40px auto 5px auto;
        }
        p {
            margin: 10px 0;
        }
    </style>

    <div class="certificate">
        <div class="diocese">DIOCESE OF NOVALICHES</div>
        <div class="church-name">' . strtoupper($churchName) . '</div>
        <div class="address">' . $parishLocation . '</div>

        <div class="title">Baptismal Certificate</div>
        <div class="subtitle">This is to Certify</div>

        <div class="content">
            <p>That  <span class="spaced-text">' . strtoupper($childName) . '</span></p>
            <p>Child of  <span class="spaced-text">' . strtoupper($motherName) . '</span>  and  <span class="spaced-text">' . strtoupper($fatherName) . '</span></p>
            <p>Born in  <span class="spaced-text">' . strtoupper($birthplace) . '</span></p>
            <p>On the  <span class="spaced-text">' . date("jS", strtotime($birthdate)) . ' day of ' . date("F", strtotime($birthdate)) . ', Year ' . date("Y", strtotime($birthdate)) . '</span></p>

            <p>Was solemnly Baptized according to the Rites of the Roman Catholic Church</p>

            <p>On the  <span class="spaced-text">' . date("jS", strtotime($preferredDate)) . ' day of ' . date("F", strtotime($preferredDate)) . ', Year ' . date("Y", strtotime($preferredDate)) . '</span></p>

            <p>By the  <span class="spaced-text">' . strtoupper($priestName) . '</span></p>
            <p>The Sponsors being  <span class="spaced-text">' . strtoupper($mainNinong) . '</span>  and  <span class="spaced-text">' . strtoupper($mainNinang) . '</span></p>

            <p>As appears on the Baptismal Book No.  <span class="spaced-text">______</span>  Page No.  <span class="spaced-text">______</span>  Line No.  <span class="spaced-text">______</span>  of this Church</p>

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
    $mpdf->Output("Baptism_Certificate.pdf", "D");
}
?>
