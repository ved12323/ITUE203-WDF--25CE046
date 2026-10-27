<?php
require_once __DIR__.'/../db.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$method=$_SERVER['REQUEST_METHOD'];
if($method==='GET') { $rows=$pdo->query('SELECT id,title,category,event_date,venue,description,poster FROM events ORDER BY event_date')->fetchAll(); echo json_encode(['success'=>true,'data'=>$rows],JSON_UNESCAPED_UNICODE); exit; }
if(empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='admin') { http_response_code(403); echo json_encode(['success'=>false,'message'=>'Admin login required']); exit; }
$payload=json_decode(file_get_contents('php://input'),true) ?? $_POST;
if($method==='POST') {
 $title=trim($payload['title']??''); $category=trim($payload['category']??'General'); $date=$payload['event_date']??''; $venue=trim($payload['venue']??'');
 if($title===''||$venue===''||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Title, valid date, and venue are required']);exit;}
 $q=$pdo->prepare('INSERT INTO events(title,category,event_date,venue,description,created_by) VALUES(?,?,?,?,?,?)');$q->execute([$title,$category,$date,$venue,trim($payload['description']??''),$_SESSION['user_id']]);http_response_code(201);echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId(),'message'=>'Event created']);exit;
}
if($method==='PUT') { $id=(int)($payload['id']??0);$title=trim($payload['title']??'');$date=$payload['event_date']??'';$venue=trim($payload['venue']??'');if(!$id||$title===''||$venue===''||!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)){http_response_code(422);echo json_encode(['success'=>false,'message'=>'ID, title, valid date, and venue are required']);exit;}$q=$pdo->prepare('UPDATE events SET title=?,category=?,event_date=?,venue=?,description=? WHERE id=?');$q->execute([$title,trim($payload['category']??'General'),$date,$venue,trim($payload['description']??''),$id]);echo json_encode(['success'=>true,'message'=>'Event updated']);exit;}
if($method==='DELETE') { $id=(int)($payload['id']??0);if(!$id){http_response_code(422);echo json_encode(['success'=>false,'message'=>'Event ID required']);exit;}$q=$pdo->prepare('DELETE FROM events WHERE id=?');$q->execute([$id]);echo json_encode(['success'=>true,'message'=>'Event deleted']);exit; }
header('Allow: GET, POST, PUT, DELETE');http_response_code(405);echo json_encode(['success'=>false,'message'=>'Method not allowed']);
?>
