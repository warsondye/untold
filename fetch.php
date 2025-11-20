<?php
header("Content-Type: application/json");
require_once "db.php";

// If no type is provided, return error
if (!isset($_GET['type'])) {
    echo json_encode(["error" => "No type provided."]);
    exit;
}

$type = $_GET['type'];

/**
 * Helper: Fetch monthly totals from a table
 */
function getMonthlyTotals($conn, $table, $column) {
    $sql = "
        SELECT 
            MONTH(date) AS month, 
            COUNT($column) AS total
        FROM $table
        GROUP BY MONTH(date)
        ORDER BY MONTH(date)
    ";
    $result = $conn->query($sql);

    $data = array_fill(1, 12, 0); // Default 12 months

    while ($row = $result->fetch_assoc()) {
        $data[(int)$row['month']] = (int)$row['total'];
    }

    return array_values($data);
}

/**
 * Helper: Fetch yearly totals from a table
 */
function getYearlyTotals($conn, $table, $column) {
    $sql = "
        SELECT 
            YEAR(date) AS year, 
            COUNT($column) AS total
        FROM $table
        GROUP BY YEAR(date)
        ORDER BY YEAR(date)
    ";
    $result = $conn->query($sql);

    $years = [];
    $values = [];

    while ($row = $result->fetch_assoc()) {
        $years[] = $row['year'];
        $values[] = (int)$row['total'];
    }

    return ["years" => $years, "values" => $values];
}

/**
 * -----------------------
 * FETCH TYPES
 * -----------------------
 */

if ($type === "attendance_monthly") {
    echo json_encode([
        "months" => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        "data" => getMonthlyTotals($conn, "attendance", "id")
    ]);
    exit;
}

if ($type === "attendance_yearly") {
    echo json_encode(getYearlyTotals($conn, "attendance", "id"));
    exit;
}

if ($type === "leads_monthly") {
    echo json_encode([
        "months" => ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
        "data" => getMonthlyTotals($conn, "leads", "id")
    ]);
    exit;
}

if ($type === "leads_yearly") {
    echo json_encode(getYearlyTotals($conn, "leads", "id"));
    exit;
}

// Unknown request
echo json_encode(["error" => "Invalid fetch type."]);
exit;
