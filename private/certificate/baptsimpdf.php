<?php
require_once 'connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch data from database
    $stmt = $dbh->prepare("SELECT * FROM baptisms WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {

        // CHILD NAME (DB column is "child")
        $_POST["child_firstname"] = $data["child_firstname"];
        $_POST["child_middlename"] = $data["child_middlename"];
        $_POST["child_lastname"] = $data["child_lastname"];
        $_POST["child_suffix"] = $data["child_suffix"];

        // PARENTS
        $_POST["father_firstname"] = $data["father_firstname"];
        $_POST["father_middlename"] = $data["father_middlename"];
        $_POST["father_lastname"] = $data["father_lastname"];
        $_POST["father_suffix"] = $data["father_suffix"];

        $_POST["mother_firstname"] = $data["mother_firstname"];
        $_POST["mother_middlename"] = $data["mother_middlename"];
        $_POST["mother_lastname"] = $data["mother_lastname"];
        $_POST["mother_suffix"] = $data["mother_suffix"];

        // Sponsors (no values in DB → blank default)
        $_POST["ninong_firstname"] = $data["ninong_firstname"];
        $_POST["ninong_middlename"] = $data["ninong_middlename"];
        $_POST["ninong_lastname"] = $data["ninong_lastname"];
        $_POST["ninong_suffix"] = $data["ninong_suffix"];

        $_POST["ninang_firstname"] = $data["ninang_firstname"];
        $_POST["ninang_middlename"] = $data["ninang_middlename"];
        $_POST["ninang_lastname"] = $data["ninang_lastname"];
        $_POST["ninang_suffix"] = $data["ninang_suffix"];

        // Other fields from DB
        $_POST["birthdate"] = $data["birth_date"];
        $_POST["birthplace"] = $data["birth_place"];
        $_POST["date"] = $data["date"];

        $_POST["submit"] = true;
    }
}

if (isset($_POST["submit"])) {

    // Assemble names
    $childName = trim($_POST["child_firstname"] . " " . $_POST["child_middlename"] . " " . $_POST["child_lastname"] . " " . $_POST["child_suffix"]);
    $motherName = trim($_POST["mother_firstname"] . " " . $_POST["mother_middlename"] . " " . $_POST["mother_lastname"] . " " . $_POST["mother_suffix"]);
    $fatherName = trim($_POST["father_firstname"] . " " . $_POST["father_middlename"] . " " . $_POST["father_lastname"] . " " . $_POST["father_suffix"]);
    $mainNinong = trim($_POST["ninong_firstname"] . " " . $_POST["ninong_middlename"] . " " . $_POST["ninong_lastname"] . " " . $_POST["ninong_suffix"]);
    $mainNinang = trim($_POST["ninang_firstname"] . " " . $_POST["ninang_middlename"] . " " . $_POST["ninang_lastname"] . " " . $_POST["ninang_suffix"]);

    // Other values
    $birthdate = $_POST["birthdate"];
    $birthplace = $_POST["birthplace"];
    $date = $_POST["date"];

    $priestName = "Rev. Fr. James Nitollama";
    $churchName = "Santa Krus Parish";
    $parishLocation = "Diocese of Novaliches • Bagumbong, Caloocan City";

    // Background
    $backgroundPath = __DIR__ . "/background.png";

    // Load mPDF
    require_once __DIR__ . '/vendor/autoload.php';

    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => __DIR__ . "/tmp",
        'format' => 'A4',
        'margin_left' => 0,
        'margin_right' => 0,
        'margin_top' => 0,
        'margin_bottom' => 0
    ]);

    // Apply background
    $mpdf->SetDefaultBodyCSS('background', "url('" . $backgroundPath . "')");
    $mpdf->SetDefaultBodyCSS('background-image-resize', 6);

    // HTML Layout
    $html = '
    <style>
        body { font-family: "Times New Roman", serif; color: #000; margin: 0; padding: 0; }
        .certificate { text-align: center; padding: 160px 80px 60px 80px; }
        .diocese { font-size: 15px; font-weight: bold; text-transform: uppercase; }
        .church-name { font-size: 22px; font-weight: bold; text-transform: uppercase; }
        .address { font-size: 14px; margin-bottom: 15px; }
        .title { font-size: 30px; font-family: "Old English Text MT"; margin-top: 15px; }
        .subtitle { font-style: italic; font-size: 16px; margin-bottom: 15px; }
        .content { font-size: 18px; line-height: 2.1; text-align: left; width: 85%; margin: 0 auto; }
        .spaced-text { letter-spacing: 2px; word-spacing: 4px; text-transform: uppercase; display: inline-block; border-bottom: 1px solid #000; padding-bottom: 2px; }
        .signature-section { margin-top: 30px; text-align: center; }
        .sign-line { width: 250px; border-top: 1px solid #000; margin: 40px auto 5px auto; }
    </style>

    <div class="certificate">
        <div class="diocese">DIOCESE OF NOVALICHES</div>
        <div class="church-name">' . strtoupper($churchName) . '</div>
        <div class="address">' . $parishLocation . '</div>

        <div class="title">Baptismal Certificate</div>
        <div class="subtitle">This is to Certify</div>

        <div class="content">

            <p>That <span class="spaced-text">' . strtoupper($childName) . '</span></p>

            <p>Child of <span class="spaced-text">' . strtoupper($motherName) . '</span> 
            and <span class="spaced-text">' . strtoupper($fatherName) . '</span></p>

            <p>Born in <span class="spaced-text">' . strtoupper($birthplace) . '</span></p>

            <p>On the <span class="spaced-text">' . date("jS", strtotime($birthdate)) . 
            ' day of ' . date("F", strtotime($birthdate)) . ', Year ' . date("Y", strtotime($birthdate)) . '</span></p>

            <p>Was solemnly Baptized according to the Rites of the Roman Catholic Church</p>

            <p>On the <span class="spaced-text">' . date("jS", strtotime($date)) .
            ' day of ' . date("F", strtotime($date)) . ', Year ' . date("Y", strtotime($date)) . '</span></p>

            <p>By <span class="spaced-text">' . strtoupper($priestName) . '</span></p>

            <p>The Sponsors being 
                <span class="spaced-text">' . strtoupper($mainNinong) . '</span> 
                and <span class="spaced-text">' . strtoupper($mainNinang) . '</span>
            </p>

            <p>As appears on the Baptismal Book No. <span class="spaced-text">______</span>
                Page No. <span class="spaced-text">______</span>
                Line No. <span class="spaced-text">______</span>
            </p>

            <p>Date Issued: <span class="spaced-text">' . strtoupper(date("F j, Y")) . '</span></p>
        </div>

    </div>';

    $mpdf->WriteHTML($html);
    $mpdf->Output("Baptism_Certificate.pdf", "D");
}
