// --------------------- API WRAPPER -------------------------
const api = (q, method='GET', body=null) => {
  if(method==='GET') return fetch(`api.php?${q}`).then(r=>r.json());
  return fetch('api.php', {method:'POST', body: body instanceof FormData ? body : new URLSearchParams(body)}).then(r=>r.json());
};

// --------------------- ELEMENTS ----------------------------
const yearSelect = document.getElementById('yearSelect');
const refreshBtn = document.getElementById('refreshBtn');
const attendanceCanvas = document.getElementById('attendanceChart').getContext('2d');
const leadsCanvas = document.getElementById('leadsChart').getContext('2d');
const searchAttendance = document.getElementById('searchAttendance');
const searchLeads = document.getElementById('searchLeads');
const kpiEventsContainer = document.getElementById('kpiEventsContainer');

let attendanceChart = null;
let leadsChart = null;

// --------------------- HELPERS ---------------------------
function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
function showModal(el){ hideAllModals(); el.hidden=false; el.style.display='block'; }
function hideAllModals(){ document.querySelectorAll('.modal').forEach(m=>{m.hidden=true;m.style.display='none';}); }

// --------------------- YEAR DROPDOWN ------------------------
function fillYearOptions(){
  const current = new Date().getFullYear();
  yearSelect.innerHTML = '';
  const allOpt = document.createElement('option'); allOpt.value = 0; allOpt.textContent = 'All Time'; yearSelect.appendChild(allOpt);
  for(let y=current;y>=current-5;y--){
    const opt=document.createElement('option'); opt.value=y; opt.textContent=y; yearSelect.appendChild(opt);
  }
  yearSelect.value=current;
}

// --------------------- REFRESH ----------------------------
async function refreshAll(){
  const year = parseInt(yearSelect.value);
  await Promise.all([
    drawAttendance(year),
    drawLeads(year),
    loadTables(year),
    updateKPIs(year),
    updateEventCount(year)
  ]);
}

// --------------------- ATTENDANCE CHART --------------------
async function drawAttendance(year){
  const chartYear = year || new Date().getFullYear();
  const res = await api(`action=getAttendanceMonthly&year=${chartYear}`);
  if(res.status!=='ok') return;
  const months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const data=res.data.map(v=>v||0);

  if(attendanceChart) attendanceChart.destroy();
  attendanceChart = new Chart(attendanceCanvas, {
    type:'bar',
    data:{labels: months, datasets:[{label:`Attendance ${year===0?'All Time':year}`,data,backgroundColor:'rgba(44,123,234,0.45)'}]},
    options:{
      responsive:true,
      plugins:{legend:{display:false},tooltip:{callbacks:{title:ctx=>`Month: ${ctx[0].label}`, label:ctx=>`Attendance: ${ctx.raw}`}} },
      scales:{ y:{beginAtZero:true,title:{display:true,text:'People'}}, x:{title:{display:true,text:'Month'}} },
      onClick: async (evt, activeEls) => {
        if(activeEls.length){
          const idx = activeEls[0].index;
          const monthNum = idx+1;
          const infoRes = await api(`action=getAttendanceRows&limit=500`);
          if(infoRes.status==='ok'){
            const rows = infoRes.data.filter(r => new Date(r.attendance_date).getMonth()+1 === monthNum && (year===0 || new Date(r.attendance_date).getFullYear()===year));
            if(rows.length){
              let details = rows.map(r=>{
                const dateObj = new Date(r.attendance_date);
                const formatted = dateObj.toLocaleDateString('en-GB', {day:'numeric', month:'long', year:'numeric'});
                return `${formatted} — ${r.section} — ${r.attendance_count} (${r.event_name||'No Event'})`;
              }).join('\n');
              alert(`Attendance details for ${months[idx]} ${chartYear}:\n\n${details}`);
            }
          }
        }
      }
    }
  });
}

