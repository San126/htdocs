<?php
header("Access-Control-Allow-Origin: *"); // optional: for cross-domain
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taxi_details";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

function getTaxiDetails($conn) {
    $sql = "SELECT * FROM taxies ORDER BY last_contacted ASC LIMIT 1";
    $result = $conn->query($sql);
    return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contacted'])) {
    header('Content-Type: application/json'); // ensure JSON response
    $contact = intval($_POST['contacted']);
    $currentDateTime = date('Y-m-d H:i:s');
    $updateSql = "UPDATE taxies SET last_contacted='$currentDateTime' WHERE contact=$contact";

    if ($conn->query($updateSql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
    }
    exit;
}

// For GET requests (load page)
$taxi = getTaxiDetails($conn);

if ($taxi) {
    echo "<div class='modal-content'>
            <h2>Taxi Details</h2>
            <form id='taxiForm'>
                <div class='form-group'><label>Contact:</label> " . htmlspecialchars($taxi['contact']) . "</div>
                <div class='form-group'><label>Name:</label> " . htmlspecialchars($taxi['name']) . "</div>
                <div class='form-group'><label>Last Contacted:</label> " . htmlspecialchars($taxi['last_contacted']) . "</div>
                <div class='form-group'>
                    <input type='checkbox' name='contacted' value='" . $taxi['contact'] . "'> Contacted
                </div>
                <div class='form-group'>
                    <button type='submit' class='btn btn-primary'>Update</button>
                </div>
            </form>
        </div>";
} else {
    echo "<div class='modal-content'><p>No taxi details found.</p></div>";
}

$conn->close();
