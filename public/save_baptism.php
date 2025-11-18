<?php
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $date = $_POST['date'];
    $phone = $_POST['phone'];
    $time = $_POST['time'];

    $child_firstname = $_POST['child_firstname'];
    $child_middlename = $_POST['child_middlename'];
    $child_lastname = $_POST['child_lastname'];
    $child_suffix = $_POST['child_suffix'] ?? ''; // NOT NULL in DB

    $birthdate = $_POST['birthdate'];
    $birthplace = $_POST['birthplace'];

    $father_firstname = $_POST['father_firstname'];
    $father_middlename = $_POST['father_middlename'];
    $father_lastname = $_POST['father_lastname'];
    $father_suffix = $_POST['father_suffix'] ?? ''; // NOT NULL in DB

    $mother_firstname = $_POST['mother_firstname'];
    $mother_middlename = $_POST['mother_middlename'];
    $mother_lastname = $_POST['mother_lastname'];
    $mother_suffix = $_POST['mother_suffix'] ?? ''; // NOT NULL in DB

    $reservation_type = $_POST['reservation_type'];

    try {

        $sql = "INSERT INTO baptisms (
                child_firstname, child_middlename, child_lastname, child_suffix,
                birth_date, birth_place,
                father_firstname, father_middlename, father_lastname, father_suffix, 
                mother_firstname, mother_middlename, mother_lastname, mother_suffix, 
                date, phone, time, reservation_type
            )
            VALUES (
                :child_firstname, :child_middlename, :child_lastname, :child_suffix,
                :birth_date, :birth_place,
                :father_firstname, :father_middlename, :father_lastname, :father_suffix,
                :mother_firstname, :mother_middlename, :mother_lastname, :mother_suffix,
                :date, :phone, :time, :reservation_type
            )";

        $stmt = $dbh->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':child_firstname', $child_firstname);
        $stmt->bindParam(':child_middlename', $child_middlename);
        $stmt->bindParam(':child_lastname', $child_lastname);
        $stmt->bindParam(':child_suffix', $child_suffix);

        $stmt->bindParam(':birth_date', $birthdate);
        $stmt->bindParam(':birth_place', $birthplace);

        $stmt->bindParam(':father_firstname', $father_firstname);
        $stmt->bindParam(':father_middlename', $father_middlename);
        $stmt->bindParam(':father_lastname', $father_lastname);
        $stmt->bindParam(':father_suffix', $father_suffix);

        $stmt->bindParam(':mother_firstname', $mother_firstname);
        $stmt->bindParam(':mother_middlename', $mother_middlename);
        $stmt->bindParam(':mother_lastname', $mother_lastname);
        $stmt->bindParam(':mother_suffix', $mother_suffix);

        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':time', $time);

        $stmt->bindParam(':reservation_type', $reservation_type);

        $stmt->execute();
        echo "<script>
                alert('Baptism reservation submitted successfully!');
                window.location.href='baptism.php';
              </script>";
        exit;

    } catch (PDOException $e) {
        echo "Error saving reservation: " . $e->getMessage();
    }

} else {
    echo "Invalid request method.";
}
?>
