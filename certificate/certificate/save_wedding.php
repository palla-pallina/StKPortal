<?php
require_once 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Combine names for full name columns
    $bride = trim($_POST['bride_firstname'] . ' ' . $_POST['bride_middlename'] . ' ' . $_POST['bride_lastname'] . ' ' . $_POST['bride_suffix']);
    $groom = trim($_POST['groom_firstname'] . ' ' . $_POST['groom_middlename'] . ' ' . $_POST['groom_lastname'] . ' ' . $_POST['groom_suffix']);
    $best_man = trim($_POST['best_man_firstname'] . ' ' . $_POST['best_man_middlename'] . ' ' . $_POST['best_man_lastname'] . ' ' . $_POST['best_man_suffix']);
    $maid_of_honor = trim($_POST['maid_of_honor_firstname'] . ' ' . $_POST['maid_of_honor_middlename'] . ' ' . $_POST['maid_of_honor_lastname'] . ' ' . $_POST['maid_of_honor_suffix']);

    // Prepare SQL
    $sql = "INSERT INTO weddings (
                bride, bride_firstname, bride_middlename, bride_lastname, bride_suffix, bride_birthdate, bride_age, bride_birthplace, bride_nationality, bride_address, bride_contact,
                groom, groom_firstname, groom_middlename, groom_lastname, groom_suffix, groom_birthdate, groom_age, groom_birthplace, groom_nationality, groom_address, groom_contact,
                reservation_type, phone, date, time,
                best_man, best_man_firstname, best_man_middlename, best_man_lastname, best_man_suffix,
                maid_of_honor, maid_of_honor_firstname, maid_of_honor_middlename, maid_of_honor_lastname, maid_of_honor_suffix
            ) VALUES (
                :bride, :bride_firstname, :bride_middlename, :bride_lastname, :bride_suffix, :bride_birthdate, :bride_age, :bride_birthplace, :bride_nationality, :bride_address, :bride_contact,
                :groom, :groom_firstname, :groom_middlename, :groom_lastname, :groom_suffix, :groom_birthdate, :groom_age, :groom_birthplace, :groom_nationality, :groom_address, :groom_contact,
                :reservation_type, :phone, :date, :time,
                :best_man, :best_man_firstname, :best_man_middlename, :best_man_lastname, :best_man_suffix,
                :maid_of_honor, :maid_of_honor_firstname, :maid_of_honor_middlename, :maid_of_honor_lastname, :maid_of_honor_suffix
            )";

    $stmt = $dbh->prepare($sql);

    // Bind parameters
    $stmt->execute([
        ':bride' => $bride,
        ':bride_firstname' => $_POST['bride_firstname'],
        ':bride_middlename' => $_POST['bride_middlename'],
        ':bride_lastname' => $_POST['bride_lastname'],
        ':bride_suffix' => $_POST['bride_suffix'],
        ':bride_birthdate' => $_POST['bride_birthdate'],
        ':bride_age' => $_POST['bride_age'],
        ':bride_birthplace' => $_POST['bride_birthplace'],
        ':bride_nationality' => $_POST['bride_nationality'],
        ':bride_address' => $_POST['bride_address'],
        ':bride_contact' => $_POST['bride_contact'],

        ':groom' => $groom,
        ':groom_firstname' => $_POST['groom_firstname'],
        ':groom_middlename' => $_POST['groom_middlename'],
        ':groom_lastname' => $_POST['groom_lastname'],
        ':groom_suffix' => $_POST['groom_suffix'],
        ':groom_birthdate' => $_POST['groom_birthdate'],
        ':groom_age' => $_POST['groom_age'],
        ':groom_birthplace' => $_POST['groom_birthplace'],
        ':groom_nationality' => $_POST['groom_nationality'],
        ':groom_address' => $_POST['groom_address'],
        ':groom_contact' => $_POST['groom_contact'],

        ':reservation_type' => $_POST['reservation_type'],
        ':phone' => $_POST['phone'],
        ':date' => $_POST['date'],
        ':time' => $_POST['time'],

        ':best_man' => $best_man,
        ':best_man_firstname' => $_POST['best_man_firstname'],
        ':best_man_middlename' => $_POST['best_man_middlename'],
        ':best_man_lastname' => $_POST['best_man_lastname'],
        ':best_man_suffix' => $_POST['best_man_suffix'],

        ':maid_of_honor' => $maid_of_honor,
        ':maid_of_honor_firstname' => $_POST['maid_of_honor_firstname'],
        ':maid_of_honor_middlename' => $_POST['maid_of_honor_middlename'],
        ':maid_of_honor_lastname' => $_POST['maid_of_honor_lastname'],
        ':maid_of_honor_suffix' => $_POST['maid_of_honor_suffix']
    ]);

    // Redirect with success notification
    header("Location: wedding.php?success=1");
    exit;
}
?>
