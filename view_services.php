<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Assign user info to variables for easier access in JS
$userRole = $_SESSION['role'] ?? 'staff';
$userName = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Services - Car Wash</title>
<link rel="stylesheet" href="style.css">
<!-- OFFLINE BOOTSTRAP CSS -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- FONTS OFFLINE -->
<style>
@font-face {
    font-family: 'Poppins';
    src: url('../assets/poppins/Poppins-Regular.ttf') format('truetype');
    font-weight: 400;
}
@font-face {
    font-family: 'Poppins';
    src: url('../assets/poppins/Poppins-Medium.ttf') format('truetype');
    font-weight: 500;
}
@font-face {
    font-family: 'Poppins';
    src: url('../assets/poppins/Poppins-SemiBold.ttf') format('truetype');
    font-weight: 600;
}

/* ===== General Styles ===== */
body { font-family:"Poppins", Arial, sans-serif; background:#f5f7fa; margin:0; color:#333; }
header h1 { margin:0; font-size:1.8em; }
header nav { margin-top:10px; }
header nav a { color:white; margin-right:15px; text-decoration:none; font-weight:bold; padding:6px 12px; border-radius:5px; transition:0.3s;}
header nav a:hover { background:rgba(255,255,255,0.2);}
main { padding:20px;}
.loader { display:none; text-align:center; margin-top:20px; font-size:18px; }
/* ===== Search Bar ===== */
.search-bar { display:flex; flex-wrap:wrap; align-items:center; gap:8px; padding:10px; border:1px solid #ddd; border-radius:8px; background:#fff; position:sticky; top:0; z-index:10; box-shadow:0 2px 6px rgba(0,0,0,0.05);}
.search-bar input, .search-bar select, .search-bar button { padding:7px 10px; border-radius:5px; border:1px solid #000; font-size:14px;}
.search-bar input[type="text"] { width:220px; }
.search-bar input[type="date"] { width:150px; }
.search-bar select { width:120px; }
.search-bar button { background:#0078D7; color:#fff; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px; font-weight:500; width:100px;}
.search-bar button:hover { background:#005ea6;}
.search-bar .record-limit-container { display:flex; align-items:center; margin-top:8px; gap:5px; flex-wrap:wrap;}
#recordLimit { width:80px; padding:5px 8px; font-size:13px; }
/* ===== Export Buttons ===== */
/* Export Buttons Container */
.export-buttons {
    display: flex;
    gap: 10px;       /* spacing between buttons */
    flex-wrap: wrap;  /* optional, keeps buttons on the same line if space allows */
    margin-bottom: 20px;
}

.export-buttons form {
    margin: 0;           /* remove default form margin */
    display: inline-flex; /* make forms inline so buttons sit in a row */
}

.export-buttons button {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
}

.export-buttons button:hover { background:#1e7e34; }

/* ===== Table ===== */
#table-container { overflow-x:auto; }
#services-table { width:100%; border-collapse:collapse; background:white; min-width:1000px;}
#services-table th { padding:10px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#services-table td { padding:5px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#services-table th { background:#0078D7; color:white; position:sticky; top:0; }
#services-table tr:nth-child(even) { background:#f2f2f2;}
#services-table tr:hover { background:#e6f7ff;}
#services-table td:first-child { font-weight:bold; text-align:center; background:#eaf4ff; color:#0056b3;}
#grandTotal { font-weight:bold; }
/* ===== Buttons in Table ===== */
.action-btn { padding:10px 10px; margin:2px; border:none; border-radius:4px; cursor:pointer; font-size:13px; transition:0.3s; white-space:nowrap;}
.edit-btn { background:#28a745; color:#fff; width:50%; }
.edit-btn:hover { background:#1e7e34; }
.delete-btn { background:#dc3545; color:#fff; width:50%; }
.delete-btn:hover { background:#c82333; }
/* ===== Modal ===== */
.modal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.4);}
.modal-content { background:#fff; margin:5% auto; padding:20px; border-radius:8px; width:90%; max-width:500px; position:relative;}
.modal-content h2 { margin-top:0; }
.close { position:absolute; right:10px; top:10px; font-size:24px; cursor:pointer; color:#333; }
.modal-content input, .modal-content select { width:100%; margin:5px 0 15px; padding:8px; border:1px solid #ccc; border-radius:5px; font-size:14px; }
.modal-content button { width:100%; padding:10px; border:none; border-radius:5px; font-size:15px; cursor:pointer; background:#0078D7; color:#fff; font-weight:500; }
.modal-content button:hover { background:#005ea6; }
/* ===== Responsive ===== */
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
<h1><i class="fa-solid fa-car-side"></i> AUTO Detail Car Wash</h1>
<nav>
    <a href="dashboard.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_service.php">Add Record</a>
    <?php if($userRole === 'admin'): ?>
        <a href="backup_system.php">🔥 Backup</a> 
        <a href="reports.php">Reports</a>
        <a href="view_expenses.php">Expenses</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>
</header>

<main>

<!-- 🔍 Search Bar -->
<div class="search-bar">
    <input type="text" id="search" placeholder="Search by customer, vehicle, or service...">
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

<!-- Export Buttons -->
<div class="export-buttons">
  <form method="GET" action="export_pdf.php" target="_blank" id="servicePdfForm">
      <input type="hidden" name="search" id="pdf_search">
      <input type="hidden" name="from_date" id="pdf_from">
      <input type="hidden" name="to_date" id="pdf_to">
      <input type="hidden" name="limit" id="pdf_limit">
      <button type="submit" id="exportServicePdfBtn">
          <i class="fa-solid fa-file-pdf"></i> Search PDF
      </button>
  </form>
<?php if($userRole === 'admin'): ?>
  <form method="GET" action="gen_report.php" target="_blank" id="generalReportForm">
      <input type="hidden" name="search" id="gr_search">
      <input type="hidden" name="from_date" id="gr_from">
      <input type="hidden" name="to_date" id="gr_to">
      <input type="hidden" name="limit" id="gr_limit">
      <button type="submit" id="exportGeneralReportBtn">
          <i class="fa-solid fa-file-pdf"></i> General Report
      </button>
  </form>
<?php endif; ?>
</div>


<!-- 📋 Data Table -->
<div id="table-container">
  <table id="services-table">
      <thead>
          <tr>
              <th>S.No</th>
              <th>Handled By</th>
              <th>Contact</th>
              <th>Vehicle No</th>
              <th>Vehicle Type</th>
              <th>Service</th>
              <th>Price</th>
              <th>Payment Status</th>
              <th>Added By</th>
              <th>Date</th>
              <th>Actions</th>
          </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
          <tr style="font-weight:bold; background:#f0f0f0;">
              <td colspan="6" style="text-align:right;">Grand Total</td>
              <td id="grandTotal">UGX 0</td>
              <td colspan="4"></td>
          </tr>
      </tfoot>
  </table>
</div>

<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>

<!-- ✏️ Edit Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2><center>Update Service</center></h2>
    <input type="hidden" id="edit_id">
    <label>Handled By</label>
    <input type="text" id="edit_customer">
    <label>Contact</label>
    <input type="text" id="edit_contact">
    <label>Vehicle Number</label>
    <input type="text" id="edit_vehicle">
    <label>Vehicle Type</label>
    <input type="text" id="edit_type">
    <label>Service Type</label>
    <select id="edit_service"></select>
    <label>Price</label>
    <input type="number" id="edit_price" readonly>
    <label>Payment Status</label>
    <select id="edit_payment">
      <option value="Paid">Paid</option>
      <option value="nopay">No Pay</option>
    </select>
    <button id="saveEdit">Save Changes</button>
  </div>
</div>

<script>
const userRole = <?php echo json_encode($userRole); ?>;
const userName = <?php echo json_encode($userName); ?>;

// ===== Load Data =====
function loadData() {
    $(".loader").show();
    let search = $("#search").val();
    let from_date = $("#from_date").val();
    let to_date = $("#to_date").val();
    let limit = $("#recordLimit").val();

    // Sync export forms
    $("#pdf_search, #gr_search").val(search);
    $("#pdf_from, #gr_from").val(from_date);
    $("#pdf_to, #gr_to").val(to_date);
    $("#pdf_limit, #gr_limit").val(limit);

    $.get("fetch_services.php", { search, from_date, to_date, limit }, function(data) {
        let tbody = "";
        let sn = 1;
        let grandTotal = 0;

        if(data.records.length > 0){
            data.records.forEach(r => {
                const canEditDelete = (userRole === 'admin' || r.added_by === userName);
                let actionButtons = '';
                if(canEditDelete){
                    actionButtons += `<button class="action-btn edit-btn" data-id="${r.id}"><i class="fa fa-edit"></i>Edit</button>`;
                    if(userRole === 'admin'){
                        actionButtons += `<button class="action-btn delete-btn" data-id="${r.id}"><i class="fa fa-trash"></i>Del</button>`;
                    }
                }

                tbody += `
                    <tr>
                        <td>${sn}</td>
                        <td>${r.customer_name}</td>
                        <td>${r.contact}</td>
                        <td>${r.vehicle_number}</td>
                        <td>${r.vehicle_type}</td>
                        <td>${r.service_type}</td>
                        <td>UGX ${parseFloat(r.price).toLocaleString()}</td>
                        <td>${r.payment_status}</td>
                        <td>${r.added_by}</td>
                        <td>${r.date}</td>
                        <td>${actionButtons}</td>
                    </tr>`;
                grandTotal += parseFloat(r.price);
                sn++;
            });
        } else {
            tbody = `<tr><td colspan="11" style="text-align:center;">No records found</td></tr>`;
        }

        $("#services-table tbody").html(tbody);
        $("#grandTotal").text("UGX " + grandTotal.toLocaleString());
        $(".loader").hide();
    }, "json");
}

// ===== Event Handlers =====
$(document).ready(function(){
    loadData();
    $("#searchBtn, #search, #from_date, #to_date, #recordLimit").on("click keyup change", loadData);

    // Edit Modal
    const modal = $("#editModal");
    $(document).on('click', '.edit-btn', function(){
        let id = $(this).data('id');
        $.get('get_service.php', { id }, function(res){
            res = JSON.parse(res);
            if(res.error){ alert(res.error); return; }
            $("#edit_id").val(res.id);
            $("#edit_customer").val(res.customer_name);
            $("#edit_contact").val(res.contact);
            $("#edit_vehicle").val(res.vehicle_number);
            $("#edit_type").val(res.vehicle_type);
            $("#edit_service").val(res.service_type).trigger('change');
            $("#edit_payment").val(res.payment_status);
            modal.show();
        });
    });
    $(".close").click(()=> modal.hide());
    $(window).click(e=> { if(e.target.id=="editModal") modal.hide(); });

    $("#saveEdit").click(function(){
        $.post('update_service.php', {
            id: $("#edit_id").val(),
            customer_name: $("#edit_customer").val(),
            contact: $("#edit_contact").val(),
            vehicle_number: $("#edit_vehicle").val(),
            vehicle_type: $("#edit_type").val(),
            service_type: $("#edit_service").val(),
            price: $("#edit_price").val(),
            payment_status: $("#edit_payment").val()
        }, function(resp){
            try { resp = JSON.parse(resp); } catch(e){ window.location.href = "view_services.php"; return; }
            alert(resp.message);
            if(resp.status === "success"){ modal.hide(); loadData(); }
        });
    });

    // Delete
    $(document).on('click', '.delete-btn', function(){
        let id = $(this).data('id');
        if(confirm("Are you sure you want to delete this record?")){
            $.post('delete_service.php', { id }, function(resp){
                try { resp = JSON.parse(resp); } catch(e){ alert("Unexpected response!"); return; }
                alert(resp.message);
                loadData();
            }).fail(()=> alert("Error deleting record."));
        }
    });

    // Load service types
    let serviceList = [];
    function loadServiceTypes(){
        $.get("get_service_types.php", data=>{
            serviceList = JSON.parse(data);
            let options = '<option value="">Select Service</option>';
            serviceList.forEach(s => options += `<option value="${s.service_name}">${s.service_name}</option>`);
            $("#edit_service").html(options);
        });
    }
    $(document).ready(loadServiceTypes);

    // Auto-fill price
    $(document).on('change','#edit_service', function(){
        let selected = $(this).val();
        let service = serviceList.find(s => s.service_name === selected);
        $("#edit_price").val(service ? service.price : '');
    });
});
</script>
</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
