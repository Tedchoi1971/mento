<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$path=__DIR__.'/../config/config.php';
if(!is_file($path)){http_response_code(503);echo json_encode(['error'=>'config/config.php가 필요합니다.']);exit;}
$config=require $path;
try{$db=new PDO($config['db']['dsn'],$config['db']['user'],$config['db']['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);}catch(Throwable $e){http_response_code(503);echo json_encode(['error'=>'데이터베이스 연결 실패']);exit;}
$method=$_SERVER['REQUEST_METHOD'];$resource=basename(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));
if($resource==='health'){echo json_encode(['ok'=>true]);exit;}
if($resource==='companies'&&$method==='GET'){$q=$db->query('SELECT id,name,industry,ceo_name AS ceo,email,stage,updated_at FROM companies ORDER BY updated_at DESC');echo json_encode(['data'=>$q->fetchAll()],JSON_UNESCAPED_UNICODE);exit;}
if($resource==='companies'&&$method==='POST'){$b=json_decode(file_get_contents('php://input'),true)?:[];if(empty(trim((string)($b['name']??'')))||empty(trim((string)($b['industry']??'')))){http_response_code(422);echo json_encode(['error'=>'기업명과 업종은 필수입니다.']);exit;}$s=$db->prepare('INSERT INTO companies(name,industry,ceo_name,email,memo,stage) VALUES(?,?,?,?,?,?)');$s->execute([$b['name'],$b['industry'],$b['ceo']??null,$b['email']??null,$b['memo']??null,$b['stage']??'자료 요청']);http_response_code(201);echo json_encode(['id'=>(int)$db->lastInsertId()]);exit;}
http_response_code(404);echo json_encode(['error'=>'API를 찾을 수 없습니다.']);
