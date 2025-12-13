<?php
$user_id = $_SESSION['user_id'];
$gender = $_SESSION['gender'];

$stmt = $dbh->prepare("SELECT givenname, middlename, lastname, gender FROM users WHERE user_id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$givenname = $user['givenname'] ?? '';
$middlename = $user['middlename'] ?? '';
$lastname = $user['lastname'] ?? '';
$suffix = $user['suffix'] ?? '';
?>
          
          
        <label>Bride's Name</label>
        <div class="form-row">
          <input type="text" name="bride_firstname" id="bride_first" placeholder="First Name" required />
          <input type="text" name="bride_middlename" id="bride_middle" placeholder="Middle Name" />
          <input type="text" name="bride_lastname" id="bride_last" placeholder="Last Name" required />
          <input type="text" name="bride_suffix" id="bride_suffix" placeholder="Suffix" required />
          
          <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] === "Female"): ?>
          <label style="">
              <input type="checkbox" id="use_my_info" style="height:50%; margin-left: -67px;"> <label style="font-size:10px; color:grey;";>autofill</label>
          </label>
          <?php endif; ?>
        </div>
        
        <label>Groom's Name</label>
        <div class="form-row">
          <input type="text" name="groom_firstname" id="groom_first" placeholder="First Name" />
          <input type="text" name="groom_middlename" id="groom_middle" placeholder="Middle Name" />
          <input type="text" name="groom_lastname" id="groom_last" placeholder="Last Name" />
          <input type="text" name="groom_suffix" id="groom_suffix" placeholder="Suffix" />

          <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] === "Male"): ?>
          <label style="margin-top:15px; display:block;">
              <input type="checkbox" id="use_my_info" style="height:50%; margin-left: -67px;"> <label style="font-size:10px";>autofill</label>
          </label>
          <?php endif; ?>

          
<script>
document.getElementById('use_my_info')?.addEventListener('change', function () {
    const gender = document.getElementById('db_gender').value;

    // Stop if gender is missing
    if (!gender) return;

    const first  = document.getElementById('db_first').value;
    const middle = document.getElementById('db_middle').value;
    const last   = document.getElementById('db_last').value;
    const suffix   = document.getElementById('db_suffix').value;

    if (this.checked) {
        if (gender === "Male") {
            document.getElementById('groom_first').value = first;
            document.getElementById('groom_middle').value = middle;
            document.getElementById('groom_last').value = last;
            document.getElementById('groom_suffix').value = suffix;
        } 
        else if (gender === "Female") {
            document.getElementById('bride_first').value = first;
            document.getElementById('bride_middle').value = middle;
            document.getElementById('bride_last').value = last;
            document.getElementById('bride_suffix').value = suffix;
        }
    } else {
        // Clear all
        document.getElementById('groom_first').value = "";
        document.getElementById('groom_middle').value = "";
        document.getElementById('groom_last').value = "";
        document.getElementById('groom_suffix').value = "";

        document.getElementById('bride_first').value = "";
        document.getElementById('bride_middle').value = "";
        document.getElementById('bride_last').value = "";
        document.getElementById('bride_suffix').value = "";
    }

    
});

</script>