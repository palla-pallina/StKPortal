<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DELETE (POST with ?delete=1&type=...&id=...)
    if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);
        if ($type === 'wedding') {
            $stmt = $conn->prepare("DELETE FROM weddings WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'funeral') {
            $stmt = $conn->prepare("DELETE FROM funerals WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("DELETE FROM baptisms WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        // Return OK (empty response)
        http_response_code(200);
        exit;
    }

    // EDIT (POST with ?edit=1&type=...&id=...)
    if (isset($_GET['edit']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);

        if ($type === 'wedding') {
            $stmt = $conn->prepare("
                UPDATE weddings SET
                    bride_firstname=?, bride_middlename=?, bride_lastname=?, bride_suffix=?,
                    groom_firstname=?, groom_middlename=?, groom_lastname=?, groom_suffix=?,
                    best_man__firstname=?, best_man__middlename=?, best_man__lastname=?, best_man__suffix=?,
                    maid_of_honor_firstname=?, maid_of_honor_middlename=?, maid_of_honor_lastname=?, maid_of_honor_suffix=?,
                    phone=?, date=?, time=?
                WHERE id=?
            ");
            $stmt->bind_param("sssssssssssssssssssi",
                $_POST['BrideFN'], $_POST['BrideMN'], $_POST['BrideLN'], $_POST['BrideSuffix'],
                $_POST['GroomFN'], $_POST['GroomMN'], $_POST['GroomLN'], $_POST['GroomSuffix'],
                $_POST['Best_Man_FN'], $_POST['Best_Man_MN'], $_POST['Best_Man_LN'], $_POST['Best_Man_Suffix'],
                $_POST['Maid_Of_HonorFN'], $_POST['Maid_Of_HonorMN'], $_POST['Maid_Of_HonorLN'], $_POST['Maid_Of_HonorSuffix'],
                $_POST['Phone_Number'], $_POST['Date'], $_POST['Time'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'funeral') {
            // Ensure cause_of_death column exists in DB
            $stmt = $conn->prepare("
                UPDATE funerals SET
                    deceased_firstname=?, deceased_middlename=?, deceased_lastname=?, deceased_suffix=?,
                    contact_person_firstname=?, contact_person_middlename=?, contact_person_lastname=?, contact_person_suffix=?,
                    contact_person_contact_no=?, cause_of_death=?, funeral_address=?, funeral_date=?, funeral_time=?, presider=?
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssssssssi",
                $_POST['DeceasedFN'], $_POST['DeceasedMN'], $_POST['DeceasedLN'], $_POST['DeceasedSuffix'],
                $_POST['ContactFN'], $_POST['ContactMN'], $_POST['ContactLN'], $_POST['ContactSuffix'],
                $_POST['Phone_Number'], $_POST['Cause_of_Death'], $_POST['Address'], $_POST['Date'], $_POST['Time'],
                $_POST['Presider'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("
                UPDATE baptisms SET
                    child_firstname=?, child_middlename=?, child_lastname=?, child_suffix=?,
                    mother_firstname=?, mother_middlename=?, mother_lastname=?, mother_suffix=?,
                    father_firstname=?, father_middlename=?, father_lastname=?, father_suffix=?,
                    contact=?, date=?, time=?
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssssssssi",
                $_POST['ChildFN'], $_POST['ChildMN'], $_POST['ChildLN'], $_POST['ChildSuffix'],
                $_POST['MotherFN'], $_POST['MotherMN'], $_POST['MotherLN'], $_POST['MotherSuffix'],
                $_POST['FatherFN'], $_POST['FatherMN'], $_POST['FatherLN'], $_POST['FatherSuffix'],
                $_POST['Contact'], $_POST['Date'], $_POST['Time'], $id
            );
            $stmt->execute();
            $stmt->close();
        }

        // Return OK (empty response)
        http_response_code(200);
        exit;
    }

    http_response_code(200);
    exit;
}

    // CREATE (regular POST from modal form, type in form hidden input)
    $type = $_POST['type'] ?? '';

    if ($type === 'wedding') {
        // collect and default optional fields
        $bride_firstname = $_POST['BrideFN'] ?? '';
        $bride_middlename = $_POST['BrideMN'] ?? '';
        $bride_lastname = $_POST['BrideLN'] ?? '';
        $bride_suffix = $_POST['BrideSuffix'] ?? '';
        $groom_firstname = $_POST['GroomFN'] ?? '';
        $groom_middlename = $_POST['GroomMN'] ?? '';
        $groom_lastname = $_POST['GroomLN'] ?? '';
        $groom_suffix = $_POST['GroomSuffix'] ?? '';
        $best_man_firstname = $_POST['Best_Man_FN'] ?? '';
        $best_man_middlename = $_POST['Best_Man_MN'] ?? '';
        $best_man_lastname = $_POST['Best_Man_LN'] ?? '';
        $best_man_suffix = $_POST['Best_Man_Suffix'] ?? '';
        $maid_of_honor_firstname = $_POST['Maid_Of_HonorFN'] ?? '';
        $maid_of_honor_middlename = $_POST['Maid_Of_HonorMN'] ?? '';
        $maid_of_honor_lastname = $_POST['Maid_Of_HonorLN'] ?? '';
        $maid_of_honor_suffix = $_POST['Maid_Of_HonorSuffix'] ?? '';
        $phone = $_POST['Phone_Number'] ?? '';
        $date = $_POST['Date'] ?? null;
        $time = $_POST['Time'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO weddings (
                bride_firstname, bride_middlename, bride_lastname, bride_suffix,
                groom_firstname, groom_middlename, groom_lastname, groom_suffix,
                best_man_firstname, best_man_middlename, best_man_lastname, best_man_suffix,        
                maid_of_honor_firstname, maid_of_honor_middlename, maid_of_honor_lastname, maid_of_honor_suffix,
                phone, date, time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssssssssssssss",
            $bride_firstname, $bride_middlename, $bride_lastname, $bride_suffix,
            $groom_firstname, $groom_middlename, $groom_lastname, $groom_suffix,
            $best_man_firstname, $best_man_middlename, $best_man_lastname, $best_man_suffix,        
            $maid_of_honor_firstname, $maid_of_honor_middlename, $maid_of_honor_lastname, $maid_of_honor_suffix,
            $phone, $date, $time
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Wedding reservation saved.';
    } elseif ($type === 'funeral') {
        $deceased_firstname = $_POST['DeceasedFN'] ?? '';
        $deceased_middlename = $_POST['DeceasedMN'] ?? '';
        $deceased_lastname = $_POST['DeceasedLN'] ?? '';
        $deceased_suffix = $_POST['DeceasedSuffix'] ?? '';

        $contact_person_firstname = $_POST['ContactFN'] ?? '';
        $contact_person_middlename = $_POST['ContactMN'] ?? '';
        $contact_person_lastname = $_POST['ContactLN'] ?? '';
        $contact_person_suffix = $_POST['ContactSuffix'] ?? '';
        $contact_person_contact_no = $_POST['Phone_Number'] ?? '';

        $cause = $_POST['Cause_of_Death'] ?? '';
        $funeral_address = $_POST['Address'] ?? '';
        $funeral_date = $_POST['Date'] ?? null;
        $funeral_time = $_POST['Time'] ?? null;
        $presider = $_POST['Presider'] ?? '';

        $stmt = $conn->prepare("
            INSERT INTO funerals (
                deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix,
                contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix, contact_person_contact_no,
                cause_of_death, funeral_address, funeral_date, funeral_time, presider
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssssssssss",
            $deceased_firstname, $deceased_middlename, $deceased_lastname, $deceased_suffix,
            $contact_person_firstname, $contact_person_middlename, $contact_person_lastname, $contact_person_suffix, $contact_person_contact_no,
            $cause, $funeral_address, $funeral_date, $funeral_time, $presider
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Funeral reservation saved.';
    } elseif ($type === 'baptism') {
        $child_firstname = $_POST['ChildFN'] ?? '';
        $child_middlename = $_POST['ChildMN'] ?? '';
        $child_lastname = $_POST['ChildLN'] ?? '';
        $child_suffix = $_POST['ChildSuffix'] ?? '';

        $mother_firstname = $_POST['MotherFN'] ?? '';
        $mother_middlename = $_POST['MotherMN'] ?? '';
        $mother_lastname = $_POST['MotherLN'] ?? '';
        $mother_suffix = $_POST['MotherSuffix'] ?? '';

        $father_firstname = $_POST['FatherFN'] ?? '';
        $father_middlename = $_POST['FatherMN'] ?? '';
        $father_lastname = $_POST['FatherLN'] ?? '';
        $father_suffix = $_POST['FatherSuffix'] ?? '';

        $contact = $_POST['Contact'] ?? '';
        $date = $_POST['Date'] ?? null;
        $time = $_POST['Time'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO baptisms (
                child_firstname, child_middlename, child_lastname, child_suffix,
                mother_firstname, mother_middlename, mother_lastname, mother_suffix,
                father_firstname, father_middlename, father_lastname, father_suffix,
                contact, date, time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssssssssss",
            $child_firstname, $child_middlename, $child_lastname, $child_suffix,
            $mother_firstname, $mother_middlename, $mother_lastname, $mother_suffix,
            $father_firstname, $father_middlename, $father_lastname, $father_suffix,
            $contact, $date, $time
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Baptism reservation saved.';
    }

    // Done handling POST create/edit/delete — return JSON or redirect as desired.
    // For the AJAX form we use fetch and expect a plain response — we'll just exit.
    exit;

// ---------- FETCH (AJAX) ----------
if (isset($_GET['fetch']) && isset($_GET['type'])) {
    $type = $_GET['type'];
    $data = [];

    // optional single record fetch
    $singleId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($type === 'wedding') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM weddings WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            $result = $conn->query("SELECT * FROM weddings ORDER BY date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    } elseif ($type === 'funeral') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM funerals WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            // note: order by funeral_date
            $result = $conn->query("SELECT * FROM funerals ORDER BY funeral_date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    } elseif ($type === 'baptism') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM baptisms WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            $result = $conn->query("SELECT * FROM baptisms ORDER BY date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>


<script>
    function buildForm(type,data={}){
  formFields.innerHTML="";
  const hiddenType=document.createElement('input');
  hiddenType.type="hidden"; hiddenType.name="type"; hiddenType.value=type;
  formFields.appendChild(hiddenType);

  const fields = type==='wedding'?[
    {label:"Bride First Name",name:"BrideFN"},{label:"Bride Middle Name",name:"BrideMN"},{label:"Bride Last Name",name:"BrideLN"},{label:"Bride Suffix",name:"BrideSuffix"},
    {label:"Groom First Name",name:"GroomFN"},{label:"Groom Middle Name",name:"GroomMN"},{label:"Groom Last Name",name:"GroomLN"},{label:"Groom Suffix",name:"GroomSuffix"},
    {label:"Best Man First Name",name:"Best_Man_FN"},{label:"Best Man Middle Name",name:"Best_Man_MN"},{label:"Best Man Last Name",name:"Best_Man_LN"},{label:"Best Man Suffix",name:"Best_Man_Suffix"},
    {label:"Phone Number",name:"Phone_Number"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"},{label:"Maid of Honor",name:"Maid_of_Honor"}
  ]:type==='funeral'?[
    {label:"Deceased First Name",name:"DeceasedFN"},{label:"Deceased Middle Name",name:"DeceasedMN"},{label:"Deceased Last Name",name:"DeceasedLN"},{label:"Deceased Suffix",name:"DeceasedSuffix"},
    {label:"Contact First Name",name:"ContactFN"},{label:"Contact Middle Name",name:"ContactMN"},{label:"Contact Last Name",name:"ContactLN"},{label:"Contact Suffix",name:"ContactSuffix"},
    {label:"Phone Number",name:"Phone_Number"},{label:"Cause of Death",name:"Cause_of_Death"},{label:"Address",name:"Address"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"},{label:"Presider",name:"Presider"}
  ]:[
    {label:"Child First Name",name:"ChildFN"},{label:"Child Middle Name",name:"ChildMN"},{label:"Child Last Name",name:"ChildLN"},{label:"Child Suffix",name:"ChildSuffix"},
    {label:"Mother First Name",name:"MotherFN"},{label:"Mother Middle Name",name:"MotherMN"},{label:"Mother Last Name",name:"MotherLN"},{label:"Mother Suffix",name:"MotherSuffix"},
    {label:"Father First Name",name:"FatherFN"},{label:"Father Middle Name",name:"FatherMN"},{label:"Father Last Name",name:"FatherLN"},{label:"Father Suffix",name:"FatherSuffix"},
    {label:"Contact",name:"Contact"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"}
  ];

  const mapping = type==='wedding'?{
    "BrideFN":"bride_firstname","BrideMN":"bride_middlename","BrideLN":"bride_lastname","BrideSuffix":"bride_suffix",
    "GroomFN":"groom_firstname","GroomMN":"groom_middlename","GroomLN":"groom_lastname","GroomSuffix":"groom_suffix",
    "Best_Man_FN":"best_man_firstname","Best_Man_MN":"best_man_middlename","Best_Man_LN":"best_man_lastname","Best_Man_Suffix":"best_man_suffix",            
    "Maid_Of_HonorFN":"maid_of_honor_firstname","Maid_Of_HonorMN":"maid_of_honor_middlename","Maid_Of_HonorLN":"maid_of_honor_lastname","Maid_Of_HonorSuffix":"maid_of_honor_suffix",
    "Phone_Number":"phone","Date":"date","Time":"time"
  }:type==='funeral'?{
    "DeceasedFN":"deceased_firstname","DeceasedMN":"deceased_middlename","DeceasedLN":"deceased_lastname","DeceasedSuffix":"deceased_suffix",
    "ContactFN":"contact_person_firstname","ContactMN":"contact_person_middlename","ContactLN":"contact_person_lastname","ContactSuffix":"contact_person_suffix",
    "Phone_Number":"contact_person_contact_no","Cause_of_Death":"cause_of_death","Address":"funeral_address",
    "Date":"funeral_date","Time":"funeral_time","Presider":"presider"
  }:{
    "ChildFN":"child_firstname","ChildMN":"child_middlename","ChildLN":"child_lastname","ChildSuffix":"child_suffix",
    "MotherFN":"mother_firstname","MotherMN":"mother_middlename","MotherLN":"mother_lastname","MotherSuffix":"mother_suffix",
    "FatherFN":"father_firstname","FatherMN":"father_middlename","FatherLN":"father_lastname","FatherSuffix":"father_suffix",
    "Phone_Number":"phone","Date":"date","Time":"time"
  };

  fields.forEach(field=>{
    const wrapper=document.createElement('div');
    wrapper.className="form-field";
    const label=document.createElement('label');
    label.textContent=field.label;
    wrapper.appendChild(label);

    const input=document.createElement('input');
    input.type=field.type||"text";
    input.name=field.name;
    input.placeholder=field.label;
    input.required=false;

    const dbKey = mapping[field.name];
    if(data && dbKey && data[dbKey]) input.value=data[dbKey];

    wrapper.appendChild(input);
    formFields.appendChild(wrapper);
  });

  const submitBtn=document.createElement('button');
  submitBtn.type="submit";
  submitBtn.textContent="Submit";
  formFields.appendChild(submitBtn);
}

/* ---------- Form submit via fetch ---------- */
formFields.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(formFields);
  let url = '';
  if (formFields.dataset.editId) {
    url = `?edit=1&type=${formFields.dataset.editType}&id=${formFields.dataset.editId}`;
    formData.append('edit', '1');
  }
  fetch(url, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // response may be empty; re-render table and close modal
    alert('Reservation saved successfully!');
    modal.style.display = 'none';
    renderTable(eventTypeSelect.value);
    formFields.dataset.editId = '';
    formFields.dataset.editType = '';
  })
  .catch(error => {
    console.error('Save error:', error);
    alert('Error saving reservation.');
  });
});

</script>