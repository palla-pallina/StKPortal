<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $date = $_POST['date'];
    $contact = $_POST['contact'];
    $time = $_POST['time'];
    $address = $_POST['address'];

    $child_firstname = $_POST['child_firstname'];
    $child_middlename = $_POST['child_middlename'];
    $child_lastname = $_POST['child_lastname'];
    $child_suffix = $_POST['child_suffix'] ?? '';
    $child_gender = $_POST['child_gender'];

    $birthdate = $_POST['birth_date'];
    $birthplace = $_POST['birth_place'];

    $father_firstname = $_POST['father_firstname'];
    $father_middlename = $_POST['father_middlename'];
    $father_lastname = $_POST['father_lastname'];
    $father_suffix = $_POST['father_suffix'] ?? '';

    $mother_firstname = $_POST['mother_firstname'];
    $mother_middlename = $_POST['mother_middlename'];
    $mother_lastname = $_POST['mother_lastname'];

    $married = $_POST['married'];
    $marriage_type = $_POST['marriage_type'];

    $ninong_firstname = $_POST['ninong_firstname'];
    $ninong_middlename = $_POST['ninong_middlename'];
    $ninong_lastname = $_POST['ninong_lastname'];
    $ninong_suffix = $_POST['ninong_suffix'] ?? '';

    $ninang_firstname = $_POST['ninang_firstname'];
    $ninang_middlename = $_POST['ninang_middlename'];
    $ninang_lastname = $_POST['ninang_lastname'];

    $as_ninong_firstname = $_POST['as_ninong_firstname'];
    $as_ninong_middlename = $_POST['as_ninong_middlename'];
    $as_ninong_lastname = $_POST['as_ninong_lastname'];
    $as_ninong_suffix = $_POST['as_ninong_suffix'] ?? '';

    $as_ninang_firstname = $_POST['as_ninang_firstname'];
    $as_ninang_middlename = $_POST['as_ninang_middlename'];
    $as_ninang_lastname = $_POST['as_ninang_lastname'];

    $reservation_type = $_POST['reservation_type'];
    $baptism_type = $_POST['baptism_type'];

    try {

        session_start();

        if (!isset($_SESSION['user_id'])) {
            die("User not logged in.");
        }

        // START TRANSACTION
        $dbh->beginTransaction();

        // ✅ MUST BE A REAL USER ID THAT EXISTS IN users TABLE
        $reservation_user_id = $_SESSION['user_id'];


        /* ------------------- INSERT INTO baptisms ------------------- */
        $sql1 = "INSERT INTO baptisms (
            reservation_user_id,
            child_firstname, child_middlename, child_lastname, child_suffix,
            birth_date, birth_place, child_gender,
            father_firstname, father_middlename, father_lastname, father_suffix, 
            mother_firstname, mother_middlename, mother_lastname,
            married, marriage_type,
            ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix, 
            ninang_firstname, ninang_middlename, ninang_lastname,
            date, address, contact, time, reservation_type, baptism_type
        ) VALUES (
            :reservation_user_id,
            :child_firstname, :child_middlename, :child_lastname, :child_suffix,
            :birth_date, :birth_place, :child_gender,
            :father_firstname, :father_middlename, :father_lastname, :father_suffix,
            :mother_firstname, :mother_middlename, :mother_lastname,
            :married, :marriage_type,
            :ninong_firstname, :ninong_middlename, :ninong_lastname, :ninong_suffix, 
            :ninang_firstname, :ninang_middlename, :ninang_lastname,
            :date, :address, :contact, :time, :reservation_type, :baptism_type
        )";


        $stmt1 = $dbh->prepare($sql1);

        // Bind params for baptisms
        $stmt1->execute([
            ':reservation_user_id' => $reservation_user_id,

            ':child_firstname' => $child_firstname,
            ':child_middlename' => $child_middlename,
            ':child_lastname' => $child_lastname,
            ':child_suffix' => $child_suffix,

            ':birth_date' => $birthdate,
            ':birth_place' => $birthplace,
            ':child_gender' => $child_gender,

            ':father_firstname' => $father_firstname,
            ':father_middlename' => $father_middlename,
            ':father_lastname' => $father_lastname,
            ':father_suffix' => $father_suffix,

            ':mother_firstname' => $mother_firstname,
            ':mother_middlename' => $mother_middlename,
            ':mother_lastname' => $mother_lastname,

            ':married' => $married,
            ':marriage_type' => $marriage_type,

            ':ninong_firstname' => $ninong_firstname,
            ':ninong_middlename' => $ninong_middlename,
            ':ninong_lastname' => $ninong_lastname,
            ':ninong_suffix' => $ninong_suffix,

            ':ninang_firstname' => $ninang_firstname,
            ':ninang_middlename' => $ninang_middlename,
            ':ninang_lastname' => $ninang_lastname,

            ':date' => $date,
            ':address' => $address,
            ':contact' => $contact,
            ':time' => $time,

            ':reservation_type' => $reservation_type,
            ':baptism_type' => $baptism_type
        ]);


        $baptism_id = $dbh->lastInsertId();

        /* ------------------- INSERT INTO ninong_tbl ------------------- */
        $sql2 = "INSERT INTO as_ninong_tbl (
            baptism_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix
        ) VALUES (
            :baptism_id, :as_ninong_firstname, :as_ninong_middlename, :as_ninong_lastname, :as_ninong_suffix
        )";

        $stmt2 = $dbh->prepare($sql2);
        $stmt2->execute([
            ':baptism_id' => $baptism_id,
            ':as_ninong_firstname' => $as_ninong_firstname,
            ':as_ninong_middlename' => $as_ninong_middlename,
            ':as_ninong_lastname' => $as_ninong_lastname,
            ':as_ninong_suffix' => $as_ninong_suffix
        ]);


        /* ------------------- INSERT INTO ninang_tbl ------------------- */
        $sql3 = "INSERT INTO as_ninang_tbl (
            baptism_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname
        ) VALUES (
            :baptism_id, :as_ninang_firstname, :as_ninang_middlename, :as_ninang_lastname
        )";

        $stmt3 = $dbh->prepare($sql3);
        $stmt3->execute([
            ':baptism_id' => $baptism_id,
            ':as_ninang_firstname' => $as_ninang_firstname,
            ':as_ninang_middlename' => $as_ninang_middlename,
            ':as_ninang_lastname' => $as_ninang_lastname
        ]);


        // COMMIT ALL QUERIES
        $dbh->commit();

        echo "<script>
            alert('Baptism reservation submitted successfully!');
            window.location.href='baptism.php';
        </script>";
        exit;

    } catch (PDOException $e) {
        $dbh->rollBack();
        echo "Database Error: " . $e->getMessage();
    }
}
