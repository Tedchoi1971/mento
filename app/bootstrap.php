<?php
declare(strict_types=1);
$configPath=__DIR__.'/../config/config.php';if(!is_file($configPath))throw new RuntimeException('config/config.php가 필요합니다.');$config=require $configPath;
spl_autoload_register(function(string $class):void{$file=__DIR__.'/'.$class.'.php';if(is_file($file))require $file;});
$db=new PDO($config['db']['dsn'],$config['db']['user'],$config['db']['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
session_name($config['app']['session_name']);session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>strpos($config['app']['base_url'],'https://')===0,'httponly'=>true,'samesite'=>'Lax']);session_start();if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));
function jsonResponse(array $data,int $status=200):void{http_response_code($status);header('Content-Type: application/json; charset=utf-8');header('X-Content-Type-Options: nosniff');header('Cache-Control: no-store');echo json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);exit;}
function bodyJson():array{$body=json_decode((string)file_get_contents('php://input'),true);return is_array($body)?$body:[];}
function requireAuth():int{if(empty($_SESSION['user_id']))jsonResponse(['error'=>'로그인이 필요합니다.'],401);return(int)$_SESSION['user_id'];}
function requireCsrf():void{$token=$_SERVER['HTTP_X_CSRF_TOKEN']??'';if(!$token||!hash_equals((string)($_SESSION['csrf']??''),$token))jsonResponse(['error'=>'보안 토큰이 올바르지 않습니다.'],419);}
function audit(PDO $db,int $userId,string $action,?string $entityType=null,?int $entityId=null,array $detail=[]):void{$st=$db->prepare('INSERT INTO audit_logs(user_id,action,entity_type,entity_id,ip_address,detail_json)VALUES(?,?,?,?,?,?)');$st->execute([$userId,$action,$entityType,$entityId,$_SERVER['REMOTE_ADDR']??null,json_encode($detail,JSON_UNESCAPED_UNICODE)]);}
function uploadedFiles(string $field):array{$files=$_FILES[$field]??null;if(!$files)return[];if(!is_array($files['name']))return[$files];$result=[];foreach($files['name']as$i=>$name)$result[]=['name'=>$name,'type'=>$files['type'][$i]??'','tmp_name'=>$files['tmp_name'][$i]??'','error'=>$files['error'][$i]??UPLOAD_ERR_NO_FILE,'size'=>$files['size'][$i]??0];return$result;}
