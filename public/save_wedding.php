<?php
require_once 'connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Bride
    $bride_firstname = $_POST['bride_firstname'];
    $bride_middlename = $_POST['bride_middlename'] ?? null;
    $bride_lastname = $_POST['bride_lastname'];
    $bride_suffix = $_POST['bride_suffix'];
    $bride_contact = $_POST['bride_contact'];

    // Groom
    $groom_firstname = $_POST['groom_firstname'];
    $groom_middlename = $_POST['groom_middlename'] ?? null;
    $groom_lastname = $_POST['groom_lastname'];
    $groom_suffix = $_POST['groom_suffix'];
    $groom_contact = $_POST['groom_contact'];

    // Best Man
    $best_man_firstname = $_POST['best_man_firstname'];
    $best_man_middlename = $_POST['best_man_middlename'] ?? null;
    $best_man_lastname = $_POST['best_man_lastname'];
    $best_man_suffix = $_POST['best_man_suffix'];

    // Maid of Honor
    $maid_of_honor_firstname = $_POST['maid_of_honor_firstname'];
    $maid_of_honor_middlename = $_POST['maid_of_honor_middlename'] ?? null;
    $maid_of_honor_lastname = $_POST['maid_of_honor_lastname'];
    $maid_of_honor_suffix = $_POST['maid_of_honor_suffix'];

    $reservation_type = $_POST['reservation_type'] ?? 'wedding';
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    try {
        $sql = "INSERT INTO weddings ( 
                    reservation_type, phone, date, time,
                    bride_firstname, bride_middlename, bride_lastname, bride_suffix, bride_contact,
                    groom_firstname, groom_middlename, groom_lastname, groom_suffix, groom_contact,
                    best_man_firstname, best_man_middlename, best_man_lastname, best_man_suffix,
                    maid_of_honor_firstname, maid_of_honor_middlename, maid_of_honor_lastname, maid_of_honor_suffix
                ) VALUES (
                    :reservation_type, :phone, :date, :time,
                    :bride_firstname, :bride_middlename, :bride_lastname, :bride_suffix, :bride_contact,             
                    :groom_firstname, :groom_middlename, :groom_lastname, :groom_suffix, :groom_contact,
                    :best_man_firstname, :best_man_middlename, :best_man_lastname, :best_man_suffix,                    
                    :maid_of_honor_firstname, :maid_of_honor_middlename, :maid_of_honor_lastname, :maid_of_honor_suffix
                )";

        $stmt = $dbh->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':reservation_type', $reservation_type);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);

        $stmt->bindParam(':bride_firstname', $bride_firstname);
        $stmt->bindParam(':bride_middlename', $bride_middlename);
        $stmt->bindParam(':bride_lastname', $bride_lastname);
        $stmt->bindParam(':bride_suffix', $bride_suffix);
        $stmt->bindParam(':bride_contact', $bride_contact);

        $stmt->bindParam(':groom_firstname', $groom_firstname);
        $stmt->bindParam(':groom_middlename', $groom_middlename);
        $stmt->bindParam(':groom_lastname', $groom_lastname);
        $stmt->bindParam(':groom_suffix', $groom_suffix);
        $stmt->bindParam(':groom_contact', $groom_contact);
        
        $stmt->bindParam(':best_man_firstname', $best_man_firstname);
        $stmt->bindParam(':best_man_middlename', $best_man_middlename);
        $stmt->bindParam(':best_man_lastname', $best_man_lastname);
        $stmt->bindParam(':best_man_suffix', $best_man_suffix);

        $stmt->bindParam(':maid_of_honor_firstname', $maid_of_honor_firstname);
        $stmt->bindParam(':maid_of_honor_middlename', $maid_of_honor_middlename);
        $stmt->bindParam(':maid_of_honor_lastname', $maid_of_honor_lastname);
        $stmt->bindParam(':maid_of_honor_suffix', $maid_of_honor_suffix);


        $stmt->execute();
        echo "<script>
                alert('Wedding reservation submitted successfully!');
                window.location.href='wedding.php';
              </script>";

    } catch (PDOException $e) {
        echo "Error saving reservation: " . $e->getMessage();
    }
} else {
    echo "Invalid request method.";
}
?>