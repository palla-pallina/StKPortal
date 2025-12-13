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

        $birth_cert = uploadFile('birth_certificate');
        $baptism_cert = uploadFile('marriage_certificate');

        $stmt = $dbh->prepare("
            INSERT INTO baptism
            (baptism_type, date, time, contact, email,
            child_firstname, child_middlename, child_lastname, child_suffix,
            child_gender, birth_date, birth_place, child_nat,
            father_firstname, father_middlename, father_lastname, father_suffix,
            mother_firstname, mother_middlename, mother_lastname,
            province, city, street_no, barangay,
            birth_certificate, marriage_certificate)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $_POST['baptism_type'], $_POST['date'], $_POST['time'],
            $_POST['contact'], $_POST['email'],
            $_POST['child_firstname'], $_POST['child_middlename'],
            $_POST['child_lastname'], $_POST['child_suffix'],
            $_POST['child_gender'], $_POST['birth_date'],
            $_POST['birth_place'], $_POST['child_nat'],
            $_POST['father_firstname'], $_POST['father_middlename'],
            $_POST['father_lastname'], $_POST['father_suffix'],
            $_POST['mother_firstname'], $_POST['mother_middlename'],
            $_POST['mother_lastname'],
            $_POST['province'], $_POST['city'], $_POST['street_no'], $_POST['barangay'],
            
            $birth_cert, $baptism_cert
        ]);

        $baptism_id = $dbh->lastInsertId();

        // Main Ninong
        $stmt = $dbh->prepare("
            INSERT INTO ninong_tbl
            (baptism_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix)
            VALUES (?,?,?,?,?)
        ");
        $stmt->execute([
            $baptism_id,
            $_POST['ninong_firstname'],
            $_POST['ninong_middlename'],
            $_POST['ninong_lastname'],
            $_POST['ninong_suffix']
        ]);

        // Main Ninang
        $stmt = $dbh->prepare("
            INSERT INTO ninang_tbl
            (baptism_id, ninang_firstname, ninang_middlename, ninang_lastname)
            VALUES (?,?,?,?)
        ");
        $stmt->execute([
            $baptism_id,
            $_POST['ninang_firstname'],
            $_POST['ninang_middlename'],
            $_POST['ninang_lastname']
        ]);

        // Additional Ninongs
        if (!empty($_POST['as_ninong_firstname'])) {
            $stmt = $dbh->prepare("
                INSERT INTO as_ninong_tbl
                (baptism_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
                VALUES (?,?,?,?,?)
            ");
            foreach ($_POST['as_ninong_firstname'] as $i => $fname) {
                $stmt->execute([
                    $baptism_id,
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
                (baptism_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
                VALUES (?,?,?,?)
            ");
            foreach ($_POST['as_ninang_firstname'] as $i => $fname) {
                $stmt->execute([
                    $baptism_id,
                    $fname,
                    $_POST['as_ninang_middlename'][$i],
                    $_POST['as_ninang_lastname'][$i]
                ]);
            }
        }

        $dbh->commit();
        header("Location: s_baptism.php?success=1");
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
<title>Create Baptism</title>
</head>
<body>

<div class="record-container">
    <h2>Create New Baptism</h2>
    <form method="POST" enctype="multipart/form-data">

        <!-- Reservation Details -->
        <div class="record-section">
            <h3>Reservation Details</h3>
            <div class="row record-inline">
                <div class="col-md-2 record-row">
                    <span class="record-label">Baptism Type</span>
                    <select name="baptism_type" class="form-control" required>
                        <option value="" disabled selected></option>
                        <option value="regular">Regular</option>
                        <option value="solo">Solo</option>
                    </select>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Baptism Date</span>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Baptism Time</span>
                    <input type="time" name="time" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Contact Number</span>
                    <input type="text" name="contact" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Email Address</span>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Child Details -->
        <div class="record-section">
            <h3>Child Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="child_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="child_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="child_lastname" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="child_suffix" class="form-control">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-2 record-row">
                    <span class="record-label">Gender</span>
                    <select name="child_gender" class="form-control" required>
                        <option value="" disabled selected>Sex</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Birthday</span>
                    <input type="date" name="birth_date" class="form-control" required>
                </div>
                <div class="col-md-4 record-row">
                    <span class="record-label">Birth Place</span>
                    <input type="text" name="birth_place" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Nationality</span>
                    <input type="text" name="child_nat" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Father Details -->
        <div class="record-section">
            <h3>Father Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="father_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="father_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="father_lastname" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="father_suffix" class="form-control">
                </div>
            </div>
        </div>

        <!-- Mother Details -->
        <div class="record-section">
            <h3>Mother Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="mother_firstname" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="mother_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="mother_lastname" class="form-control" required>
                </div>
            </div>
            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-2 record-row">
                    <span class="record-label">Marriage Type</span>
                    <select name="marriage_type" class="form-control">
                        <option value="" disabled selected></option>
                        <option value="Single">Single</option>
                        <option value="Church Marriage">Church Marriage</option>
                        <option value="Civil Marriage">Civil Marriage</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Address -->
        <div class="record-section">
            <h3>Address</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Province</span>
                    <input type="text" name="province" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">City</span>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Street Number</span>
                    <input type="text" name="street_no" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Barangay</span>
                    <input type="text" name="barangay" class="form-control">
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


        <!-- Documents -->
        <div class="record-section">
            <h3>Documents</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birth Certificate</span>
                    <input type="file" name="birth_certificate" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Marriage Certificate</span>
                    <input type="file" name="marriage_certificate" class="form-control">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="record-buttons" style="margin-top:15px;">
            <button type="submit" class="btn btn-success">Save Record</button>
            <a href="s_baptism.php" class="btn btn-primary">Back</a>
        </div>

    </form>
</div>


</body>
</html>
