<?php
require 'db.php';

$action = $_GET['action'] ?? '';

/* ---------------- CREATE EVENT ---------------- */
if($action == 'createEvent' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'] ?? '';
    $date = $_POST['date'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $token = bin2hex(random_bytes(8));

    $stmt = $conn->prepare("INSERT INTO events(name,date,location,description,link_token) VALUES(?,?,?,?,?)");
    if(!$stmt){
        echo json_encode(['status'=>'error','message'=>$conn->error]);
        exit;
    }
    $stmt->bind_param("sssss", $name, $date, $location, $description, $token);
    if($stmt->execute()){
        echo json_encode([
            'status'=>'ok',
            'link'=>"https://localhost/church/register.php?event=$token"
        ]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Failed to create event']);
    }
    exit;
}

/* ---------------- GET EVENTS ---------------- */
if($action === 'getEvents'){
    $res = $conn->query("
        SELECT e.id, e.name, e.date AS event_date, e.location, e.link_token,
        (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registrations
        FROM events e
        ORDER BY e.date DESC
    ");
    $events = [];
    while($row = $res->fetch_assoc()){
        $events[] = $row;
    }
    echo json_encode($events);
    exit;
}

/* ---------------- GET REGISTRATIONS ---------------- */
if($action === 'getRegistrations' && isset($_GET['event_id'])){
    $event_id = (int)$_GET['event_id'];
    $res = $conn->query("
        SELECT r.name, r.contact, r.guests, r.registered_at, e.name AS event_name
        FROM registrations r
        JOIN events e ON e.id = r.event_id
        WHERE r.event_id = $event_id
        ORDER BY r.registered_at DESC
    ");
    $regs = [];
    while($row = $res->fetch_assoc()){
        $regs[] = $row;
    }
    echo json_encode($regs);
    exit;
}

/* ---------------- DELETE EVENT ---------------- */
if($action === 'deleteEvent' && isset($_POST['event_id'])){
    $event_id = (int)$_POST['event_id'];

    // Delete registrations first
    $stmt1 = $conn->prepare("DELETE FROM registrations WHERE event_id=?");
    $stmt1->bind_param("i", $event_id);
    $stmt1->execute();

    // Delete the event
    $stmt2 = $conn->prepare("DELETE FROM events WHERE id=?");
    $stmt2->bind_param("i", $event_id);

    if($stmt2->execute()){
        echo json_encode(['status'=>'ok']);
    } else {
        echo json_encode(['status'=>'error','message'=>$stmt2->error]);
    }
    exit;
}

/* ---------------- EDIT EVENT ---------------- */
if($action === 'editEvent' && isset($_POST['event_id'])){
    $event_id = (int)$_POST['event_id'];
    $name = $_POST['name'] ?? '';
    $date = $_POST['date'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';

    $stmt = $conn->prepare("UPDATE events SET name=?, date=?, location=?, description=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $date, $location, $description, $event_id);

    if($stmt->execute()){
        echo json_encode(['status'=>'ok']);
    } else {
        echo json_encode(['status'=>'error','message'=>$stmt->error]);
    }
    exit;
}

/* ---------------- INVALID ACTION ---------------- */
echo json_encode(['status'=>'error','message'=>'Invalid action']);
exit;
?>
