<?php 
include('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// FIXED: user → username
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Activity Log - AUTO Detail Car Wash</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* Reuse your previous styling */
body { font-family:"Poppins", Arial, sans-serif; background:#f5f7fa; margin:0; color:#333; }
header h1 { margin:0; font-size:1.8em; }
header nav { margin-top:10px; }
header nav a { color:white; margin-right:15px; text-decoration:none; font-weight:bold; padding:6px 12px; border-radius:5px; transition:0.3s;}
header nav a:hover { background:rgba(255,255,255,0.2);}
main { padding:20px;}
.loader { display:none; text-align:center; margin-top:20px; font-size:18px; }

/* Search Bar */
.search-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.search-bar input,
.search-bar select,
.search-bar button {
    padding: 7px 10px;
    border-radius: 5px;
    border: 1px solid black;
    font-size: 14px;
}
.search-bar input[type="text"] { width: 220px; }
.search-bar input[type="date"] { width: 150px; }
.search-bar button {
    background: #0078D7;
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-weight: 500;
    width: 100px;
}
.search-bar button:hover { background:#005ea6;}
.search-bar .record-limit-container { display: flex; align-items: center; margin-top: 8px; gap: 5px; flex-wrap: wrap;}
#recordLimit { width: 80px; padding: 5px 8px; font-size: 13px; }

/* Export Buttons */
.export-buttons { margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;}
.export-buttons button { background:#28a745; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; display:flex; align-items:center; gap:5px;}
.export-buttons button:hover { background:#1e7e34;}

/* Table Styles */
#table-container { overflow-x:auto; }
#activity-table { width:100%; border-collapse:collapse; background:white; min-width:1000px;}
#activity-table th { padding:8px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#activity-table td { padding:2px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#activity-table th { position:sticky; top:0; background:#0078D7; color:white; }
#activity-table tr:nth-child(even) { background:#f2f2f2;}
#activity-table tr:hover { background:#e6f7ff;}
#activity-table td:first-child { font-weight:bold; text-align:center; background:#eaf4ff; color:#0056b3; }

/* Buttons in Table */
.action-btn { padding:5px 5px; margin:2px; border:none; border-radius:4px; cursor:pointer; font-size:13px; transition:0.3s; white-space:nowrap;}
.delete-btn { background:#dc3545; color:#fff; width:80%; }
.delete-btn:hover { background:#c82333; }

/* Modal Styles */
.modal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.4);}
.modal-content { background:#fff; margin:5% auto; padding:20px; border-radius:8px; width:90%; max-width:500px; position:relative;}
.modal-content h2 { margin-top:0; }
.close { position:absolute; right:10px; top:10px; font-size:24px; cursor:pointer; color:#333; }

/* Responsive */
@media(max-width:768px){
    .search-bar input[type="text"]{ width:100%; }
    .search-bar input[type="date"]{ width:calc(50% - 10px);}
    .search-bar button{ width:100%; justify-content:center; }
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="assets/jquery/jquery-3.7.0.min.js"></script>
</head>
<body>

<header>
<h1><i class="fa-solid fa-list-check"></i> Activity Log</h1>
<nav>
    <a href="dashboard.php">Home</a>
    <a href="dashboard.php">Dashboard</a>

    <?php if($_SESSION['role']=='admin'): ?>
        <a href="backup_system.php">🔥 Backup</a> 
        <a href="reports.php">Reports</a>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</nav>
</header>

<main>

<!-- 🔍 Search Bar -->
<div class="search-bar">
    <input type="text" id="search" placeholder="Search by user, action, or details...">
    <input type="date" id="from_date">
    <input type="date" id="to_date">
    <button id="searchBtn"><i class="fa fa-search"></i> Search</button>
    <div class="record-limit-container">
        <label for="recordLimit" style="color:Red;">Show Range:</label>
        <select id="recordLimit">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="All">All</option>
        </select>
    </div>
</div>

<!-- ✅ Export Buttons -->
<div class="export-buttons">
  <form method="GET" action="export_activity_pdf.php" target="_blank" id="pdfForm">
      <input type="hidden" name="search" id="pdf_search">
      <input type="hidden" name="from_date" id="pdf_from">
      <input type="hidden" name="to_date" id="pdf_to">
      <input type="hidden" name="limit" id="pdf_limit">
      <button type="submit" id="exportPdfBtn">
          <i class="fa-solid fa-file-pdf"></i> Export PDF
      </button>
  </form>
</div>

<!-- 📋 Data Table -->
<div id="table-container">
  <table id="activity-table">
      <thead>
          <tr>
              <th>S.No</th>
              <th>Username</th>
              <th>Action</th>
              <th>Details</th>
              <th>IP Address</th>
              <th>Timestamp</th>
              <th>Actions</th>
          </tr>
      </thead>
      <tbody></tbody>
  </table>
</div>

<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>

<script>
function loadData() {
    $(".loader").show();

    let search = $("#search").val();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    let limit = $("#recordLimit").val();

    $("#pdf_search").val(search);
    $("#pdf_from").val(from_date);
    $("#pdf_to").val(to_date);
    $("#pdf_limit").val(limit);

    $.ajax({
        url: "fetch_activity.php",
        method: "GET",
        data: { search, from_date, to_date, limit },
        dataType: "json",
        success: function(data) {
            let tbody = "";
            let sn = 1;

            if (data.records.length > 0) {
                data.records.forEach(r => {
                    let actionBtn = <?php echo json_encode($_SESSION['role']); ?> === 'admin' ? 
                        `<button class="action-btn delete-btn" data-id="${r.id}"><i class="fa fa-trash"></i>Del</button>` : '';

                    tbody += `
                        <tr>
                            <td>${sn}</td>
                            <td>${r.username}</td>
                            <td>${r.action}</td>
                            <td>${r.details}</td>
                            <td>${r.ip_address}</td>
                            <td>${r.created_at}</td>
                            <td>${actionBtn}</td>
                        </tr>`;
                    sn++;
                });
            } else {
                tbody = `<tr><td colspan="8" style="text-align:center;">No activity found</td></tr>`;
            }

            $("#activity-table tbody").html(tbody);
            $(".loader").hide();
        }
    });
}

// Sync export PDF filters
$("#pdfForm").on("submit", function() {
    $("#pdf_search").val($("#search").val());
    $("#pdf_from").val($("#from_date").val());
    $("#pdf_to").val($("#to_date").val());
    $("#pdf_limit").val($("#recordLimit").val());
});

$(document).ready(function(){
    loadData();
    $("#searchBtn, #search, #from_date, #to_date, #recordLimit").on("click keyup change", loadData);
});

// Delete event
$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    if(confirm("Are you sure you want to delete this activity?")){
        $.post('delete_activity.php', {id}, function(resp){
            resp = JSON.parse(resp);
            alert(resp.message);
            loadData();
        });
    }
});
</script>

</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