// --------------------- LEADS CHART ------------------------
async function drawLeads(year){
  const chartYear = year || new Date().getFullYear();
  const res = await api(`action=getLeadsMonthly&year=${chartYear}`);
  if(res.status!=='ok') return;
  const months=['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  const data=res.data.map(v=>v||0);

  if(leadsChart) leadsChart.destroy();
  leadsChart = new Chart(leadsCanvas,{
    type:'line',
    data:{labels: months, datasets:[{label:`Leads ${year===0?'All Time':year}`,data,fill:true,borderColor:'#ff7d00',backgroundColor:'rgba(255,125,0,0.2)',tension:0.3,pointRadius:5,pointHoverRadius:7}]},
    options:{
      responsive:true,
      plugins:{legend:{display:false},tooltip:{callbacks:{title:ctx=>`Month: ${ctx[0].label}`, label:ctx=>`Leads: ${ctx.raw}`}} },
      scales:{ y:{beginAtZero:true,title:{display:true,text:'Count'}}, x:{title:{display:true,text:'Month'}} },
      onClick: async (evt, activeEls) => {
        if(activeEls.length){
          const idx = activeEls[0].index;
          const monthNum = idx+1;
          const infoRes = await api(`action=getLeadsRows&limit=500`);
          if(infoRes.status==='ok'){
            const rows = infoRes.data.filter(r => new Date(r.date).getMonth()+1 === monthNum && (year===0 || new Date(r.date).getFullYear()===year));
            if(rows.length){
              let details = rows.map(r=>`${r.name} - ${r.contact||'No Contact'} (${r.lead_status||'Pending'})`).join('\n');
              alert(`Leads details for ${months[idx]} ${chartYear}:\n\n${details}`);
            }
          }
        }
      }
    }
  });
}

// --------------------- TABLES -----------------------------
async function loadTables(year){
  const searchAtt=searchAttendance.value.toLowerCase();
  const searchLead=searchLeads.value.toLowerCase();

  // Attendance
  const aRes=await api('action=getAttendanceRows&limit=500');
  const atbody=document.querySelector('#attendanceTable tbody'); atbody.innerHTML='';
  if(aRes.status==='ok'){
    aRes.data
      .filter(r=>year===0 || new Date(r.attendance_date).getFullYear()===year)
      .filter(r=>`${r.section} ${r.event_name||''}`.toLowerCase().includes(searchAtt))
      .forEach(r=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`<td>${r.attendance_date}</td><td>${escapeHtml(r.section)}</td><td>${r.attendance_count}</td><td>${escapeHtml(r.event_name||'')}</td><td><button class="small" data-id="${r.id}" data-type="att-delete">Delete</button></td>`;
        atbody.appendChild(tr);
      });
  }

  // Leads
  const lRes=await api('action=getLeadsRows&limit=500');
  const ltbody=document.querySelector('#leadsTable tbody'); ltbody.innerHTML='';
  if(lRes.status==='ok'){
    lRes.data
      .filter(r=>year===0 || new Date(r.date).getFullYear()===year)
      .filter(r=>`${r.name} ${r.contact||''} ${r.lead_status||''}`.toLowerCase().includes(searchLead))
      .forEach(r=>{
        const tr=document.createElement('tr');
        tr.innerHTML=`<td>${r.date}</td><td>${escapeHtml(r.name)}</td><td>${escapeHtml(r.contact||'')}</td><td>${escapeHtml(r.lead_status||'')}</td><td><button class="small" data-id="${r.id}" data-type="lead-delete">Delete</button></td>`;
        ltbody.appendChild(tr);
      });
  }
}

// --------------------- KPIs -----------------------------
async function updateKPIs(year){
  const chartYear = year || new Date().getFullYear();
  const aRes = await api(`action=getAttendanceMonthly&year=${chartYear}`);
  const lRes = await api(`action=getLeadsMonthly&year=${chartYear}`);
  const totalAttendance = aRes.status==='ok'?aRes.data.reduce((s,v)=>s+v,0):0;
  const totalLeads = lRes.status==='ok'?lRes.data.reduce((s,v)=>s+v,0):0;
  const services = aRes.status==='ok'?aRes.data.filter(v=>v!==0).length:0;
  const avg = services ? Math.round(totalAttendance/services) : 0;

  document.getElementById('kpiAttendance').textContent = totalAttendance;
  document.getElementById('kpiLeads').textContent = totalLeads;
  document.getElementById('kpiAvg').textContent = avg;

  // Follow-up rate
  const leadsRows = await api('action=getLeadsRows&limit=500');
  let followRate='—';
  if(leadsRows.status==='ok' && leadsRows.data.length){
    const yearRows = leadsRows.data.filter(l=>year===0 || new Date(l.date).getFullYear()===year);
    if(yearRows.length){
      const withStatus = yearRows.filter(l=>l.lead_status && l.lead_status.trim()).length;
      followRate = Math.round((withStatus/yearRows.length)*100)+'%';
    }
  }
  document.getElementById('kpiFollow').textContent = followRate;
}

