<?php
require_once '../connection.php';
session_start();

// ---------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ------------------------------
// LOAD PHPMailer
// ------------------------------
require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ------------------------------
    // COLLECT & SANITIZE INPUT
    // ------------------------------
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msg     = trim($_POST["message"]);

    if ($name && $email && $subject && $msg) {

        try {
            $mail = new PHPMailer(true);

            // ------------------------------
            // SMTP SETTINGS (GMAIL EXAMPLE)
            // ------------------------------
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'atsaratom@gmail.com';     // ✅ YOUR EMAIL
            $mail->Password   = 'gcrp redk guor brwo';   // ✅ APP PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ------------------------------
            // EMAIL HEADERS
            // ------------------------------
            $mail->setFrom('atsaratom@gmail.com', 'StKPortal');
            $mail->addAddress('atsaratom@gmail.com');        // ✅ RECEIVER
            $mail->addReplyTo($email, $name);

            // ------------------------------
            // EMAIL CONTENT
            // ------------------------------
            $mail->isHTML(true);
            $mail->Subject = "Inquiry: " . $subject;

            $mail->Body = "
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>{$msg}</p>
            ";

            $mail->AltBody = "
                Name: $name
                Email: $email
                Subject: $subject
                Message: $msg
            ";

            // ------------------------------
            // SEND EMAIL
            // ------------------------------
            $mail->send();
            $message = "✅ Your message has been sent successfully!";

        } catch (Exception $e) {
            $message = "❌ Mail Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ All fields are required.";
    }
}
// ---------------------------------------------------------

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            window.history.back();
          </script>";
    exit();
}
$user_name = $_SESSION['givenname'] ?? null; 
// retrieves logged in user's username
$user_id = $_SESSION['user_id']; 
// retrieves logged in user ID
/*--------- USER ACCESS RESTRICT END */

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

/*--------- FOR AUTO FILL
- retrieves user information */
$stmt = $dbh->prepare("
    SELECT givenname, middlename, lastname, suffix, gender, birthday, contact, email
    FROM users WHERE user_id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for JS access
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}
/*--------- FOR AUTO FILL END */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../../stylesheets/public/reservation_form.css" />
    <link rel="stylesheet" href="../../stylesheets/public/TOS_modal.css" />

</head>

<body data-service="Baptism">

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="../homepage.php">Home</a></li>
            <li><a href="../history.php">History</a></li>
            <li><a href="../events.php">News & Events</a></li>
            <!--<li><a href="sacraments.php">Sacraments</a></li>-->

            <li class="li-dropdown">
                <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="../sacraments.php">Sacraments</a></li>
                    <li><a href="../services.php">Services</a></li>
                </ul>
            </li>
            
            <li><a href="../donation.php">Donate</a></li>
            <li><a href="../prayer_page.php">Prayer Request</a></li>
            <li><a href="../contact.php">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="../login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li style="border-bottom: 1px solid var(--yellow);"><a href="../members/status.php">Status</a></li>
                        <li><a href="../logout.php" a>Logout</a></li>
                    </ul>
                </li>

                <li class="nav-notification">
                    <a href="notifications.php">
                        <span class="bell">&#128276;</span>
                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>
<!----------------- NAVBAR END----------------->

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/baptism.jpg');">
    <h1>Baptism</h1>
</div>

