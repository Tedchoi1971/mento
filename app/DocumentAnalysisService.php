<?php
declare(strict_types=1);
final class DocumentAnalysisService {
 private PDO $db;private OpenAIClient $openai;private int $maxBytes;
 public function __construct(PDO $db,OpenAIClient $openai,int $maxBytes){$this->db=$db;$this->openai=$openai;$this->maxBytes=$maxBytes;}
 public function analyzeUpload(array $file,?int $companyId,?int $grantId,int $userId):array {
  if(($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK)throw new InvalidArgumentException('PDF 업로드에 실패했습니다.');$path=(string)$file['tmp_name'];$size=(int)$file['size'];$name=basename((string)$file['name']);
  if(!is_uploaded_file($path))throw new InvalidArgumentException('유효한 업로드 파일이 아닙니다.');if($size<5||$size>$this->maxBytes)throw new InvalidArgumentException('PDF 파일 크기 제한을 확인해 주세요.');$head=(string)file_get_contents($path,false,null,0,5);if($head!=='%PDF-')throw new InvalidArgumentException('PDF 형식만 분석할 수 있습니다.');
  $hash=hash_file('sha256',$path);$st=$this->db->prepare("INSERT INTO documents(company_id,grant_program_id,original_name,mime_type,file_size,content_hash,analysis_status,created_by)VALUES(?,?,?,?,?,?,'processing',?)");$st->execute([$companyId,$grantId,$name,'application/pdf',$size,$hash,$userId]);$id=(int)$this->db->lastInsertId();$openaiFile=null;
  try{$openaiFile=$this->openai->upload($path,$name,'application/pdf');$result=$this->openai->analyze($openaiFile,$grantId?'공모사업 공고문':'기업 진단자료');$analysis=json_encode($result['data'],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$evidence=json_encode($result['data']['evidence']??[],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);$this->db->prepare("UPDATE documents SET analysis_status='completed',analysis_json=?,evidence_json=?,analyzed_at=NOW() WHERE id=?")->execute([$analysis,$evidence,$id]);return ['id'=>$id,'name'=>$name,'status'=>'completed','analysis'=>$result['data']];}
  catch(Throwable $e){$this->db->prepare("UPDATE documents SET analysis_status='failed',error_message=? WHERE id=?")->execute([substr($e->getMessage(),0,1000),$id]);throw $e;}
  finally{if($openaiFile)$this->openai->deleteFile($openaiFile);if(is_file($path)){file_put_contents($path,'');@unlink($path);}}
 }
}
