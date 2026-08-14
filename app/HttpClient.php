<?php
declare(strict_types=1);
final class HttpClient {
 public static function request(string $method,string $url,array $headers=[],?string $body=null):array {
  $ch=curl_init($url);curl_setopt_array($ch,[CURLOPT_CUSTOMREQUEST=>$method,CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>120,CURLOPT_HTTPHEADER=>$headers,CURLOPT_HEADER=>true]);if($body!==null)curl_setopt($ch,CURLOPT_POSTFIELDS,$body);
  $raw=curl_exec($ch);if($raw===false){$error=curl_error($ch);curl_close($ch);throw new RuntimeException('외부 API 연결 실패: '.$error);}$status=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);$size=(int)curl_getinfo($ch,CURLINFO_HEADER_SIZE);curl_close($ch);return ['status'=>$status,'headers'=>substr($raw,0,$size),'body'=>substr($raw,$size)];
 }
 public static function json(string $method,string $url,array $headers=[],?array $payload=null):array {
  $headers[]='Content-Type: application/json';$r=self::request($method,$url,$headers,$payload===null?null:json_encode($payload,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));$data=json_decode($r['body'],true);if($r['status']<200||$r['status']>=300)throw new RuntimeException('외부 API 오류('.$r['status'].'): '.substr((string)($data['error']['message']??$r['body']),0,500));return is_array($data)?$data:[];
 }
}