<main>

  <div class="breadcrumb">
      <a href="../sacraments.php">Sacraments</a>
      <span></span>
      <a href="../sacraments/baptism_info.php">Baptism</a>
      <span>Reserve Baptism</span>
  </div>
  
  <?php if (!empty($_SESSION['toast_success'])): ?>
    <div class="success-alert">
      <i class="fa-solid fa-circle-check"></i>
      <?= htmlspecialchars($_SESSION['toast_success']) ?>
    </div>
    <?php unset($_SESSION['toast_success']); ?>
  <?php endif; ?>

    <!-- RESERVATION FORM -->
    <div class="reservation-form">

    <!-- INFO SECTION -->
    <div class="info-section">
      <h4>Important Information</h4>
      <ul>
        <li>Baptism reservations are reviewed by the parish office. Please ensure that all details are accurate before submitting.</li>
        <li>You will receive a notification once your reservation is approved.</li>
        <li>Please note that you can only book at least 3 weeks ahead of the intended date. </li> 
        <li><button type="button" id="openBatchRequestModal" class="btn-secondary"> Click here </button> if you would like to request a solo baptism for more than one child. </li>
      </ul>

      <!-- Batch Request Modal -->
      <div id="batchRequestModal" class="modal-overlay" aria-hidden="true" style="display:none;">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="batchRequestTitle">
          <div class="modal-header">
            <h3 id="batchRequestTitle">Request a Batch Date</h3>
            <button type="button" class="modal-close" id="closeBatchRequestModal" aria-label="Close">&times;</button>
          </div>

          <form id="batchRequestForm">
            <div class="modal-body">
              <input type="hidden" name="sacrament" id="batch_sacrament" value="">

              <label for="preferred_date">Preferred Date</label>
              <input type="date" name="preferred_date" id="preferred_date" required>

              <label for="preferred_time">Preferred Time</label>
              <input type="time" name="preferred_time" id="preferred_time" required>

              <label for="batch_amount">Batch amount</label>
              <select name="batch_amount" id="batch_amount" required>
                <option value="" disabled selected>- Select amount -</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
              </select>

              <p id="batchRequestFeedback" style="margin-top:10px; font-size: 0.95rem;"></p>
            </div>

            <div class="modal-footer">
              <button type="submit" class="modal-btn">Submit Request</button>
            </div>
          </form>
        </div>
      </div>
    </div>


      <h3>Baptism Date</h3>

      <form action="handle/save_baptism.php" method="post" enctype="multipart/form-data">
          
        <div class="form-row">
              <label>Baptism Type<span class="required">*</span></label> 
                <select name="baptism_type" id="baptism_type" required> 
                  <option value="" disabled selected>- Select Baptism Type-</option>
                  <option value="Regular">Batch</option>
                  <option value="Solo">Solo</option>
                </select>
        </div>

        <div class="form-row">
          <div>
            <div style="padding-top: 9px;">
            <label>Set Time<span class="required">*</span></label>
              <div id="timeButtons" class="time-buttons"></div>
                <input type="hidden" name="time" id="time_hidden" required>
              </div>
          </div>
          <div>
            
            <label class="tooltip-label">
              Set Date<span class="required">*</span>
              <span class="tooltip-icon">ⓘ
                <span class="tooltip-text" id="dateTooltip">
                  Please select a baptism type.
                </span>
              </span>
            </label>

            <div class="mini-calendar">
              <div class="calendar-header">
                <button type="button" id="prevMonth">❮</button>
                <span id="calendarMonth"></span>
                <button type="button" id="nextMonth">❯</button>
              </div>
              <div class="calendar-grid" id="calendarGrid"></div>
            </div>
            <input type="date" id="date" name="date" required hidden />
          </div>
        </div>

        <!-- CLAIMING FOR WHO? -->
        <div class="form-group centered-section">

        <div class="form-group">
          <h4 class="form-section-title" style="margin-bottom:-17px;">Who is filing the form?<span class="required">*</span> </h4>

          <div class="form-claim">
            <button type="button" class="claim-btn" name="claiming_for" value="parent"
                    onclick="toggleClaim('parent', this)">
              Parent
            </button>

            <button type="button" class="claim-btn" name="claiming_for" value="guardian"
                    onclick="toggleClaim('guardian', this)">
              Guardian/Relative
            </button>
          </div>
        </div>
        </div>
        
        <hr class="divider">

        <h3>Child</h3>

        <!-- Child Info -->
        <label>Name of Child<span class="required">*</span></label> 
        <div class="form-row">
          <input type="text" name="child_firstname" placeholder="First Name" required />
          <input type="text" name="child_middlename" placeholder="Middle Name" />
          <input type="text" name="child_lastname" placeholder="Last Name" required />
          <input type="text" name="child_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Birthdate<span class="required">*</span></label>
            <input type="date" name="birth_date" id="child_birthdate" required />
          </div>
          <div>
            <label>Birthplace<span class="required">*</span></label>
            <input type="text" name="birth_place" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Sex<span class="required">*</span></label>
            <select name="child_gender" required>
              <option value="" disabled selected>- Select Sex -</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label for="nationality">
              Nationality<span class="required">*</span>
            </label>
            <select name="nationality" id="nationality" required>
              <option value="" disabled>- Select nationality -</option>
              <option value="Afghan">Afghan</option>
              <option value="Albanian">Albanian</option>
              <option value="Algerian">Algerian</option>
              <option value="American">American</option>
              <option value="Argentine">Argentine</option>
              <option value="Australian">Australian</option>
              <option value="Austrian">Austrian</option>
              <option value="Bangladeshi">Bangladeshi</option>
              <option value="Belgian">Belgian</option>
              <option value="Brazilian">Brazilian</option>
              <option value="British">British</option>
              <option value="Canadian">Canadian</option>
              <option value="Chinese">Chinese</option>
              <option value="Colombian">Colombian</option>
              <option value="Danish">Danish</option>
              <option value="Dutch">Dutch</option>
              <option value="Egyptian">Egyptian</option>
              <option value="Filipino" selected>Filipino</option>
              <option value="Finnish">Finnish</option>
              <option value="French">French</option>
              <option value="German">German</option>
              <option value="Ghanaian">Ghanaian</option>
              <option value="Greek">Greek</option>
              <option value="Indian">Indian</option>
              <option value="Indonesian">Indonesian</option>
              <option value="Irish">Irish</option>
              <option value="Italian">Italian</option>
              <option value="Japanese">Japanese</option>
              <option value="Kenyan">Kenyan</option>
              <option value="Malaysian">Malaysian</option>
              <option value="Mexican">Mexican</option>
              <option value="Moroccan">Moroccan</option>
              <option value="Nepalese">Nepalese</option>
              <option value="New Zealander">New Zealander</option>
              <option value="Nigerian">Nigerian</option>
              <option value="Norwegian">Norwegian</option>
              <option value="Pakistani">Pakistani</option>
              <option value="Peruvian">Peruvian</option>
              <option value="Philippine">Philippine</option>
              <option value="Polish">Polish</option>
              <option value="Portuguese">Portuguese</option>
              <option value="Russian">Russian</option>
              <option value="Saudi">Saudi</option>
              <option value="Singaporean">Singaporean</option>
              <option value="South African">South African</option>
              <option value="South Korean">South Korean</option>
              <option value="Spanish">Spanish</option>
              <option value="Sri Lankan">Sri Lankan</option>
              <option value="Swedish">Swedish</option>
              <option value="Swiss">Swiss</option>
              <option value="Thai">Thai</option>
              <option value="Turkish">Turkish</option>
              <option value="Ukrainian">Ukrainian</option>
              <option value="Vietnamese">Vietnamese</option>
              <option value="Zimbabwean">Zimbabwean</option>
            </select>
          </div>


          <div class="form-row">
            <div>
              <label>Birth Certificate<span class="required">*</span></label>
              <input type="file" name="birth_certificate" accept=".jpg,.jpeg,.png,.pdf" required/>
            </div>
          </div>
        </div>

        <hr class="divider">

        <!-- GUARDIAN IS FILING -->
        <div id="claim-guardian-section" class="reservation-form-hidden" style="display:none">

        <h3>Guardian</h3>
          <label>Name of Guardian<span class="required">*</span></label><br>
          <div class="form-row">
            <input type="text" name="guardian_firstname" id="guardian_first" placeholder="First Name" required/>
            <input type="text" name="guardian_middlename" id="guardian_middle" placeholder="Middle Name" />
            <input type="text" name="guardian_lastname" id="guardian_last" placeholder="Last Name" required/>
            <input type="text" name="guardian_suffix" id="guardian_suffix" placeholder="Suffix" />
          </div><br>

          <div class="form-row">
            <div>
              <label>Relation to Child<span class="required">*</span></label>
              <select name="guardian_type" id="guardian_type" required>
                <option value="" disabled selected>Select Relation</option>
                <option value="Father">Father</option>
                <option value="Mother">Mother</option>
                <option value="Aunt">Aunt</option>
                <option value="Uncle">Uncle</option>
                <option value="Grandparent">Grandparent</option>
              </select>
            </div>
          </div>

          <br><label>Address<span class="required">*</span></label>
          <div class="form-row">
            <input type="text" name="guardian_province" placeholder="Province" required/>
            <input type="text" name="guardian_city" placeholder="City" required/>
            <input type="text" name="guardian_street_no" placeholder="Street Number" required/>
            <input type="text" name="guardian_barangay" placeholder="Barangay" required/>
          </div><br>

          <div class="form-row">
            <div>
              <label>Contact Number<span class="required">*</span></label>
              <input type="text" name="guardian_contact" id="contact_guardian" placeholder="Contact Number" required>
            </div>
            <div>
              <label>Email Address<span class="required">*</span></label>
              <input type="email" name="guardian_email" id="email_guardian" placeholder="Email Address" required />   
            </div>
          </div><br>
          
          <hr class="divider-dotted" style="border-top: 4px dotted #e5e0db; border-bottom: hidden;">
        </div>

        <h3>Parents</h3>

        <label>Name of Mother<span class="required">*</span></label>
        <div class="form-row">
          <input type="text" name="mother_firstname"  placeholder="First Name" id="mother_first"required />
          <input type="text" name="mother_middlename" id="mother_middle" placeholder="Middle Name" />
          <input type="text" name="mother_lastname" id="mother_last" placeholder="Last Name" required />
        </div>
        
        <label>Name of Father<span class="required">*</span></label>
        <div class="form-row">
          <input type="text" name="father_firstname" id="father_first" placeholder="First Name" required/>
          <input type="text" name="father_middlename" id="father_middle" placeholder="Middle Name" />
          <input type="text" name="father_lastname" id="father_last" placeholder="Last Name" required/>
          <input type="text" name="father_suffix" id="father_suffix" placeholder="Suffix" />
        </div>

        <!-- PARENT IS FILING-->
        <div id="claim-parent-section">
        
          <div class="form-row">
            <div>
              <label>Contact Number<span class="required">*</span></label>
              <input type="text" name="contact" id="contact" placeholder="Contact Number">
            </div>
            <div>
              <label>Email Address<span class="required">*</span></label>
              <input type="email" name="email" id="email" placeholder="Email Address"/>   
            </div>
          </div><br>

          <div class="form-row">
            <div>
              <label>Married?<span class="required">*</span></label>
              <select name="married" id="married">
                <option value="" disabled selected>- Yes/No -</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
              </select>
            </div>

            <div id="marriage_type_container" style="display:none;">
              <label>Marriage Type<span class="required">*</span></label>
              <select name="marriage_type" id="marriage_type">
                  <option value="" disabled selected>- Select Marriage Type-</option>
                  <option value="Church Marriage">Church Marriage</option>
                  <option value="Civil Marriage">Civil Marriage</option>
              </select>
            </div>
            
            <div id="marriage_cert_container" style="display:none;">
              <label>Marriage Certificate<span class="required">*</span></label>
              <input type="file" name="marriage_certificate" accept=".jpg,.jpeg,.png,.pdf">
            </div>
          </div>

          <div><br>
            <label>Address<span class="required">*</span></label>
            <div class="form-row">
              <input type="text" name="parent_province" placeholder="Province"/>
              <input type="text" name="parent_city" placeholder="City" required />
              <input type="text" name="parent_street_no" placeholder="Street Number"/>
              <input type="text" name="parent_barangay" placeholder="Barangay"/>
            </div>
          </div>
        </div>

        <hr class="divider">
        <h3>Primary Sponsors</h3>

        <label>Pangunahing Ninong<span class="required">*</span></label>
        <div class="form-row">
          <input type="text" name="ninong_firstname" placeholder="First Name" required/>
          <input type="text" name="ninong_middlename" placeholder="Middle Name" />
          <input type="text" name="ninong_lastname" placeholder="Last Name" required/>
          <input type="text" name="ninong_suffix" placeholder="Suffix"/>
        </div>

        <label>Pangunahing Ninang<span class="required">*</span></label>
        <div class="form-row">
          <input type="text" name="ninang_firstname" placeholder="First Name" required/>
          <input type="text" name="ninang_middlename" placeholder="Middle Name" />
          <input type="text" name="ninang_lastname" placeholder="Last Name" required/>
        </div>

        <hr class="divider">
        <h3>Additional Sponsors</h3>

        <div id="extra-ninong-wrapper">
        <div class="label-with-btn">
          <label>Ninong</label>
          <button type="button" class="add-btn" onclick="addNinong()">+</button>
        </div>
          <div class="form-row extra-ninong">
            <input type="text" name="as_ninong_firstname[]" placeholder="First Name">
            <input type="text" name="as_ninong_middlename[]" placeholder="Middle Name">
            <input type="text" name="as_ninong_lastname[]" placeholder="Last Name">
            <input type="text" name="as_ninong_suffix[]" placeholder="Suffix">
            
          </div>
        </div>
    
        <div id="extra-ninang-wrapper">
        <div class="label-with-btn">
          <label>Ninang</label>
          <button type="button" class="add-btn" onclick="addNinang()">+</button>
        </div>

        <div class="form-row extra-ninang">
          <input type="text" name="as_ninang_firstname[]" placeholder="First Name">
          <input type="text" name="as_ninang_middlename[]" placeholder="Middle Name">
          <input type="text" name="as_ninang_lastname[]" placeholder="Last Name">
        </div>
        </div>

        <!-- TERMS MODAL -->
        <div class="modal-overlay" id="termsModal">
          <div class="modal">
            <div class="modal-header">
              <h3>Terms & Conditions</h3>
              <button type="button" class="close-modal" id="closeTerms">&times;</button>
            </div>

            <div class="modal-body">
              <p>
                By requesting a certificate from Santa Krus Parish, you agree to comply
                with parish policies and guidelines.
              </p>
              <br>
              <p>
                All personal information submitted will be used solely for verification,
                documentation, and parish-related services. Your data will not be shared
                with third parties without consent.
              </p>
              <br>
              <p>
                Submitting false or misleading information may result in request denial.
              </p>
            </div>

            <div class="modal-footer">
              <button type="button" class="modal-btn" id="acceptTerms">
                I Understand
              </button>
            </div>
          </div>
        </div>

        <!-- TERMS -->
        <br><div class="terms-row">
          <input type="checkbox" id="terms" required>
          <label for="terms">
            I agree to the
            <a href="javascript:void(0)" id="openTerms">Terms & Conditions</a>
            <span class="required">*</span>
          </label>
        </div>

        <div class="text-center" id="reserve-btn">
          <button type="submit" name="submit">RESERVE</button>
        </div>
        
      </form>
    </div>
  </section>
