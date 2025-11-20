<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>City Gate Chapel - Event Management</title>
<style>
:root{
  --bg:#f0f8f4;
  --card:#fff;
  --accent:#4caf50;
  --accent-dark:#388e3c;
  --muted:#555;
  --hover:rgba(76,175,80,0.1);
  --shadow-light:rgba(0,0,0,0.06);
  --shadow-dark:rgba(0,0,0,0.15);
  --transition:0.3s ease-in-out;
}

*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',Arial,sans-serif;}
body{background:var(--bg);color:#111;line-height:1.5;}
.topbar{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;background:linear-gradient(90deg,#e0f2e9,#f0f8f4);border-bottom:1px solid #cde7d3;position:sticky;top:0;z-index:100;flex-wrap:wrap;box-shadow:0 4px 12px var(--shadow-light);}
.brand{font-weight:700;font-size:22px;color:var(--accent-dark);}
.topbar button{padding:8px 16px;font-size:14px;border-radius:8px;border:1px solid #ddd;background:#fff;box-shadow:0 2px 6px var(--shadow-light);transition:var(--transition);}
.topbar button:hover{background:var(--hover);transform:translateY(-1px);}
.container{max-width:1200px;margin:24px auto;padding:0 16px;}
.tables{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:40px;}
.table-card{background:var(--card);border-radius:16px;padding:16px;box-shadow:0 6px 18px var(--shadow-light);overflow-x:auto;transition:var(--transition);}
.table-card:hover{box-shadow:0 10px 28px var(--shadow-dark);}
.table-card h4{margin-bottom:12px;font-size:18px;font-weight:600;color:var(--accent-dark);}
.table-card table{width:100%;border-collapse:collapse;font-size:14px;}
.table-card th,.table-card td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
.table-card th{font-weight:600;color:var(--muted);}
.table-card tr:hover{background:var(--hover);transform:scale(1.01);transition:var(--transition);}
.small{padding:6px 12px;font-size:13px;border-radius:8px;border:1px solid #ddd;background:#fff;box-shadow:0 2px 6px var(--shadow-light);transition:var(--transition);}
.small:hover{background:var(--hover);transform:translateY(-1px);}
.modal{position:fixed;inset:0;background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;z-index:200;}
.modal[hidden]{display:none;}
.modal-content{background:var(--card);padding:24px;width:95%;max-width:450px;border-radius:16px;box-shadow:0 12px 32px var(--shadow-dark);animation:fadeIn 0.3s ease;}
.modal-content h3{margin-top:0;margin-bottom:16px;font-size:20px;color:var(--accent-dark);}
.modal-content form label{display:block;margin:10px 0 6px;font-size:14px;font-weight:500;}
.modal-content input,.modal-content textarea{width:100%;padding:10px;border-radius:8px;border:1px solid #ddd;font-size:14px;margin-bottom:12px;transition:var(--transition);}
.modal-content input:focus,.modal-content textarea:focus{border-color:var(--accent);box-shadow:0 0 6px var(--accent-dark);}
.modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px;}
button[type="submit"]{background:var(--accent);color:#fff;border:none;padding:10px 18px;border-radius:8px;font-weight:600;box-shadow:0 2px 6px var(--shadow-light);}
button[type="submit"]:hover{background:var(--accent-dark);transform:translateY(-1px);}
.closeModal{background:#e53935;color:#fff;border:none;padding:8px 16px;border-radius:8px;box-shadow:0 2px 6px var(--shadow-light);}
.closeModal:hover{background:#b71c1c;transform:translateY(-1px);}
.copy-btn{margin-bottom:10px;}
.toast{position:fixed;bottom:20px;right:20px;background:var(--accent);color:#fff;padding:12px 18px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:9999;opacity:0;transition:opacity 0.3s ease,transform 0.3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
@media(max-width:1000px){.tables{grid-template-columns:1fr;}}
@media(max-width:600px){.modal-content{padding:20px;}.topbar{flex-direction:column;gap:10px;}.brand{font-size:20px;}.topbar button{width:100%;}}
</style>
</head>
<body>

<div class="topbar">
  <div class="brand">City Gate Chapel - Elohim Sanctuary</div>
  <button id="openCreateEvent">+ Create Event</button>
</div>

<div class="container">
  <div class="tables">
    <div class="table-card">
      <h4>Events</h4>
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Date</th>
            <th>Location</th>
            <th>Registrations</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody id="eventsTable"></tbody>
      </table>
    </div>

    <div class="table-card">
      <h4>Registered People</h4>
      <button class="small copy-btn" id="copyPhones">Copy Selected Numbers</button>
      <table>
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAllRegs"></th>
            <th>Event</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Guests</th>
            <th>Registered At</th>
          </tr>
        </thead>
        <tbody id="registrationsTable"></tbody>
      </table>
      <button class="small" onclick="copyLink('https://localhost/church/register.php?event=2a32ecf37b8b3876')">Copy Link</button>

    </div>
  </div>
</div>

<div class="modal" id="createEventModal" hidden>
  <div class="modal-content">
    <h3>Create Event</h3>
    <form id="createEventForm">
      <label>Event Name</label>
      <input type="text" name="name" required>
      <label>Date & Time</label>
      <input type="datetime-local" name="date" required>
      <label>Location</label>
      <input type="text" name="location">
      <label>Description</label>
      <textarea name="description"></textarea>
      <div class="modal-actions">
        <button type="submit">Create</button>
        <button type="button" class="closeModal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
const createModal = document.getElementById('createEventModal');
const openBtn = document.getElementById('openCreateEvent');
const closeBtns = document.querySelectorAll('.closeModal');
const createForm = document.getElementById('createEventForm');
const eventsTable = document.getElementById('eventsTable');
const registrationsTable = document.getElementById('registrationsTable');
const copyPhonesBtn = document.getElementById('copyPhones');
const selectAllCheckbox = document.getElementById('selectAllRegs');

openBtn.addEventListener('click', () => createModal.hidden = false);
closeBtns.forEach(btn => btn.addEventListener('click', () => createModal.hidden = true));

// FETCH EVENTS
async function fetchEvents() {
  const res = await fetch('events_api.php?action=getEvents');
  const data = await res.json();
  eventsTable.innerHTML = '';
  data.forEach(ev => {
    const eventDate = new Date(ev.event_date).toLocaleString();
    const link = `register.php?event=${ev.link_token}`;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${ev.name}</td>
      <td>${eventDate}</td>
      <td>${ev.location || '-'}</td>
      <td>${ev.registrations}</td>
      <td>
        <button class="small" onclick="viewRegistrations(${ev.id})">View</button>
        <button class="small" onclick="deleteEvent(${ev.id})">Delete</button>
        <button class="small" onclick="copyLink('${link}')">Copy Link</button>
      </td>
    `;
    eventsTable.appendChild(tr);
  });
}

// DELETE EVENT
async function deleteEvent(id){
  if(!confirm('Delete this event?')) return;
  const res = await fetch('events_api.php?action=deleteEvent', {
    method:'POST',
    body:new URLSearchParams({event_id:id})
  });
  const data = await res.json();
  if(data.status==='ok'){ showToast("Event deleted!"); fetchEvents(); registrationsTable.innerHTML=''; }
  else showToast(data.message);
}

// FETCH REGISTRATIONS
async function viewRegistrations(eventId) {
  const res = await fetch(`events_api.php?action=getRegistrations&event_id=${eventId}`);
  const data = await res.json();
  registrationsTable.innerHTML = '';
  data.forEach(r => {
    const regDate = new Date(r.registered_at).toLocaleString();
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="checkbox" class="selectReg" value="${r.contact}"></td>
      <td>${r.event_name}</td>
      <td>${r.name}</td>
      <td>${r.contact}</td>
      <td>${r.guests}</td>
      <td>${regDate}</td>
    `;
    registrationsTable.appendChild(tr);
  });
}

// CREATE EVENT
createForm.addEventListener('submit', async e => {
  e.preventDefault();
  const formData = new FormData(createForm);
  const res = await fetch('events_api.php?action=createEvent',{method:'POST',body:formData});
  const data = await res.json();
  if(data.status==='ok'){
    showToast("Event created!");
    createModal.hidden=true;
    createForm.reset();
    fetchEvents();
  } else showToast(data.message);
});

// COPY LINK
function copyLink(link){ navigator.clipboard.writeText(link).then(()=>showToast("Link copied!")); }

// COPY SELECTED PHONES
copyPhonesBtn.addEventListener('click', () => {
  const selected = document.querySelectorAll('.selectReg:checked');
  if(selected.length===0){ return showToast("No contacts selected!"); }
  const numbers = Array.from(selected).map(cb=>cb.value).join(', ');
  navigator.clipboard.writeText(numbers).then(()=>showToast(`${selected.length} numbers copied!`));
});

// SELECT ALL
selectAllCheckbox.addEventListener('change', e => {
  document.querySelectorAll('.selectReg').forEach(cb=>cb.checked = e.target.checked);
});

// TOAST
function showToast(msg){
  const toast = document.createElement('div');
  toast.className='toast';
  toast.textContent=msg;
  document.body.appendChild(toast);
  setTimeout(()=>toast.style.opacity=1,50);
  setTimeout(()=>toast.style.opacity=0,2000);
  setTimeout(()=>toast.remove(),2300);
}

// INITIAL LOAD
fetchEvents();
</script>

</body>
</html>
