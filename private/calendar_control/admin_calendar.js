document.addEventListener("DOMContentLoaded", () => {
  const calendarGrid    = document.getElementById("calendarGrid");
  const calendarMonth   = document.getElementById("calendarMonth");
  const prevBtn         = document.getElementById("prevMonth");
  const nextBtn         = document.getElementById("nextMonth");
  const filterSacrament = document.getElementById("filterSacrament");
  const scheduleModal   = document.getElementById("scheduleModal");
  const scheduleForm    = document.getElementById("scheduleForm");

  let currentDate = new Date();
  let schedules   = {};

  function iso(y,m,d){ return `${y}-${String(m+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`; }

  async function loadSchedules(){
    const filter = filterSacrament.value || 'All';
    const res = await fetch(`admin_get_calendar.php?filter=${filter}`);
    schedules = await res.json();
  }

  function renderCalendar(){
    calendarGrid.innerHTML = "";
    const y = currentDate.getFullYear();
    const m = currentDate.getMonth();
    const firstDay = new Date(y,m,1).getDay();
    const days = new Date(y,m+1,0).getDate();

    calendarMonth.textContent = currentDate.toLocaleString("default",{month:"long", year:"numeric"});
    ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d=>{
      const el = document.createElement("div");
      el.className = "calendar-weekday";
      el.textContent = d;
      calendarGrid.appendChild(el);
    });

    for(let i=0;i<firstDay;i++) calendarGrid.appendChild(document.createElement("div"));

    for(let d=1; d<=days; d++){
      const key = iso(y,m,d);
      const cell = document.createElement("div");
      cell.textContent = d;
      cell.className = "calendar-day";
      if(schedules[key] && schedules[key].length) cell.classList.add("has-schedule");
      cell.onclick = () => openScheduleModal(key);
      calendarGrid.appendChild(cell);
    }
  }

    function openScheduleModal(date){
    scheduleForm.reset();
    document.getElementById("schedule_id").value = "";
    document.getElementById("schedule_date").value = date;
    scheduleModal.style.display = "flex";

    const container = document.getElementById("existingSchedules");
    container.innerHTML = "";

    if(schedules[date]){
        schedules[date].forEach(s => {
        const div = document.createElement("div");
        div.style.marginBottom = "5px";
        div.innerHTML = `
            ${s.time || "N/A"} - ${s.service_type} (${s.type || "N/A"}) - ${s.is_enabled ? "Enabled" : "Disabled"} - ${s.note || ""}
            <button data-id="${s.id}" class="edit-btn">Edit</button>
            <button data-id="${s.id}" class="delete-btn">Delete</button>
        `;
        container.appendChild(div);

        // Edit button
        div.querySelector(".edit-btn").onclick = () => {
            document.getElementById("schedule_id").value = s.id;
            document.getElementById("schedule_date").value = s.date;
            document.getElementById("schedule_time").value = s.time || "";
            document.getElementById("schedule_service_type").value = s.service_type;
            document.getElementById("schedule_type").value = s.type || "Regular";
            document.getElementById("schedule_max_slots").value = s.max_slots || 15;
            document.getElementById("schedule_is_enabled").value = s.is_enabled ? "1" : "0";
            document.getElementById("schedule_note").value = s.note || "";
        };

        // Delete button
        div.querySelector(".delete-btn").onclick = async () => {
            if(confirm("Delete this schedule?")){
            const res = await fetch("handle/admin_delete_calendar.php",{
                method: "POST",
                headers: { "Content-Type":"application/json" },
                body: JSON.stringify({ id: s.id })
            });
            const data = await res.json();
            if(data.success){
                await reloadCalendar();
                openScheduleModal(date); // refresh modal
            } else {
                alert("Delete failed: " + data.message);
            }
            }
        };
        });
    }
    }


  window.closeScheduleModal = () => scheduleModal.style.display = "none";

  scheduleForm.addEventListener("submit", async e => {
    e.preventDefault();
    const formData = new FormData(scheduleForm);
    const res = await fetch("admin_save_calendar.php",{
      method: "POST",
      body: formData
    });
    const data = await res.json();
    if(data.success){
      closeScheduleModal();
      await reloadCalendar();
    } else {
      alert("Failed: " + data.message);
    }
  });

  async function reloadCalendar(){
    await loadSchedules();
    renderCalendar();
  }

  prevBtn.onclick = async () => { currentDate.setMonth(currentDate.getMonth()-1); await reloadCalendar(); };
  nextBtn.onclick = async () => { currentDate.setMonth(currentDate.getMonth()+1); await reloadCalendar(); };
  filterSacrament.onchange = reloadCalendar;

  reloadCalendar();
});