</main>


<!-- FOOTER -->
<footer class="custom-footer">

  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Contact Number</b><br>(02) 8984-3933</p>

    <ul class="social-icons">
      <li><a href="https://www.facebook.com/DioNova.StaKrusParish"><span class="fab fa-facebook-f"></span></a></li>
    </ul>
  </div>
  <!-- footer-left was not closed before -->

  <!-- LOCATION SECTION (Center) -->
  <div class="footer-location">
    <h3>Location</h3>
    <p>205 Deparo Rd, Caloocan City, 1420 Metro Manila</p>

    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.6890401821715!2d121.00261717591935!3d14.737954572658468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7bb4e6b1b65%3A0x424ba9f332da9e86!2s205%20Deparo%20Rd%2C%20Caloocan%2C%201420%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1731985610001"
        width="100%"
        height="250"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>

  <!-- CONTACT SECTION -->
  <div class="footer-contact">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Contact Us</h3>
    <form  method="POST">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" placeholder="Message" required></textarea>
      <button type="submit">SUBMIT</button>
    </form>
  </div>

</footer>
  
  
<script>
(function () {
  const modal = document.getElementById('batchRequestModal');
  const openBtn = document.getElementById('openBatchRequestModal');
  const closeBtn = document.getElementById('closeBatchRequestModal');
  const cancelBtn = document.getElementById('cancelBatchRequest');
  const form = document.getElementById('batchRequestForm');
  const feedback = document.getElementById('batchRequestFeedback');
  const sacramentInput = document.getElementById('batch_sacrament');

  // Read the sacrament from <body data-service="First Communion">
  const sacramentFromBody = document.body.getAttribute('data-service') || 'Sacrament';

  function openModal() {
    sacramentInput.value = sacramentFromBody;
    feedback.textContent = '';
    feedback.style.color = '';
    modal.style.display = 'flex';
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    form.reset();
    sacramentInput.value = '';
  }

  openBtn?.addEventListener('click', openModal);
  closeBtn?.addEventListener('click', closeModal);
  cancelBtn?.addEventListener('click', closeModal);

  // close on overlay click
  modal?.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });

  // Submit request -> admin notification
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    feedback.textContent = 'Submitting...';
    feedback.style.color = '#333';

    try {
      const fd = new FormData(form);

      const res = await fetch('handle/request_batch_date.php', {
        method: 'POST',
        body: fd
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        feedback.textContent = data.message || 'Failed to submit request.';
        feedback.style.color = 'crimson';
        return;
      }

      feedback.textContent = 'Request submitted! The parish office has been notified.';
      feedback.style.color = 'green';

      // close after a short moment
      setTimeout(closeModal, 800);
    } catch (err) {
      feedback.textContent = 'Network error. Please try again.';
      feedback.style.color = 'crimson';
    }
  });
})();
</script>

