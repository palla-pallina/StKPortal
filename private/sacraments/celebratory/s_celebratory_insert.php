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

        $g = fn($k) => trim($_POST[$k] ?? '');

        // Required (based on schema)
        $date             = $g('date');
        $time             = $g('time');
        $celebration_type = $g('celebration_type');

        $contact_relation = $g('contact_relation');
        $contact_number   = $g('contact_number');
        $contact_email    = $g('contact_email');

        if ($date === '' || $time === '' || $celebration_type === '' || $contact_relation === '' || $contact_number === '' || $contact_email === '') {
            throw new Exception("Please fill in all required fields.");
        }

        // Type-specific (clear irrelevant fields)
        $thanksgiving = null;
        $special_intention = null;

        $celebrant_firstname = $celebrant_middlename = $celebrant_lastname = $celebrant_suffix = null;
        $celebrant_birthdate = null;

        $wife_firstname = $wife_middlename = $wife_lastname = null;
        $husband_firstname = $husband_middlename = $husband_lastname = $husband_suffix = null;
        $wedding_date = null;

        if ($celebration_type === 'Thanksgiving') {
            $thanksgiving = $g('thanksgiving') ?: null;

        } elseif ($celebration_type === 'Birthday') {
            $celebrant_firstname = $g('celebrant_firstname') ?: null;
            $celebrant_middlename = $g('celebrant_middlename') ?: null;
            $celebrant_lastname = $g('celebrant_lastname') ?: null;
            $celebrant_suffix = $g('celebrant_suffix') ?: null;
            $celebrant_birthdate = $g('celebrant_birthdate') ?: null;

        } elseif ($celebration_type === 'Wedding Anniversary') {
            $wife_firstname = $g('wife_firstname') ?: null;
            $wife_middlename = $g('wife_middlename') ?: null;
            $wife_lastname = $g('wife_lastname') ?: null;

            $husband_firstname = $g('husband_firstname') ?: null;
            $husband_middlename = $g('husband_middlename') ?: null;
            $husband_lastname = $g('husband_lastname') ?: null;
            $husband_suffix = $g('husband_suffix') ?: null;

            $wedding_date = $g('wedding_date') ?: null;

        } else { // Special Intention
            $special_intention = $g('special_intention') ?: null;
        }

        $stmt = $dbh->prepare("
            INSERT INTO celebratory
            (
              reservation_user_id,
              status,
              date, time, celebration_type,
              mass_venue, province, city, street_no, barangay,
              thanksgiving, special_intention,
              celebrant_firstname, celebrant_middlename, celebrant_lastname, celebrant_suffix, celebrant_birthdate,
              wife_firstname, wife_middlename, wife_lastname,
              husband_firstname, husband_middlename, husband_lastname, husband_suffix,
              wedding_date,
              contact_firstname, contact_middlename, contact_lastname, contact_suffix,
              contact_relation, contact_number, contact_email
            )
            VALUES
            (
              :reservation_user_id,
              'Pending',
              :date, :time, :celebration_type,
              :mass_venue, :province, :city, :street_no, :barangay,
              :thanksgiving, :special_intention,
              :celebrant_firstname, :celebrant_middlename, :celebrant_lastname, :celebrant_suffix, :celebrant_birthdate,
              :wife_firstname, :wife_middlename, :wife_lastname,
              :husband_firstname, :husband_middlename, :husband_lastname, :husband_suffix,
              :wedding_date,
              :contact_firstname, :contact_middlename, :contact_lastname, :contact_suffix,
              :contact_relation, :contact_number, :contact_email
            )
        ");

        $stmt->execute([
            ':reservation_user_id' => $_SESSION['user_id'] ?? null,

            ':date' => $date,
            ':time' => $time,
            ':celebration_type' => $celebration_type,

            ':mass_venue' => $g('mass_venue') ?: null,
            ':province' => $g('province') ?: null,
            ':city' => $g('city') ?: null,
            ':street_no' => $g('street_no') ?: null,
            ':barangay' => $g('barangay') ?: null,

            ':thanksgiving' => $thanksgiving,
            ':special_intention' => $special_intention,

            ':celebrant_firstname' => $celebrant_firstname,
            ':celebrant_middlename' => $celebrant_middlename,
            ':celebrant_lastname' => $celebrant_lastname,
            ':celebrant_suffix' => $celebrant_suffix,
            ':celebrant_birthdate' => $celebrant_birthdate,

            ':wife_firstname' => $wife_firstname,
            ':wife_middlename' => $wife_middlename,
            ':wife_lastname' => $wife_lastname,

            ':husband_firstname' => $husband_firstname,
            ':husband_middlename' => $husband_middlename,
            ':husband_lastname' => $husband_lastname,
            ':husband_suffix' => $husband_suffix,

            ':wedding_date' => $wedding_date,

            ':contact_firstname' => $g('contact_firstname') ?: null,
            ':contact_middlename' => $g('contact_middlename') ?: null,
            ':contact_lastname' => $g('contact_lastname') ?: null,
            ':contact_suffix' => $g('contact_suffix') ?: null,

            ':contact_relation' => $contact_relation,
            ':contact_number' => $contact_number,
            ':contact_email' => $contact_email,
        ]);

        $dbh->commit();
        header("Location: s_celebratory.php?success=1");
        exit();

    } catch (Exception $e) {
        if ($dbh->inTransaction()) $dbh->rollBack();
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
<title>Create Celebratory Mass</title>
<style>
.hidden { display:none; }
</style>
</head>
<body>

<div class="record-container">
    <h2>Create New Celebratory Mass</h2>
    <form method="POST">

        <!-- Reservation Details -->
        <div class="record-section">
            <h3>Reservation Details</h3>

            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Mass Date</span>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Mass Time</span>
                    <input type="time" name="time" class="form-control" required>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Celebration Type</span>
                    <select name="celebration_type" id="celebration_type" class="form-control" required>
                        <option value="" disabled selected></option>
                        <option value="Thanksgiving">Thanksgiving</option>
                        <option value="Birthday">Birthday</option>
                        <option value="Wedding Anniversary">Wedding Anniversary</option>
                        <option value="Special Intention">Special Intention</option>
                    </select>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Mass Venue</span>
                    <input type="text" name="mass_venue" class="form-control" placeholder="e.g., Church / Home / Chapel">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-3 record-row">
                    <span class="record-label">Province</span>
                    <input type="text" name="province" class="form-control" value="Metro Manila">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">City</span>
                    <input type="text" name="city" class="form-control" value="Caloocan">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Street No.</span>
                    <input type="text" name="street_no" class="form-control" value="205 Deparo Rd">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Barangay</span>
                    <input type="text" name="barangay" class="form-control">
                </div>
            </div>
        </div>

        <!-- Celebration Details (dynamic) -->
        <div class="record-section">
            <h3>Celebration Details</h3>

            <!-- THANKSGIVING -->
            <div id="section_thanksgiving" class="type-section">
                <div class="record-row">
                    <span class="record-label">Thanksgiving</span>
                    <textarea name="thanksgiving" class="form-control" rows="4" placeholder="Write thanksgiving message..."></textarea>
                </div>
            </div>

            <!-- BIRTHDAY -->
            <div id="section_birthday" class="type-section hidden">
                <div class="row record-inline">
                    <div class="col-md-3 record-row">
                        <span class="record-label">Celebrant First Name</span>
                        <input type="text" name="celebrant_firstname" class="form-control">
                    </div>
                    <div class="col-md-3 record-row">
                        <span class="record-label">Celebrant Middle Name</span>
                        <input type="text" name="celebrant_middlename" class="form-control">
                    </div>
                    <div class="col-md-3 record-row">
                        <span class="record-label">Celebrant Last Name</span>
                        <input type="text" name="celebrant_lastname" class="form-control">
                    </div>
                    <div class="col-md-3 record-row">
                        <span class="record-label">Suffix</span>
                        <input type="text" name="celebrant_suffix" class="form-control">
                    </div>
                </div>

                <div class="row record-inline" style="margin-top:10px;">
                    <div class="col-md-3 record-row">
                        <span class="record-label">Birthdate</span>
                        <input type="date" name="celebrant_birthdate" class="form-control">
                    </div>
                </div>
            </div>

            <!-- WEDDING ANNIVERSARY -->
            <div id="section_wedding" class="type-section hidden">
                <div class="row record-inline">
                    <div class="col-md-4 record-row">
                        <span class="record-label">Wife First Name</span>
                        <input type="text" name="wife_firstname" class="form-control">
                    </div>
                    <div class="col-md-4 record-row">
                        <span class="record-label">Wife Middle Name</span>
                        <input type="text" name="wife_middlename" class="form-control">
                    </div>
                    <div class="col-md-4 record-row">
                        <span class="record-label">Wife Last Name</span>
                        <input type="text" name="wife_lastname" class="form-control">
                    </div>
                </div>

                <div class="row record-inline" style="margin-top:10px;">
                    <div class="col-md-4 record-row">
                        <span class="record-label">Husband First Name</span>
                        <input type="text" name="husband_firstname" class="form-control">
                    </div>
                    <div class="col-md-4 record-row">
                        <span class="record-label">Husband Middle Name</span>
                        <input type="text" name="husband_middlename" class="form-control">
                    </div>
                    <div class="col-md-4 record-row">
                        <span class="record-label">Husband Last Name</span>
                        <input type="text" name="husband_lastname" class="form-control">
                    </div>
                </div>

                <div class="row record-inline" style="margin-top:10px;">
                    <div class="col-md-3 record-row">
                        <span class="record-label">Husband Suffix</span>
                        <input type="text" name="husband_suffix" class="form-control">
                    </div>
                    <div class="col-md-3 record-row">
                        <span class="record-label">Wedding Date</span>
                        <input type="date" name="wedding_date" class="form-control">
                    </div>
                </div>
            </div>

            <!-- SPECIAL INTENTION -->
            <div id="section_intention" class="type-section hidden">
                <div class="record-row">
                    <span class="record-label">Special Intention</span>
                    <textarea name="special_intention" class="form-control" rows="4" placeholder="Write special intention..."></textarea>
                </div>
            </div>

        </div>

        <!-- Contact Person -->
        <div class="record-section">
            <h3>Contact Person</h3>

            <div class="row record-inline">
                <div class="col-md-3 record-row"><span class="record-label">First Name</span><input type="text" name="contact_firstname" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="contact_middlename" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="contact_lastname" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input type="text" name="contact_suffix" class="form-control"></div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-4 record-row">
                    <span class="record-label">Relation</span>
                    <input type="text" name="contact_relation" class="form-control" required>
                </div>
                <div class="col-md-4 record-row">
                    <span class="record-label">Phone</span>
                    <input type="text" name="contact_number" class="form-control" required>
                </div>
                <div class="col-md-4 record-row">
                    <span class="record-label">Email</span>
                    <input type="email" name="contact_email" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="record-buttons" style="margin-top:15px;">
            <button type="submit" class="btn btn-success">Save Record</button>
            <a href="s_celebratory.php" class="btn btn-primary">Back</a>
        </div>

    </form>
</div>

<script>
function toggleSections() {
  const type = document.getElementById('celebration_type').value;

  const secThanks = document.getElementById('section_thanksgiving');
  const secBday   = document.getElementById('section_birthday');
  const secWed    = document.getElementById('section_wedding');
  const secInt    = document.getElementById('section_intention');

  [secThanks, secBday, secWed, secInt].forEach(s => s.classList.add('hidden'));

  if (type === 'Thanksgiving') secThanks.classList.remove('hidden');
  else if (type === 'Birthday') secBday.classList.remove('hidden');
  else if (type === 'Wedding Anniversary') secWed.classList.remove('hidden');
  else if (type === 'Special Intention') secInt.classList.remove('hidden');
}

// default: show thanksgiving section (matches first visible section in markup)
toggleSections();
document.getElementById('celebration_type').addEventListener('change', toggleSections);
</script>

</body>
</html>
