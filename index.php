<?php
// index.php - Church Dashboard UI
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>City Gate Chapel— Attendance & Leads</title>
  <link rel="stylesheet" href="style.css">
  <!-- Chart.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <!-- Topbar -->
  <header class="topbar">
    <div class="brand">🏛️ City Gate Chapel</div>
    <div class="controls">
      <label for="yearSelect">Year</label>
      <select id="yearSelect"></select>
      <button id="refreshBtn">Refresh</button>
      <button id="openAddAttendance">+ Attendance</button>
      <button id="openAddLead">+ Lead</button>
    </div>
  </header>

  <!-- Main Container -->
  <main class="container">
    <!-- KPIs -->
    <section class="kpis">
      <div class="kpi card">
        <div class="kpi-title">Total Attendance (YTD)</div>
        <div id="kpiAttendance" class="kpi-value">—</div>
      </div>
      <div class="kpi card">
        <div class="kpi-title">Average / Service</div>
        <div id="kpiAvg" class="kpi-value">—</div>
      </div>
      <div class="kpi card">
        <div class="kpi-title">New Leads (YTD)</div>
        <div id="kpiLeads" class="kpi-value">—</div>
      </div>
      <div class="kpi card">
        <div class="kpi-title">Follow-up Rate</div>
        <div id="kpiFollow" class="kpi-value">—</div>
      </div>

      <!-- Special Event KPIs -->
      <div id="kpiEventsContainer" class="event-kpis"></div>
    </section>
    <!-- Special Event KPIs -->
<div id="kpiEventsContainer" class="event-kpis"></div>

<!-- Special Events List Section -->
<div id="specialEventsList" class="special-events-list">
  <h4>Special Events</h4>
  <div id="specialEventsContainer"></div>
</div>


    <!-- Charts Section -->
    <section class="charts">
      <div class="card chart-card">
        <h3>Attendance — Monthly</h3>
        <canvas id="attendanceChart"></canvas>
      </div>
      <div class="card chart-card">
        <h3>Leads — Monthly</h3>
        <canvas id="leadsChart"></canvas>
      </div>
    </section>

    <!-- Tables Section -->
    <section class="tables">
      <div class="card table-card">
        <h4>Recent Attendance</h4>
        <input type="text" id="searchAttendance" placeholder="Search attendance..." />
        <table id="attendanceTable">
          <thead>
            <tr><th>Date</th><th>Section</th><th>Count</th><th>Event</th><th>Action</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>

      <div class="card table-card">
        <h4>Recent Leads</h4>
        <input type="text" id="searchLeads" placeholder="Search leads..." />
        <table id="leadsTable">
          <thead>
            <tr><th>Date</th><th>Name</th><th>Contact</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </section>
  </main>

  <!-- Modal: Add Attendance -->
  <div id="modalAttendance" class="modal" hidden>
    <div class="modal-content">
      <h3>Add Attendance</h3>
      <form id="attendanceForm" autocomplete="off">
        <label>Date <input type="date" name="attendance_date" required></label>
        <label>Section <input name="section" required placeholder="Normal Church"></label>
        <label>Count <input type="number" name="attendance_count" required min="0" value="0"></label>
        <label>Event <input name="event_name" placeholder="Optional"></label>
        <div class="modal-actions">
          <button type="submit">Save</button>
          <button type="button" class="closeModal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal: Add Lead -->
  <div id="modalLead" class="modal" hidden>
    <div class="modal-content">
      <h3>Add Lead</h3>
      <form id="leadForm" autocomplete="off">
        <label>Date <input type="date" name="date" required></label>
        <label>Name <input name="name" required></label>
        <label>Contact <input name="contact"></label>
        <label>Job <input name="job"></label>
        <label>Reached Out By <input name="reached_out_by"></label>
        <label>Status <input name="lead_status"></label>
        <label>Remarks <textarea name="remarks" rows="3"></textarea></label>
        <div class="modal-actions">
          <button type="submit">Save</button>
          <button type="button" class="closeModal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
