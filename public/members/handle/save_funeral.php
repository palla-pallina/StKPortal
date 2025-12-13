<?php
require_once '../../connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

/* ===============================
   FORM DATA
================================ */

$reservation_user_id = $_SESSION['user_id'];

/* deceased */
$deceased_firstname = $_POST['deceased_firstname'];
$deceased_middlename = $_POST['deceased_middlename'] ?? '';
$deceased_lastname = $_POST['deceased_lastname'];
$deceased_suffix = $_POST['deceased_suffix'] ?? '';

$cause_of_death = $_POST['cause_of_death'];
$funeral_date = $_POST['funeral_date'];
$funeral_time = $_POST['funeral_time'];
$province = $_POST['province'];
$city = $_POST['city'];
$street_no = $_POST['street_no'];
$barangay = $_POST['barangay'];

/* contact_person */
$contact_person_firstname = $_POST['contact_person_firstname'];
$contact_person_middlename = $_POST['contact_person_middlename'] ?? '';
$contact_person_lastname = $_POST['contact_person_lastname'];
$contact_person_suffix = $_POST['contact_person_suffix'] ?? '';
$contact_person_relation = $_POST['contact_person_relation'];
$contact_person_contact_no = $_POST['contact_person_contact_no'];
$contact_person_email = $_POST['contact_person_email'];


/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql = "INSERT INTO funerals (
        reservation_user_id,
        deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix,
        cause_of_death,
        funeral_date, funeral_time, 
        province, city, street_no, barangay,
        contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix,
        contact_person_relation, contact_person_contact_no, contact_person_email
    ) VALUES (
        :reservation_user_id,
        :deceased_firstname, :deceased_middlename, :deceased_lastname, :deceased_suffix,
        :cause_of_death,
        :funeral_date, :funeral_time,
        :province, :city, :street_no, :barangay,
        :contact_person_firstname, :contact_person_middlename, :contact_person_lastname, :contact_person_suffix,
        :contact_person_relation, :contact_person_contact_no, :contact_person_email

    )";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':reservation_user_id' => $reservation_user_id,

        ':deceased_firstname' => $deceased_firstname,
        ':deceased_middlename' => $deceased_middlename,
        ':deceased_lastname' => $deceased_lastname,
        ':deceased_suffix' => $deceased_suffix,

        ':cause_of_death' => $cause_of_death,
        ':funeral_date' => $funeral_date,
        ':funeral_time' => $funeral_time,

        ':province' => $province,
        ':city' => $city,
        ':street_no' => $street_no,
        ':barangay' => $barangay,

        ':contact_person_firstname' => $contact_person_firstname,
        ':contact_person_middlename' => $contact_person_middlename,
        ':contact_person_lastname' => $contact_person_lastname,
        ':contact_person_suffix' => $contact_person_suffix,

        ':contact_person_relation' => $contact_person_relation,
        ':contact_person_contact_no' => $contact_person_contact_no,
        ':contact_person_email' => $contact_person_email
    ]);

    $dbh->commit();
    echo "<script>
            alert('Funeral reservation submitted successfully!');
            window.location.href='../funeral.php';
    </script>";
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
