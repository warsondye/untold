<?php
require 'db.php';

$token = $_GET['event'] ?? '';
$event = $conn->prepare("SELECT * FROM events WHERE link_token=?");
if(!$event){
    die("<h2>Error fetching event: " . $conn->error . "</h2>");
}
$event->bind_param('s', $token);
$event->execute();
$eventData = $event->get_result()->fetch_assoc();

if(!$eventData) die("<h2>Invalid event link.</h2>");

$success = '';
$phoneToCopy = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $name = $_POST['name'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $guests = intval($_POST['guests'] ?? 0);

    // Validate phone number (digits only, 6-15 digits)
    if(!preg_match('/^\+?\d{6,15}$/', $contact)){
        $success = "Please enter a valid phone number!";
    } else {
        $stmt = $conn->prepare("INSERT INTO registrations(event_id,name,contact,guests,registered_at) VALUES(?,?,?,?,NOW())");
        if(!$stmt){
            die("<h2>Error preparing registration: " . $conn->error . "</h2>");
        }
        $stmt->bind_param("issi", $eventData['id'], $name, $contact, $guests);
        if($stmt->execute()){
            $success = "Registration successful! Thank you.";
            $phoneToCopy = $contact;
        } else {
            $success = "Failed to register. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - <?= htmlspecialchars($eventData['name']) ?></title>
<style>
:root{
  --bg:#e8f5e9;
  --card:#ffffff;
  --accent:#4caf50;
  --accent-dark:#2e7d32;
  --muted:#555;
  --hover:rgba(76,175,80,0.1);
  --shadow-light:rgba(0,0,0,0.06);
  --shadow-dark:rgba(0,0,0,0.15);
  --transition:0.3s;
}

*{margin:0;padding:0;box-sizing:border-box;font-family:'Inter',Arial,sans-serif;}

body{
  background:var(--bg);
  display:flex;
  justify-content:center;
  align-items:center;
  min-height:100vh;
  padding:20px;
}

.card{
  background:var(--card);
  padding:30px;
  border-radius:20px;
  box-shadow:0 10px 30px var(--shadow-dark);
  width:100%;
  max-width:450px;
  transition:var(--transition);
}

.card:hover{box-shadow:0 14px 40px var(--shadow-dark);}

h1,h2{
  text-align:center;
  color:var(--accent-dark);
  margin-bottom:10px;
}

h1{
  font-size:22px;
  font-weight:700;
}

h2{
  font-size:18px;
  margin-bottom:25px;
}

form{
  display:flex;
  flex-direction:column;
  gap:15px;
}

input, button{
  padding:12px;
  border-radius:10px;
  border:1px solid #ccc;
  font-size:14px;
  transition: var(--transition);
}

input:focus{
  outline:none;
  border-color:var(--accent);
  box-shadow:0 0 6px var(--accent-dark);
}

button{
  background:var(--accent);
  color:#fff;
  font-weight:600;
  border:none;
  cursor:pointer;
  transition: var(--transition);
}

button:hover{
  background:var(--accent-dark);
  transform:translateY(-2px);
}

.success{
  background:#d4edda;
  color:#155724;
  padding:12px;
  border-radius:10px;
  text-align:center;
  font-weight:600;
  margin-bottom:15px;
}

@media(max-width:500px){
  .card{padding:20px;}
  h1{font-size:20px;}
  h2{font-size:16px;}
}
</style>
</head>
<body>

<div class="card">
  <h1>City Gate Chapel – Elohim Sanctuary</h1>
  <h2>Register for <?= htmlspecialchars($eventData['name']) ?></h2>

  <?php if($success): ?>
    <div class="success"><?= $success ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="tel" name="contact" placeholder="Phone Number" required pattern="\+?\d{6,15}" title="Enter a valid phone number">
    <input type="number" name="guests" min="0" placeholder="Number of Guests">
    <button type="submit">Register Now</button>
  </form>
</div>

<?php if($phoneToCopy): ?>
<script>
// Copy phone number to clipboard automatically
navigator.clipboard.writeText("<?= $phoneToCopy ?>")
.then(()=>alert("Phone number <?= $phoneToCopy ?> copied to clipboard!"))
.catch(err=>console.error("Clipboard copy failed:", err));
</script>
<?php endif; ?>

</body>
</html>
