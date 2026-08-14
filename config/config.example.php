<?php
return ['db'=>['dsn'=>'mysql:host=localhost;dbname=compass;charset=utf8mb4','user'=>'compass_user','password'=>'CHANGE_ME'],'openai_api_key'=>getenv('OPENAI_API_KEY')?:'','openai_model'=>getenv('OPENAI_MODEL')?:'gpt-5-mini'];
