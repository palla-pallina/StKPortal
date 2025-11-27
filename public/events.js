document.getElementById('year').textContent = new Date().getFullYear();
document.querySelectorAll('.calendar-header .chev').forEach(btn => {
  btn.addEventListener('click', () => {
  });
});

(function() {
  const IC_NOTES = {
    "2025-04-13": "Palm Sunday — celebrates Jesus’s triumphant entry into Jerusalem.",
    "2025-04-17": "Maundy Thursday — commemorates the Last Supper and the washing of feet.",
    "2025-04-18": "Good Friday — observes the crucifixion of Jesus.",
    "2025-04-19": "Holy Saturday — Jesus’ body rests in the tomb.",
    "2025-04-20": "Easter Sunday — celebrates the resurrection of Jesus."
  };

  const state = {
    year: 2025,
    month: 3,   // 0-indexed (3 = April)
    selected: null
  };

  const daysEl   = document.getElementById('ic-days');
  const monthEl  = document.getElementById('ic-month');
  const prevBtn  = document.getElementById('ic-prev');
  const nextBtn  = document.getElementById('ic-next');
  const titleEl  = document.getElementById('ic-title');
  const textEl   = document.getElementById('ic-text');

  const pad = n => String(n).padStart(2, '0');
  const iso = (y,m,d) => `${y}-${pad(m+1)}-${pad(d)}`;
  const monthLabel = (m,y) => new Intl.DateTimeFormat(undefined, { month: 'long', year: 'numeric' })
                                 .format(new Date(y, m, 1))
                                 .replace(/\u00A0/g, ' ');

  function daysWithNotesForMonth(y, m) {
    const set = new Set();
    Object.keys(IC_NOTES).forEach(k => {
      const [Y, M, D] = k.split('-').map(Number);
      if (Y === y && (M - 1) === m) set.add(D);
    });
    return set;
  }

  function render() {
    const { year, month, selected } = state;

    monthEl.textContent = monthLabel(month, year).split(' ')[0];

    daysEl.innerHTML = '';
    const firstDow = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const noted = daysWithNotesForMonth(year, month);

    for (let i = 0; i < firstDow; i++) {
      const blank = document.createElement('li');
      blank.className = 'muted';
      blank.setAttribute('aria-hidden', 'true');
      daysEl.appendChild(blank);
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const li = document.createElement('li');
      li.textContent = d;
      li.setAttribute('role', 'button');
      li.setAttribute('aria-label', `${monthLabel(month, year)} ${d}`);
      if (noted.has(d)) li.classList.add('has-note');
      if (selected === d) li.classList.add('selected');
      li.addEventListener('click', () => onDayClick(d));
      daysEl.appendChild(li);
    }
  }

  function onDayClick(day) {
    const { year, month } = state;
    const key = iso(year, month, day);
    const text = IC_NOTES[key];

    if (state.selected === day) {
      state.selected = null;
      titleEl.textContent = 'Select a date';
      textEl.textContent  = 'Click any highlighted day to see details here.';
    } else {
      state.selected = day;
      titleEl.textContent = monthLabel(month, year) + ' ' + day;
      textEl.textContent  = text || 'No event for this date.';
    }
    render();
  }

  prevBtn.addEventListener('click', () => {
    state.month -= 1;
    if (state.month < 0) { state.month = 11; state.year -= 1; }
    state.selected = null;
    titleEl.textContent = 'Select a date';
    textEl.textContent  = 'Click any highlighted day to see details here.';
    render();
  });

  nextBtn.addEventListener('click', () => {
    state.month += 1;
    if (state.month > 11) { state.month = 0; state.year += 1; }
    state.selected = null;
    titleEl.textContent = 'Select a date';
    textEl.textContent  = 'Click any highlighted day to see details here.';
    render();
  });

  render();
})();