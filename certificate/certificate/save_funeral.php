<?php
require_once 'dbconfig.php'; // make sure this connects $dbh (PDO)

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Combine deceased full name
    $deceased_fullname = trim(
        $_POST['deceased_firstname'] . " " .
        ($_POST['deceased_middlename'] ?? '') . " " .
        $_POST['deceased_lastname'] . " " .
        ($_POST['deceased_suffix'] ?? '')
    );

    // Other deceased info
    $cause_of_death = $_POST['cause_of_death'];
    $deceased_dob = $_POST['deceased_dob'];
    $funeral_date = $_POST['funeral_date'];
    $funeral_time = $_POST['funeral_time'];
    $funeral_address = $_POST['funeral_address'];

    // Contact person
    $contact_person_firstname = $_POST['contact_person_firstname'];
    $contact_person_middlename = $_POST['contact_person_middlename'] ?? null;
    $contact_person_lastname = $_POST['contact_person_lastname'];
    $contact_person_relation = $_POST['contact_person_relation'];
    $contact_person_contact_no = $_POST['contact_person_contact_no'];
    $contact_person_email = $_POST['contact_person_email'] ?? null;

    $reservation_type = $_POST['reservation_type'] ?? 'funeral';

    try {
        $sql = "INSERT INTO funeral_reservation (
                    reservation_type, deceased_fullname, cause_of_death, deceased_dob,
                    funeral_date, funeral_time, funeral_address,
                    contact_person_firstname, contact_person_middlename, contact_person_lastname,
                    contact_person_relation, contact_person_contact_no, contact_person_email
                ) VALUES (
                    :reservation_type, :deceased_fullname, :cause_of_death, :deceased_dob,
                    :funeral_date, :funeral_time, :funeral_address,
                    :contact_person_firstname, :contact_person_middlename, :contact_person_lastname,
                    :contact_person_relation, :contact_person_contact_no, :contact_person_email
                )";

        $stmt = $dbh->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':reservation_type', $reservation_type);
        $stmt->bindParam(':deceased_fullname', $deceased_fullname);
        $stmt->bindParam(':cause_of_death', $cause_of_death);
        $stmt->bindParam(':deceased_dob', $deceased_dob);
        $stmt->bindParam(':funeral_date', $funeral_date);
        $stmt->bindParam(':funeral_time', $funeral_time);
        $stmt->bindParam(':funeral_address', $funeral_address);
        $stmt->bindParam(':contact_person_firstname', $contact_person_firstname);
        $stmt->bindParam(':contact_person_middlename', $contact_person_middlename);
        $stmt->bindParam(':contact_person_lastname', $contact_person_lastname);
        $stmt->bindParam(':contact_person_relation', $contact_person_relation);
        $stmt->bindParam(':contact_person_contact_no', $contact_person_contact_no);
        $stmt->bindParam(':contact_person_email', $contact_person_email);

        $stmt->execute();

        echo "<script>
                alert('Funeral reservation submitted successfully!');
                window.location.href='funeral.php';
              </script>";

    } catch (PDOException $e) {
        echo "Error saving reservation: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>
