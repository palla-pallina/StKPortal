<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh->beginTransaction();


        function uploadFile($field) {
            if (!empty($_FILES[$field]['name'])) {
                $dir = "../uploads/";
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $name = time() . "_" . basename($_FILES[$field]['name']);
                $path = $dir . $name;
                move_uploaded_file($_FILES[$field]['tmp_name'], $path);
                return $name;
            }
            return null;
        }

        // BRIDE DOCUMENTS
        $b_birth_cert = uploadFile('b_birth_certificate');
        $b_baptismal_cert = uploadFile('b_baptismal_certificate');
        $b_marriage_license = uploadFile('b_marriage_license');
        $b_confirmation_cert = uploadFile('b_confirmation_certificate');
        $b_cenomar = uploadFile('b_cenomar');

        // GROOM DOCUMENTS
        $g_birth_cert = uploadFile('g_birth_certificate');
        $g_baptismal_cert = uploadFile('g_baptismal_certificate');
        $g_marriage_license = uploadFile('g_marriage_license');
        $g_confirmation_cert = uploadFile('g_confirmation_certificate');
        $g_cenomar = uploadFile('g_cenomar');

        if (empty($_POST['bride_lastname']) || empty($_POST['groom_lastname'])) {
        die("Bride and Groom last names are required.");
        }

        $stmt = $dbh->prepare("
            INSERT INTO weddings (
                status, date, time,

                bride_firstname, bride_middlename, bride_lastname,
                bride_birthdate, bride_nationality,
                bride_contact, bride_email,

                b_birth_certificate, b_baptismal_certificate,
                b_marriage_license, b_confirmation_certificate, b_cenomar,

                groom_firstname, groom_middlename, groom_lastname, groom_suffix,
                groom_birthdate, groom_nationality,
                groom_contact, groom_email,

                g_birth_certificate, g_baptismal_certificate,
                g_marriage_license, g_confirmation_certificate, g_cenomar
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            'Pending',
            $_POST['date'] ?? null,
            $_POST['time'] ?? null,

            $_POST['bride_firstname'] ?? null,
            $_POST['bride_middlename'] ?? null,
            $_POST['bride_lastname'] ?? null,
            $_POST['bride_birthdate'] ?? null,
            $_POST['bride_nationality'] ?? null,
            $_POST['bride_contact'] ?? null,
            $_POST['bride_email'] ?? null,

            $b_birth_cert,
            $b_baptismal_cert,
            $b_marriage_license,
            $b_confirmation_cert,
            $b_cenomar,

            $_POST['groom_firstname'] ?? null,
            $_POST['groom_middlename'] ?? null,
            $_POST['groom_lastname'] ?? null,
            $_POST['groom_suffix'] ?? null,
            $_POST['groom_birthdate'] ?? null,
            $_POST['groom_nationality'] ?? null,
            $_POST['groom_contact'] ?? null,
            $_POST['groom_email'] ?? null,

            $g_birth_cert,
            $g_baptismal_cert,
            $g_marriage_license,
            $g_confirmation_cert,
            $g_cenomar
        ]);



        $confirmation_id = $dbh->lastInsertId();

        // Main Ninong
        $stmt = $dbh->prepare("
            INSERT INTO ninong_tbl
            (confirmation_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix)
            VALUES (?,?,?,?,?)
        ");
        $stmt->execute([
            $confirmation_id,
            $_POST['ninong_firstname'],
            $_POST['ninong_middlename'],
            $_POST['ninong_lastname'],
            $_POST['ninong_suffix']
        ]);

        // Main Ninang
        $stmt = $dbh->prepare("
            INSERT INTO ninang_tbl
            (confirmation_id, ninang_firstname, ninang_middlename, ninang_lastname)
            VALUES (?,?,?,?)
        ");
        $stmt->execute([
            $confirmation_id,
            $_POST['ninang_firstname'],
            $_POST['ninang_middlename'],
            $_POST['ninang_lastname']
        ]);

        // Additional Ninongs
        if (!empty($_POST['as_ninong_firstname'])) {
            $stmt = $dbh->prepare("
                INSERT INTO as_ninong_tbl
                (confirmation_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
                VALUES (?,?,?,?,?)
            ");
            foreach ($_POST['as_ninong_firstname'] as $i => $fname) {
                $stmt->execute([
                    $confirmation_id,
                    $fname,
                    $_POST['as_ninong_middlename'][$i],
                    $_POST['as_ninong_lastname'][$i],
                    $_POST['as_ninong_suffix'][$i]
                ]);
            }
        }

        // Additional Ninangs
        if (!empty($_POST['as_ninang_firstname'])) {
            $stmt = $dbh->prepare("
                INSERT INTO as_ninang_tbl
                (confirmation_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
                VALUES (?,?,?,?)
            ");
            foreach ($_POST['as_ninang_firstname'] as $i => $fname) {
                $stmt->execute([
                    $confirmation_id,
                    $fname,
                    $_POST['as_ninang_middlename'][$i],
                    $_POST['as_ninang_lastname'][$i]
                ]);
            }
        }

        $dbh->commit();
        header("Location: s_wedding.php?success=1");
        exit();

    } catch (Exception $e) {
        $dbh->rollBack();
        die("Insert Failed: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="../../../stylesheets/private/sacrament_insert.css" rel="stylesheet">
<title>Create Wedding</title>
</head>
<body>

<div class="record-container">
    <h2>Create New Wedding</h2>
    <form method="POST" enctype="multipart/form-data">

        <!-- Reservation Details -->
        <div class="record-section">
            <h3>Reservation Details</h3>
            <div class="row record-inline">
                <div class="col-md-2 record-row">
                    <span class="record-label">Wedding Type</span>
                    <select name="wedding_type" class="form-control" required>
                        <option value="" disabled selected></option>
                        <option value="regular">Regular</option>
                        <option value="solo">Solo</option>
                    </select>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Wedding Date</span>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Wedding Time</span>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>
        </div>


        
        <!-- Bride Details -->
        <div class="record-section">
            <h3>Bride Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="bride_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="bride_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="bride_lastname" class="form-control" required>
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birthdate</span>
                    <input type="date" name="bride_birthdate" class="form-control" required>
                </div>
                <div class="col-md-4 record-row">
                    <span class="record-label">Nationality</span>
                    <input type="text" name="bride_nationality" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Bride Documents -->
        <div class="record-section">
            <h3>Bride Documents</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birth Certificate</span>
                    <input type="file" name="b_birth_certificate" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Baptismal Certificate</span>
                    <input type="file" name="b_baptismal_certificate" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Marriage Certificate</span>
                    <input type="file" name="b_marriage_license" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Confirmation Certificate</span>
                    <input type="file" name="b_confirmation_certificate" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Certificate of No Marriage</span>
                    <input type="file" name="b_cenomar" class="form-control">
                </div>
            </div>
        </div>




        <!-- Groom Details -->
        <div class="record-section">
            <h3>Groom Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="groom_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="groom_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="groom_lastname" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="groom_suffix" class="form-control">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birthdate</span>
                    <input type="date" name="groom_birthdate" class="form-control" required>
                </div>
                <div class="col-md-4 record-row">
                    <span class="record-label">Nationality</span>
                    <input type="text" name="groom_nationality" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Groom Documents -->
        <div class="record-section">
            <h3>Groom Documents</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birth Certificate</span>
                    <input type="file" name="g_birth_certificate" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Baptismal Certificate</span>
                    <input type="file" name="g_baptismal_certificate" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Marriage Certificate</span>
                    <input type="file" name="g_marriage_license" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Confirmation Certificate</span>
                    <input type="file" name="g_confirmation_certificate" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Certificate of No Marriage</span>
                    <input type="file" name="g_cenomar" class="form-control">
                </div>
            </div>
        </div>


        <!-- Main Ninong -->
        <div class="record-section">
            <h3>Main Ninong</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="ninong_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="ninong_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="ninong_lastname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="ninong_suffix" class="form-control">
                </div>
            </div>
        </div>

        <!-- Main Ninang -->
        <div class="record-section">
            <h3>Main Ninang</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="ninang_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="ninang_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="ninang_lastname" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Additional Ninongs -->
        <div class="record-section">
            <h3>Additional Ninong</h3>
            <div id="additionalNinong"></div>
            <button type="button" class="btn btn-sm btn-info" onclick="addNinong()">+ Add Ninong</button>
        </div>

        <!-- Additional Ninangs -->
        <div class="record-section">
            <h3>Additional Ninang</h3>
            <div id="additionalNinang"></div>
            <button type="button" class="btn btn-sm btn-info" onclick="addNinang()">+ Add Ninang</button>
        </div>

        <script>
        function addNinong() {
            const container = document.getElementById('additionalNinong');
            const div = document.createElement('div');
            div.classList.add('row', 'record-inline', 'mt-2');

            div.innerHTML = `
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninong_firstname[]" class="form-control" placeholder="Given Name" required>
                </div>
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninong_middlename[]" class="form-control" placeholder="Middle Name">
                </div>
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninong_lastname[]" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-md-2 record-row">
                    <input type="text" name="as_ninong_suffix[]" class="form-control" placeholder="Suffix">
                </div>
                <div class="col-md-1 record-row">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">Remove</button>
                </div>
            `;

            container.appendChild(div);
        }

        function addNinang() {
            const container = document.getElementById('additionalNinang');
            const div = document.createElement('div');
            div.classList.add('row', 'record-inline', 'mt-2');

            div.innerHTML = `
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninang_firstname[]" class="form-control" placeholder="Given Name" required>
                </div>
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninang_middlename[]" class="form-control" placeholder="Middle Name">
                </div>
                <div class="col-md-3 record-row">
                    <input type="text" name="as_ninang_lastname[]" class="form-control" placeholder="Last Name" required>
                </div>
                <div class="col-md-1 record-row">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">Remove</button>
                </div>
            `;

            container.appendChild(div);
        }
        </script>

        <!-- Buttons -->
        <div class="record-buttons" style="margin-top:15px;">
            <button type="submit" class="btn btn-success">Save Record</button>
            <a href="s_wedding.php" class="btn btn-primary">Back</a>
        </div>

    </form>
</div>


</body>
</html>