// --------------------- EVENT COUNT WITH DETAILS -------------------------
async function updateEventCount(year){
  const chartYear = year || new Date().getFullYear();
  kpiEventsContainer.innerHTML='';

  // Fetch all distinct event names from DB
  const eventsRes = await api('action=getSpecialEvents');
  if(eventsRes.status !== 'ok' || !eventsRes.events.length) return;

  const specialEvents = eventsRes.events; // dynamically from DB

  for(const eventName of specialEvents){
    const res = await api(`action=getEventCount&year=${chartYear}&event_name=${encodeURIComponent(eventName)}`);
    const count = res.status==='ok'?res.total:0;

    const div = document.createElement('div');
    div.className = 'kpi card event-card';
    div.innerHTML = `<div class="kpi-title">${eventName}</div><div class="kpi-value">${count}</div>`;
    kpiEventsContainer.appendChild(div);

    div.addEventListener('click', async () => {
      const rowsRes = await api('action=getAttendanceRows&limit=500');
      if(rowsRes.status!=='ok') return;

      const rows = rowsRes.data
        .filter(r=>r.event_name && r.event_name.includes(eventName))
        .filter(r=>chartYear===0 || new Date(r.attendance_date).getFullYear()===chartYear);

      if(!rows.length){
        alert(`No attendees for ${eventName} in ${chartYear===0?'all years':chartYear}`);
        return;
      }

      let tableHtml = `<table border="1" style="width:100%;border-collapse:collapse">
        <thead><tr><th>Date</th><th>Event</th><th>Count</th></tr></thead><tbody>`;

      for(const r of rows){
        const dateObj = new Date(r.attendance_date);
        const formatted = dateObj.toLocaleDateString('en-GB', {day:'numeric', month:'long', year:'numeric'});
        tableHtml += `<tr><td>${formatted}</td><td>${escapeHtml(r.event_name)}</td><td>${r.attendance_count}</td></tr>`;
      }

      tableHtml += '</tbody></table>';
      const modalDiv = document.createElement('div');
      modalDiv.className='modal';
      modalDiv.innerHTML = `<div class="modal-content"><h3>${eventName} Attendance</h3>${tableHtml}<div style="text-align:right;margin-top:10px"><button class="closeModal">Close</button></div></div>`;
      document.body.appendChild(modalDiv);
      showModal(modalDiv);

      modalDiv.querySelector('.closeModal').addEventListener('click', ()=>{ modalDiv.remove(); });
    });
  }
}
async function updateSpecialEvents(year) {
  const chartYear = year || new Date().getFullYear();
  const container = document.getElementById("specialEventsContainer");
  container.innerHTML = '';

  // Fetch all distinct special events
  const eventsRes = await api('action=getSpecialEvents');
  if(eventsRes.status !== 'ok' || !eventsRes.events.length) return;

  for(const eventName of eventsRes.events) {
    const res = await api(`action=getEventCount&year=${chartYear}&event_name=${encodeURIComponent(eventName)}`);
    const count = res.status === 'ok' ? res.total : 0;

    const div = document.createElement('div');
    div.className = 'event-card';
    div.innerHTML = `
      <div class="event-title">${eventName}</div>
      <div class="event-count">${count}</div>
    `;
    container.appendChild(div);

    div.addEventListener('click', async () => {
      const rowsRes = await api('action=getAttendanceRows&limit=500');
      if(rowsRes.status !== 'ok') return;

      const rows = rowsRes.data
        .filter(r => r.event_name && r.event_name.includes(eventName))
        .filter(r => chartYear === 0 || new Date(r.attendance_date).getFullYear() === chartYear);

      if(!rows.length){
        alert(`No attendees for ${eventName} in ${chartYear === 0 ? 'all years' : chartYear}`);
        return;
      }

      let tableHtml = `<table border="1" style="width:100%;border-collapse:collapse">
        <thead><tr><th>Date</th><th>Event</th><th>Count</th></tr></thead><tbody>`;

      for(const r of rows){
        const dateObj = new Date(r.attendance_date);
        const formatted = dateObj.toLocaleDateString('en-GB', {day:'numeric', month:'long', year:'numeric'});
        tableHtml += `<tr><td>${formatted}</td><td>${escapeHtml(r.event_name)}</td><td>${r.attendance_count}</td></tr>`;
      }

      tableHtml += '</tbody></table>';
      const modalDiv = document.createElement('div');
      modalDiv.className = 'modal';
      modalDiv.innerHTML = `<div class="modal-content"><h3>${eventName} Attendance</h3>${tableHtml}<div style="text-align:right;margin-top:10px"><button class="closeModal">Close</button></div></div>`;
      document.body.appendChild(modalDiv);
      showModal(modalDiv);

      modalDiv.querySelector('.closeModal').addEventListener('click', () => { modalDiv.remove(); });
    });
  }
}
async function updateEventKPIs(year){
    const chartYear = year || new Date().getFullYear();
    const events = [
        'Monthly Men Conference',
        'Mighty Women Conference',
        'Strive',
        'Prophetic Service'
    ];

    for(const event of events){
        const res = await api(`action=getEventCount&year=${chartYear}&event_name=${encodeURIComponent(event)}`);
        const count = res.status==='ok'?res.total:0;

        let elId = '';
        switch(event){
            case 'Monthly Men Conference': elId='kpiMen'; break;
            case 'Mighty Women Conference': elId='kpiWomen'; break;
            case 'Strive': elId='kpiStrive'; break;
            case 'Prophetic Service': elId='kpiProphetic'; break;
        }

        const el = document.getElementById(elId);
        if(el) el.textContent = count;
    }
}

