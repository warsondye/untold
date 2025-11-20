<?php
// api.php - Church Dashboard API (Attendance & Leads) - Full Version
header('Content-Type: application/json');
require_once 'db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function respond($data){
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

function safeLimit($n){ return max(1, min(intval($n), 500)); }

function formatDate($dateStr){
    return date('d F Y', strtotime($dateStr));
}

function buildWhere($fields, $search, $year=null){
    $clauses=[]; $params=[]; $types='';
    if($search){
        $terms = explode(' ', $search);
        $likeClauses=[];
        foreach($terms as $t){
            $sub=[];
            foreach($fields as $f){
                $sub[]="$f LIKE ?";
                $params[]="%$t%";
                $types.='s';
            }
            $likeClauses[]='('.implode(' OR ', $sub).')';
        }
        $clauses[]=implode(' AND ', $likeClauses);
    }
    if($year){
        $clauses[]="YEAR({$fields[0]})=?";
        $params[]=$year;
        $types.='i';
    }
    return [ $clauses ? 'WHERE '.implode(' AND ', $clauses) : '', $types, $params ];
}

// ------------------------
// MAIN ACTION SWITCH
// ------------------------
switch($action){

    // ------------------------
    // ATTENDANCE ENDPOINTS
    // ------------------------

    case 'getAttendanceMonthly':
        $year = $_GET['year'] ?? 'all';
        $sql = "SELECT MONTH(attendance_date) AS month, SUM(attendance_count) AS total FROM attendance";
        $params=[]; $types='';
        if($year!=='all'){
            $sql.=" WHERE YEAR(attendance_date)=?";
            $types='i'; $params=[intval($year)];
        }
        $sql.=" GROUP BY MONTH(attendance_date) ORDER BY month";
        $stmt=$conn->prepare($sql);
        if($params) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $res=$stmt->get_result();
        $data=array_fill(1,12,0);
        while($r=$res->fetch_assoc()) $data[intval($r['month'])]=intval($r['total']);
        respond(['status'=>'ok','year'=>$year,'data'=>array_values($data)]);
        break;

    case 'getAttendanceYearly':
        $res = $conn->query("SELECT YEAR(attendance_date) AS year, SUM(attendance_count) AS total FROM attendance GROUP BY YEAR(attendance_date) ORDER BY year");
        $rows=[];
        while($r=$res->fetch_assoc()) $rows[]=['year'=>intval($r['year']),'total'=>intval($r['total'])];
        respond(['status'=>'ok','data'=>$rows]);
        break;

    case 'getAttendanceRows':
        $limit = safeLimit($_GET['limit'] ?? 50);
        $search = trim($_GET['search'] ?? '');
        $year = $_GET['year'] ?? null;
        list($where, $types, $params) = buildWhere(['attendance_date','section','event_name'], $search, $year);
        $sql = "SELECT id, attendance_date, section, attendance_count, event_name FROM attendance $where ORDER BY attendance_date DESC LIMIT ?";
        $types.='i'; $params[]=$limit;
        $stmt=$conn->prepare($sql);
        if($params) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $res=$stmt->get_result();
        $rows=[];
        while($r=$res->fetch_assoc()){
            $r['formatted_date'] = formatDate($r['attendance_date']);
            $rows[] = $r;
        }
        respond(['status'=>'ok','data'=>$rows]);
        break;

    case 'addAttendance':
        $date = $_POST['attendance_date'] ?? null;
        $section = trim($_POST['section'] ?? '');
        $count = intval($_POST['attendance_count'] ?? 0);
        $event = trim($_POST['event_name'] ?? '');
        if(!$date) respond(['status'=>'error','message'=>'Missing date']);
        $stmt = $conn->prepare("INSERT INTO attendance(attendance_date, section, attendance_count, event_name) VALUES(?,?,?,?)");
        $stmt->bind_param('ssis', $date, $section, $count, $event);
        if($stmt->execute()){
    respond(['status'=>'ok','id'=>$stmt->insert_id]);
} else {
    respond(['status'=>'error','message'=>$stmt->error]);
}

        break;

    case 'deleteAttendance':
        $id = intval($_POST['id'] ?? 0);
        if(!$id) respond(['status'=>'error','message'=>'Missing ID']);
        $stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        respond(['status'=>'ok','affected'=>$stmt->affected_rows]);
        break;

    case 'getAttendanceTotals':
        $year = $_GET['year'] ?? null;

        // Year total
        if($year && $year!=='all'){
            $stmt=$conn->prepare("SELECT SUM(attendance_count) AS total, COUNT(*) AS events FROM attendance WHERE YEAR(attendance_date)=?");
            $stmt->bind_param('i', $year);
            $stmt->execute();
            $row=$stmt->get_result()->fetch_assoc();
            $yearTotal=intval($row['total'] ?? 0);
            $yearEvents=intval($row['events'] ?? 0);
            $avgPerService = $yearEvents ? round($yearTotal/$yearEvents,2) : 0;
        } else {
            $yearTotal = $yearEvents = $avgPerService = 0;
        }

        // All-time total
        $resAll = $conn->query("SELECT SUM(attendance_count) AS total FROM attendance");
        $allTimeTotal = intval($resAll->fetch_assoc()['total'] ?? 0);

        // Special events
        $resSpecial = $conn->query("SELECT DISTINCT event_name FROM attendance WHERE event_name IS NOT NULL AND event_name != ''");
        $specialEvents = [];
        while($r=$resSpecial->fetch_assoc()) $specialEvents[]=$r['event_name'];

        respond([
            'status'=>'ok',
            'yearTotal'=>$yearTotal,
            'avgPerService'=>$avgPerService,
            'allTimeTotal'=>$allTimeTotal,
            'specialEvents'=>$specialEvents
        ]);
        break;
case 'getEventCount':
    $year = intval($_GET['year'] ?? 0);
    $event = trim($_GET['event_name'] ?? '');
    if(!$event) respond(['status'=>'error','message'=>'Missing event name']);
    $sql = "SELECT SUM(attendance_count) AS cnt FROM attendance WHERE event_name LIKE ?";
    $params = ["%$event%"];
    $types='s';
    if($year){
        $sql.=" AND YEAR(attendance_date)=?";
        $params[]=$year; $types.='i';
    }
    $stmt=$conn->prepare($sql);
    $stmt->bind_param($types,...$params);
    $stmt->execute();
    $res=$stmt->get_result();
    $count = intval($res->fetch_assoc()['cnt'] ?? 0);
    respond(['status'=>'ok','total'=>$count]);
    break;

        // Fetch all dates for this event
        $sql2 = "SELECT attendance_date, attendance_count FROM attendance WHERE event_name LIKE ?";
        $params2 = ["%$event%"];
        if($year){ $sql2.=" AND YEAR(attendance_date)=?"; $params2[]=$year; }
        $stmt2=$conn->prepare($sql2);
        $stmt2->bind_param(str_repeat('s',count($params2)), ...$params2);
        $stmt2->execute();
        $res2=$stmt2->get_result();
        $dates = [];
        while($r=$res2->fetch_assoc()){
            $dates[]=['date'=>$r['attendance_date'],'formatted_date'=>formatDate($r['attendance_date']),'count'=>intval($r['attendance_count'])];
        }

        respond(['status'=>'ok','total'=>$count,'dates'=>$dates]);
        break;

    // ------------------------
    // LEADS ENDPOINTS
    // ------------------------
    case 'getLeadsMonthly':
        $year = $_GET['year'] ?? 'all';
        $sql = "SELECT MONTH(`date`) AS month, COUNT(*) AS total FROM leads";
        $params=[]; $types='';
        if($year!=='all'){ $sql.=" WHERE YEAR(`date`)=?"; $types='i'; $params=[intval($year)]; }
        $sql.=" GROUP BY MONTH(`date`) ORDER BY month";
        $stmt=$conn->prepare($sql);
        if($params) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $res=$stmt->get_result();
        $data=array_fill(1,12,0);
        while($r=$res->fetch_assoc()) $data[intval($r['month'])]=intval($r['total']);
        respond(['status'=>'ok','year'=>$year,'data'=>array_values($data)]);
        break;

    case 'getLeadsYearly':
        $res=$conn->query("SELECT YEAR(`date`) AS year, COUNT(*) AS total FROM leads GROUP BY YEAR(`date`) ORDER BY year");
        $rows=[];
        while($r=$res->fetch_assoc()) $rows[]=['year'=>intval($r['year']),'total'=>intval($r['total'])];
        respond(['status'=>'ok','data'=>$rows]);
        break;

    case 'getLeadsRows':
        $limit = safeLimit($_GET['limit'] ?? 50);
        $search = trim($_GET['search'] ?? '');
        $year = $_GET['year'] ?? null;
        list($where,$types,$params) = buildWhere(['date','name','contact','lead_status'],$search,$year);
        $sql = "SELECT id,date,name,contact,job,reached_out_by,lead_status,remarks FROM leads $where ORDER BY date DESC LIMIT ?";
        $types.='i'; $params[]=$limit;
        $stmt=$conn->prepare($sql);
        if($params) $stmt->bind_param($types,...$params);
        $stmt->execute();
        $res=$stmt->get_result();
        $rows=[];
        while($r=$res->fetch_assoc()){
            $r['formatted_date'] = formatDate($r['date']);
            $rows[] = $r;
        }
        respond(['status'=>'ok','data'=>$rows]);
        break;

    case 'addLead':
        $date = $_POST['date'] ?? null;
        $name = trim($_POST['name'] ?? '');
        if(!$date || !$name) respond(['status'=>'error','message'=>'Missing date or name']);
        $stmt=$conn->prepare("INSERT INTO leads(`date`,name,contact,job,reached_out_by,lead_status,remarks) VALUES(?,?,?,?,?,?,?)");
        $stmt->bind_param('sssssss', $date, $name, $_POST['contact']??'', $_POST['job']??'', $_POST['reached_out_by']??'', $_POST['lead_status']??'', $_POST['remarks']??'');
        if($stmt->execute()){
    respond(['status'=>'ok','id'=>$stmt->insert_id]);
} else {
    respond(['status'=>'error','message'=>$stmt->error]);
}

        break;
case 'getSpecialEvents':
    $res = $conn->query("SELECT DISTINCT event_name FROM attendance WHERE event_name IS NOT NULL AND event_name != '' ORDER BY event_name");
    $events = [];
    while($r = $res->fetch_assoc()) $events[] = $r['event_name'];
    respond(['status'=>'ok','events'=>$events]);
    break;

    case 'deleteLead':
        $id=intval($_POST['id'] ?? 0);
        if(!$id) respond(['status'=>'error','message'=>'Missing ID']);
        $stmt=$conn->prepare("DELETE FROM leads WHERE id=?");
        $stmt->bind_param('i',$id); $stmt->execute();
        respond(['status'=>'ok','affected'=>$stmt->affected_rows]);
        break;

    default:
        respond(['status'=>'error','message'=>'Invalid action']);
}
