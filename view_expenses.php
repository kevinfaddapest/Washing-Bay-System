<?php
include('config.php');
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// ✅ Only admin allowed
$userRole = strtolower($_SESSION['role'] ?? '');
if ($userRole !== 'admin') {
    echo "Unauthorized — Only admins can access this page.";
    exit;
}

$userName = $_SESSION['username'];
$userId = $_SESSION['user_id'] ?? 0;

// ===== Function to log activity =====
function logActivity($user_id, $username, $action, $details = '') {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, username, action, details, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $user_id, $username, $action, $details, $ip_address);
        $stmt->execute();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Expenses - Car Wash</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* ===== General ===== */
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
.export-buttons { margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;}
.export-buttons button { background:#28a745; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer; display:flex; align-items:center; gap:5px;}
.export-buttons button:hover { background:#1e7e34;}

/* ===== Table ===== */
#table-container { overflow-x:auto; }
#expenses-table { width:100%; border-collapse:collapse; background:white; min-width:900px;}
#expenses-table th { padding:10px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#expenses-table td { padding:5px; border:1px solid #ddd; text-align:left; white-space:nowrap; }
#expenses-table th { background:#0078D7; color:white; position:sticky; top:0; }
#expenses-table tr:nth-child(even) { background:#f2f2f2;}
#expenses-table tr:hover { background:#e6f7ff;}
#expenses-table td:first-child { font-weight:bold; text-align:center; background:#eaf4ff; color:#0056b3;}
#grandTotal { font-weight:bold; }

/* ===== Buttons in Table ===== */
.action-btn { padding:5px 5px; margin:2px; border:none; border-radius:4px; cursor:pointer; font-size:13px; transition:0.3s; white-space:nowrap;}
.edit-btn { background:#28a745; color:#fff; width:50%; }
.edit-btn:hover { background:#1e7e34; }
.delete-btn { background:#dc3545; color:#fff; width:50%; }
.delete-btn:hover { background:#c82333; }

/* ===== Modal ===== */
.modal { display:none; position:fixed; z-index:100; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.4); }
.modal-content { background:#fff; margin:5% auto; padding:20px; border-radius:8px; width:90%; max-width:500px; position:relative; }
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
<h1><i class="fa-solid fa-wallet"></i> Expense Records</h1>
<nav>
    <a href="dashboard.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_expense.php">Add Expense</a>
    <a href="backup_system.php">🔥 Backup</a> 
    <a href="reports.php">Reports</a>
    <a href="logout.php">Logout</a>
</nav>
</header>

<main>

<!-- 🔍 Search Bar -->
<div class="search-bar">
    <input type="text" id="search" placeholder="Search by expense or category...">
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

<!-- ✅ Export Button -->
<div class="export-buttons">
  <form method="GET" action="export_expenses.php" target="_blank" id="pdfForm">
      <input type="hidden" name="search" id="pdf_search">
      <input type="hidden" name="from_date" id="pdf_from">
      <input type="hidden" name="to_date" id="pdf_to">
      <input type="hidden" name="limit" id="pdf_limit">
      <button type="submit" id="exportPdfBtn" style="width:auto; padding:5px 10px; font-size:13px;">
          <i class="fa-solid fa-file-pdf"></i>Export PDF
      </button>
  </form>
</div>

<!-- 📋 Expenses Table -->
<div id="table-container">
  <table id="expenses-table">
      <thead>
          <tr>
              <th>Sno</th>
              <th>Expense Name</th>
              <th>Category</th>
              <th>Amount</th>
              <th>Added By</th>
              <th>Date</th>
              <th>Receipt Number</th>
              <th>Created At</th>
              <th>Actions</th>
          </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
          <tr style="font-weight:bold; background:#f0f0f0;">
              <td colspan="3" style="text-align:center;">Total Expenses</td>
              <td id="grandTotal">UGX 0</td>
              <td colspan="4"></td>
          </tr>
      </tfoot>
  </table>
</div>

<div class="loader"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>

<!-- ✏️ Edit Expense Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <span class="close">&times;</span>

    <h2 style="text-align:center;">Update Expense</h2>

    <form id="editExpenseForm" enctype="multipart/form-data">

      <input type="hidden" name="id" id="edit_id">

      <label>Expense Name</label>
      <input type="text" name="expense_name" id="edit_expense" required>

      <label>Category</label>
      <input type="text" name="category" id="edit_category" required>

      <label>Amount (UGX)</label>
      <input type="number" name="amount" id="edit_amount" required>

      <label>Date</label>
      <input type="date" name="date" id="edit_date" required>

      <!-- Receipt Fields -->
      <div id="edit_receipt_fields" style="display:none;">
        <label>Receipt Number</label>
        <input type="text" name="receipt_number" id="edit_receipt_number">

        <label>Replace Receipt</label>
        <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf">
      </div>

      <button type="submit" id="saveEdit">Save Changes</button>

    </form>
  </div>
</div>


<script>
$(document).ready(function(){

    const modal = $("#editModal");

    /* =========================
       LOAD EXPENSE DATA
    ========================== */
    function loadData() {
        $(".loader").show();

        const search    = $("#search").val();
        const from_date = $("#from_date").val();
        const to_date   = $("#to_date").val();
        const limit     = $("#recordLimit").val();

        // Sync export form
        $("#pdf_search").val(search);
        $("#pdf_from").val(from_date);
        $("#pdf_to").val(to_date);
        $("#pdf_limit").val(limit);

        $.get("fetch_expenses.php",
            { search, from_date, to_date, limit, t: Date.now() },
            function(data){

                let tbody = "";
                let grandTotal = 0;
                let serial = 1;

                if (data.records && data.records.length > 0) {
                    data.records.forEach(r => {
                        tbody += `
                            <tr>
                                <td>${serial}</td>
                                <td>${r.expense_name}</td>
                                <td>${r.category}</td>
                                <td>UGX ${parseFloat(r.amount).toLocaleString()}</td>
                                <td>${r.added_by}</td>
                                <td>${r.date ? r.date.split(' ')[0] : ''}</td>
                                <td>${r.receipt_number ?? ''}</td>
                                <td>${r.created_at}</td>
                                <td>
                                    <button class="action-btn edit-btn" data-id="${r.id}">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <button class="action-btn delete-btn" data-id="${r.id}">
                                        <i class="fa fa-trash"></i> Del
                                    </button>
                                </td>
                            </tr>`;
                        grandTotal += parseFloat(r.amount);
                        serial++;
                    });
                } else {
                    tbody = `<tr><td colspan="9" style="text-align:center;">No records found</td></tr>`;
                }

                $("#expenses-table tbody").html(tbody);
                $("#grandTotal").text("UGX " + grandTotal.toLocaleString());
                $(".loader").hide();
            },
            "json"
        ).fail(function(){
            $(".loader").hide();
            alert("Error fetching data.");
        });
    }

    /* =========================
       INITIAL LOAD & FILTERS
    ========================== */
    loadData();

    $("#searchBtn, #search, #from_date, #to_date, #recordLimit")
        .on("click keyup change", loadData);

    /* =========================
       OPEN EDIT MODAL
    ========================== */
    $(document).on("click", ".edit-btn", function(){
        const id = $(this).data("id");

        $.get("get_expense.php",
            { id, t: Date.now() },
            function(res){
                if (res.error) {
                    alert(res.message);
                    return;
                }

                $("#edit_id").val(res.id);
                $("#edit_expense").val(res.expense_name);
                $("#edit_category").val(res.category);
                $("#edit_amount").val(res.amount);
                $("#edit_date").val(res.date ? res.date.split(" ")[0] : "");
                $("#edit_receipt_number").val(res.receipt_number);

                // Toggle receipt fields on load
                if (parseFloat(res.amount) >= 25000) {
                    $("#edit_receipt_fields").show();
                    $("#edit_receipt_number").prop("required", true);
                } else {
                    $("#edit_receipt_fields").hide();
                    $("#edit_receipt_number").prop("required", false);
                }

                modal.show();
            },
            "json"
        );
    });

    $(".close").click(() => modal.hide());
    $(window).click(e => {
        if (e.target.id === "editModal") modal.hide();
    });

    /* =========================
       TOGGLE RECEIPT FIELDS
    ========================== */
    $("#edit_amount").on("input", function () {
        if (parseFloat(this.value) >= 25000) {
            $("#edit_receipt_fields").slideDown();
            $("#edit_receipt_number").prop("required", true);
        } else {
            $("#edit_receipt_fields").slideUp();
            $("#edit_receipt_number").prop("required", false);
        }
    });

    /* =========================
       SAVE EDIT (WITH UPLOAD)
    ========================== */
    $("#editExpenseForm").on("submit", function(e){
        e.preventDefault();

        const amount = parseFloat($("#edit_amount").val());
        const receiptNumber = $("#edit_receipt_number").val();

        if (amount >= 25000 && !receiptNumber) {
            alert("Receipt number is required for amounts ≥ 25,000 UGX");
            return;
        }

        const formData = new FormData(this);

        $.ajax({
            url: "update_expense.php",
            type: "POST",
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(resp){
                alert(resp.message);
                if (!resp.error) {
                    modal.hide();
                    location.reload();
                }
            },
            error: function(xhr){
                console.error(xhr.responseText);
                alert("Server error. Check console.");
            }
        });
    });

    /* =========================
       DELETE EXPENSE
    ========================== */
    $(document).on("click", ".delete-btn", function(){

        const id = $(this).data("id");

        if (!confirm("Are you sure you want to delete this expense?")) return;

        $.post("delete_expense.php",
            { id },
            function(resp){
                alert(resp.message);
                if (!resp.error) loadData();
            },
            "json"
        );
    });

});
</script>


</main>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