// Call this in refreshAll()
async function refreshAll(){
    const year = parseInt(yearSelect.value);
    await Promise.all([
        drawAttendance(year),
        drawLeads(year),
        loadTables(year),
        updateKPIs(year),
        updateSpecialEvents(year),
        updateEventKPIs(year)   // <-- new
    ]);
}

// --------------------- OPEN MODALS -------------------------
document.getElementById('openAddAttendance').addEventListener('click', () => {
  showModal(document.getElementById('modalAttendance'));
});

document.getElementById('openAddLead').addEventListener('click', () => {
  showModal(document.getElementById('modalLead'));
});

// --------------------- CLOSE MODALS ------------------------
document.querySelectorAll('.closeModal').forEach(btn => {
  btn.addEventListener('click', () => hideAllModals());
});


// --------------------- FORM HANDLERS -------------------------
document.getElementById('attendanceForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const data=new FormData(e.target); data.append('action','addAttendance');
  const res=await api('', 'POST', data);
  if(res.status==='ok'){ hideAllModals(); e.target.reset(); refreshAll(); }
  else alert(res.message||'Error saving');
});

document.getElementById('leadForm').addEventListener('submit', async e=>{
  e.preventDefault();
  const data=new FormData(e.target); data.append('action','addLead');
  const res=await api('', 'POST', data);
  if(res.status==='ok'){ hideAllModals(); e.target.reset(); refreshAll(); }
  else alert(res.message||'Error saving');
});

// --------------------- DELETE BUTTONS -------------------------
document.body.addEventListener('click', async ev=>{
  const btn = ev.target.closest('button[data-type]'); if(!btn) return;
  const id = btn.dataset.id; const type = btn.dataset.type;
  if(type==='att-delete'){ if(!confirm('Delete attendance?')) return; const res=await api('', 'POST',{action:'deleteAttendance',id}); if(res.status==='ok') refreshAll(); }
  if(type==='lead-delete'){ if(!confirm('Delete lead?')) return; const res=await api('', 'POST',{action:'deleteLead',id}); if(res.status==='ok') refreshAll(); }
});

// --------------------- INIT -------------------------
fillYearOptions();
refreshAll();
yearSelect.addEventListener('change', refreshAll);
refreshBtn.addEventListener('click', refreshAll);
searchAttendance.addEventListener('input', ()=>refreshAll());
searchLeads.addEventListener('input', ()=>refreshAll());