</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const modal  = document.getElementById("termsModal");
  const open   = document.getElementById("openTerms");
  const close  = document.getElementById("closeTerms");
  const accept = document.getElementById("acceptTerms");
  const chk    = document.getElementById("terms");

  if (!modal || !open || !close || !accept) return;

  const openModal = () => modal.classList.add("show");
  const closeModal = () => modal.classList.remove("show");

  open.addEventListener("click", (e) => {
    e.preventDefault();
    openModal();
  });

  close.addEventListener("click", closeModal);

  accept.addEventListener("click", () => {
    chk.checked = true;     // optional: auto-check on accept
    closeModal();
  });

  // close when clicking outside the modal box
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });

  // ESC to close
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const service = document.body.dataset.service; // e.g., "Baptism"
  const calendarGrid  = document.getElementById("calendarGrid");
  const calendarMonth = document.getElementById("calendarMonth");
  const dateInput     = document.getElementById("date");
  const timeButtons   = document.getElementById("timeButtons");
  const timeHidden    = document.getElementById("time_hidden");
  const baptismType   = document.getElementById("baptism_type");

  let currentDate = new Date();
  let schedules = {}; // only for Regular

  // Solo fixed schedule (static)
  const soloTimes = 
  [
    "07:00","08:00","09:00","10:00","11:00",
    "12:00","13:00","14:00","15:00","16:00"
  ];

  function iso(y,m,d){
    return `${y}-${String(m+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;
  }

  async function loadSchedules(){
    schedules = {};
    const y = currentDate.getFullYear();
    const m = currentDate.getMonth()+1;

    const res = await fetch(
      `handle/calendar_control_month.php?service=${service}&year=${y}&month=${m}`
    );
    schedules = await res.json();
  }

function renderCalendar(){
    calendarGrid.innerHTML = "";

    const y = currentDate.getFullYear();
    const m = currentDate.getMonth();
    const firstDay = new Date(y,m,1).getDay();
    const days = new Date(y,m+1,0).getDate();

    calendarMonth.textContent =
      currentDate.toLocaleString("default",{month:"long",year:"numeric"});

    ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d=>{
      const el=document.createElement("div");
      el.className="calendar-weekday";
      el.textContent=d;
      calendarGrid.appendChild(el);
    });

    // Minimum allowed booking date = today + 14 days
    const today = new Date();
    const minDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 21); // block dates for 3 weeks 

    for(let i=0;i<firstDay;i++){
      calendarGrid.appendChild(document.createElement("div"));
    }

    for(let d=1; d<=days; d++){
      const key = iso(y,m,d);
      const cell = document.createElement("div");
      cell.textContent = d;
      cell.className = "calendar-day blocked";

      const cellDate = new Date(y, m, d);

      // Block past dates or before 3-week window
if (cellDate < minDate) {
  cell.className = "calendar-day blocked past";
} else {

  // SOLO logic (static, except Sundays)
  if (baptismType.value === "Solo") {

    // Block Sundays for Solo
    if (cellDate.getDay() === 0) {
      cell.className = "calendar-day blocked sunday";
    } else {
      cell.classList.remove("blocked");
      cell.classList.add("available");
    }

  }

  // REGULAR logic (DB-controlled)
  if (
    baptismType.value !== "Solo" &&
    schedules[key] &&
    schedules[key].length > 0
  ) {
    cell.classList.remove("blocked");
    cell.classList.add("available");
  }
}


      cell.onclick = () => {
        if(!cell.classList.contains("available")) return;

        document.querySelectorAll(".calendar-day").forEach(x => x.classList.remove("selected"));
        cell.classList.add("selected");

        dateInput.value = key;
        populateTimes(key);
      };

      calendarGrid.appendChild(cell);
    }
}

  
  function populateTimes(date){
    timeButtons.innerHTML="";
    timeHidden.value="";

    if(baptismType.value === "Solo"){
      soloTimes.forEach(t => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "time-btn";
        btn.textContent = toAMPM(t);

        btn.onclick = () => {
          document.querySelectorAll(".time-btn").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          timeHidden.value = t;
        };

        timeButtons.appendChild(btn);
      });
    } else {
      // Regular schedules from DB
      if(!schedules[date]) return;
      schedules[date].forEach(row => {
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "time-btn";
        btn.textContent = toAMPM(row.time);

        btn.onclick = () => {
          document.querySelectorAll(".time-btn").forEach(b => b.classList.remove("active"));
          btn.classList.add("active");
          timeHidden.value = row.time;
        };

        timeButtons.appendChild(btn);
      });
    }
  }

  function toAMPM(t){
    let[h,m]=t.split(":");
    h=parseInt(h);
    const a=h>=12?"PM":"AM";
    h=h%12||12;
    return `${h}:${m} ${a}`;
  }

  async function reload(){
    await loadSchedules();
    renderCalendar();
  }

  document.getElementById("prevMonth").onclick = async () => {
    currentDate.setMonth(currentDate.getMonth()-1);
    await reload();
  };

  document.getElementById("nextMonth").onclick = async () => {
    currentDate.setMonth(currentDate.getMonth()+1);
    await reload();
  };

  // Update times when baptism type changes
  baptismType.addEventListener("change", () => {
    const selectedDate = dateInput.value;
    renderCalendar(); // Re-render cells to apply Solo availability
    if(selectedDate) populateTimes(selectedDate);
  });

  reload();
});

</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const birthInput = document.getElementById("child_birthdate");

  birthInput.addEventListener("change", () => {
    const birthDate = new Date(birthInput.value);
    const today = new Date();

    if (birthDate > today) {
      alert("Birthdate cannot be in the future.");
      birthInput.value = "";
      return;
    }

    // Calculate age in months
    let months =
      (today.getFullYear() - birthDate.getFullYear()) * 12 +
      (today.getMonth() - birthDate.getMonth());

    if (today.getDate() < birthDate.getDate()) {
      months--;
    }

    if (months > 12) {
      alert("Only infants 1 year old and below are permitted for baptism.");
      birthInput.value = "";
    }
  });
});
</script>

<script>
function disableSection(section) {
  section.querySelectorAll("input, select").forEach(el => {
    el.value = "";
    el.disabled = true;
  });
}

function enableSection(section) {
  section.querySelectorAll("input, select").forEach(el => {
    el.disabled = false;
  });
}
</script>

<script>
function toggleClaim(type, clickedBtn) {
  const parentSection   = document.getElementById('claim-parent-section');
  const guardianSection = document.getElementById('claim-guardian-section');

  if (type === 'parent') {
    disableSection(guardianSection);
    enableSection(parentSection);

    guardianSection.style.display = 'none';
    parentSection.style.display   = 'block';
  } else {
    disableSection(parentSection);
    enableSection(guardianSection);

    parentSection.style.display   = 'none';
    guardianSection.style.display = 'block';
  }

  // Button active state
  document.querySelectorAll('.claim-btn').forEach(btn =>
    btn.classList.remove('active')
  );
  clickedBtn.classList.add('active');

  autofillBaptism(type);
}

</script>

<script>
function autofillBaptism(type) {
  const data = {
    first:   "<?= $_SESSION['givenname'] ?? '' ?>",
    middle:  "<?= $_SESSION['middlename'] ?? '' ?>",
    last:    "<?= $_SESSION['lastname'] ?? '' ?>",
    suffix:  "<?= $_SESSION['suffix'] ?? '' ?>",
    contact: "<?= $_SESSION['contact'] ?? '' ?>",
    email:   "<?= $_SESSION['email'] ?? '' ?>",
    gender:  "<?= $_SESSION['gender'] ?? '' ?>"
  };

  /* ================= PARENT ================= */
  if (type === "parent") {

    // Contact info (shared)
    document.querySelector('#claim-parent-section #contact').value = data.contact;
    document.querySelector('#claim-parent-section #email').value   = data.email;

    // Gender-based fill
    if (data.gender === "Female") {
      document.querySelector('#claim-parent-section #mother_first').value  = data.first;
      document.querySelector('#claim-parent-section #mother_middle').value = data.middle;
      document.querySelector('#claim-parent-section #mother_last').value   = data.last;
    }

    if (data.gender === "Male") {
      document.querySelector('#claim-parent-section #father_first').value  = data.first;
      document.querySelector('#claim-parent-section #father_middle').value = data.middle;
      document.querySelector('#claim-parent-section #father_last').value   = data.last;
      document.querySelector('#claim-parent-section #father_suffix').value = data.suffix;
    }
  }

  /* ================= GUARDIAN ================= */
  if (type === "guardian") {

    document.querySelector('#claim-guardian-section #guardian_first').value  = data.first;
    document.querySelector('#claim-guardian-section #guardian_middle').value = data.middle;
    document.querySelector('#claim-guardian-section #guardian_last').value   = data.last;
    document.querySelector('#claim-guardian-section #guardian_suffix').value = data.suffix;

    document.querySelector('#claim-guardian-section #contact_guardian').value = data.contact;
    document.querySelector('#claim-guardian-section #email_guardian').value   = data.email;
  }
}

document.getElementById('married').addEventListener('change', function() {
    let marriageTypeDiv = document.getElementById('marriage_type_container');
    let marriageTypeSelect = document.getElementById('marriage_type');
    let marriageCertDiv = document.getElementById('marriage_cert_container');
    let marriageCertSelect = document.getElementById('marriage_certificate');

    if (this.value === "Yes") {
        marriageTypeDiv.style.display = "block";
        marriageTypeSelect.setAttribute("required", "required");
        
        marriageCertDiv.style.display = "block";
        marriageCertSelect.setAttribute("required", "required");
    } else {
        marriageTypeDiv.style.display = "none";
        marriageTypeSelect.removeAttribute("required");
        marriageTypeSelect.value = "";

        marriageCertDiv.style.display = "none";
        marriageCertSelect.removeAttribute("required");
        marriageCertSelect.value = "";
    }
});
</script>

<script>
function addNinong() {
  const wrapper = document.getElementById("extra-ninong-wrapper");
  const div = document.createElement("div");
  div.className = "form-row extra-ninong";

  div.innerHTML = `
    <input type="text" name="as_ninong_firstname[]" placeholder="First Name" style="margin-top:10px;">
    <input type="text" name="as_ninong_middlename[]" placeholder="Middle Name" style="margin-top:10px;">
    <input type="text" name="as_ninong_lastname[]" placeholder="Last Name" style="margin-top:10px;">
    <input type="text" name="as_ninong_suffix[]" placeholder="Suffix" style="margin-top:10px;">
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()" style="margin-top:10px;">−</button>
  `;

  wrapper.appendChild(div);
}

function addNinang() {
  const wrapper = document.getElementById("extra-ninang-wrapper");
  const div = document.createElement("div");
  div.className = "form-row extra-ninang";

  div.innerHTML = `
    <input type="text" name="as_ninang_firstname[]" placeholder="First Name" style="margin-top:10px;">
    <input type="text" name="as_ninang_middlename[]" placeholder="Middle Name" style="margin-top:10px;">
    <input type="text" name="as_ninang_lastname[]" placeholder="Last Name" style="margin-top:10px;">
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()" style="margin-top:10px;">−</button>
  `;

  wrapper.appendChild(div);
}
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const baptismType = document.getElementById("baptism_type"); // your select
  const tooltipText = document.getElementById("dateTooltip");

  function updateWeddingTooltip() {

    if (!baptismType.value) {
      tooltipText.innerHTML = "Please select a baptism type first.";
      return;
    }

    if (baptismType.value === "Solo") {
      tooltipText.innerHTML = `
        Solo Baptism Rules:
        <br>• Please select any day <b>except Sundays</b>
        <br>• Contact us if you would like to baptize more than one child. 
      `;
    }

    if (baptismType.value === "Regular") {
      tooltipText.innerHTML = `
        Batch Baptism Rules:
        <br>• Schedule for batch baptism is set beforehand.</b>
        <br>• Please see <a href="http://localhost/stkportal_src/src/public/announcements.php" style="color:white">announcements</a> for any announced <i>binyagang bayan</i></b>
      `;
    }
  }

  baptismType.addEventListener("change", updateWeddingTooltip);

  // Run once on load if already selected
  updateWeddingTooltip();
});
</script>
