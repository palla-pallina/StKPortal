<?php
require_once 'dbconfig.php';

if (isset($_POST['submit'])) {
    try {
        // Collect only the fields your table supports
        $reservation_type = $_POST['reservation_type'];
        $preferred_date = $_POST['preferred_date'];
        $child_name = $_POST['child_first'] . " " . $_POST['child_middle'] . " " . $_POST['child_last'] . " " . $_POST['child_suffix'];
        $birthdate = $_POST['birthdate'];
        $birthplace = $_POST['birthplace'];
        $father_name = $_POST['father_first'] . " " . $_POST['father_middle'] . " " . $_POST['father_last'] . " " . $_POST['father_suffix'];
        $mother_name = $_POST['mother_first'] . " " . $_POST['mother_middle'] . " " . $_POST['mother_last'] . " " . $_POST['mother_suffix'];

        // Insert only the allowed fields
        $sql = "INSERT INTO baptism_reservations 
                (child_name, birth_date, birth_place, father_name, mother_name, preferred_date, reservation_type)
                VALUES 
                (:child_name, :birth_date, :birth_place, :father_name, :mother_name, :preferred_date, :reservation_type)";
        
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':child_name' => $child_name,
            ':birth_date' => $birthdate,
            ':birth_place' => $birthplace,
            ':father_name' => $father_name,
            ':mother_name' => $mother_name,
            ':preferred_date' => $preferred_date,
            ':reservation_type' => $reservation_type
        ]);

        // Show success message
        echo "<script>
                alert('✅ Complete! Please wait for your email confirmation.');
                window.location.href = 'baptism.php';
              </script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
} else {
    header("Location: baptism.php");
    exit();
}
?>
