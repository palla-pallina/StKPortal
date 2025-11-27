<h2>Reservation Details</h2>
<div class="row">
    <div class="col-md-2">
        <b>Baptism Type</b>
        <select name="baptism_type" id="baptism_type" class="form-control" required>
            <option value="" disabled selected></option>
            <option value="regular">Regular</option>
            <option value="solo">Solo</option>
        </select>
    </div>

    <div class="col-md-2">
        <b>Baptism Date</b>
        <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>

    <div class="col-md-2">
        <b>Baptism Time</b>
        <input type="time" name="time" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>

    <div class="col-md-3">
        <b>Contact Number</b>
        <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>

    <div class="col-md-3">
        <b>Email Address</b>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>
</div>

<br>
<hr class="dividerform" />

<h2>Child Details</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="child_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="child_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="child_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Suffix</b>
        <input type="text" name="child_suffix" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>
</div>

<br>

<div class="row">
    <div class="col-md-2">
        <b>Gender</b>
        <select name="gender" class="form-control" required>
            <option value="" disabled selected>Sex</option>
            <option value="" disabled <?= empty($user->gender) ? 'selected' : '' ?>>Male</option>
            <option value="" disabled <?= empty($user->gender) ? 'selected' : '' ?>>Female</option>
        </select>
    </div>

    <div class="col-md-2">
        <b>Birthday</b>
        <input type="date" name="birth_date" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>

    <div class="col-md-5">
        <b>Birth Place</b>
        <input type="text" name="birth_place" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>

    <div class="col-md-3">
        <b>Nationality</b>
        <input type="text" name="child_nat" class="form-control" value="<?php echo htmlspecialchars($user->thing); ?>" required>
    </div>
</div>

<br>
<hr class="dividerform" />

<h2>Father Details</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="father_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="father_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="father_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Suffix</b>
        <input type="text" name="father_suffix" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>
</div>

<h2>Mother Details</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="mother_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="mother_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="mother_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>
</div>

<br>
<div class="row">
    <div class="col-md-2">
        <b>Marriage Type</b>
        <select name="marriage_type" id="marriage_type" class="form-control">
            <option value="" disabled selected></option>
            <option value="Single" <?= ($user->marriage_type ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
            <option value="Church Marriage" <?= ($user->marriage_type ?? '') === 'Church Marriage' ? 'selected' : '' ?>>Church Marriage</option>
            <option value="Civil Marriage" <?= ($user->marriage_type ?? '') === 'Civil Marriage' ? 'selected' : '' ?>>Civil Marriage</option>
        </select>
    </div>
    <div class="col-md-5">
        <b>Address</b>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>
</div>

<br>
<hr class="dividerform" />

<h2>Pangunahing Ninong</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="ninong_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="ninong_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="ninong_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Suffix</b>
        <input type="text" name="ninong_suffix" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>
</div>

<h2>Pangunahing Ninang</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="ninang_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="ninang_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="ninang_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>
</div>


<br>
<hr class="dividerform" />

<h2>Ninong</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="as_ninong_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="as_ninong_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="as_ninong_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Suffix</b>
        <input type="text" name="as_ninong_suffix" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>
</div>

<h2>Ninang</h2>

<div class="row">
    <div class="col-md-3">
        <b>Given Name</b>
        <input type="text" name="as_ninang_firstname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>

    <div class="col-md-3">
        <b>Middle Name</b>
        <input type="text" name="as_ninang_middlename" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control">
    </div>

    <div class="col-md-3">
        <b>Last Name</b>
        <input type="text" name="as_ninang_lastname" value="<?php echo htmlspecialchars($user->thing); ?>" class="form-control" required>
    </div>
</div>