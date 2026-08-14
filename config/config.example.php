<?php
return [
 'app'=>['env'=>getenv('APP_ENV')?:'production','base_url'=>getenv('APP_URL')?:'https://example.com/compass','session_name'=>'compass_session'],
 'db'=>['dsn'=>getenv('DB_DSN')?:'mysql:host=localhost;dbname=compass;charset=utf8mb4','user'=>getenv('DB_USER')?:'compass_user','password'=>getenv('DB_PASSWORD')?:''],
 'openai'=>['api_key'=>getenv('OPENAI_API_KEY')?:'','model'=>getenv('OPENAI_MODEL')?:'gpt-5.6-luna'],
 'google'=>['client_id'=>getenv('GOOGLE_CLIENT_ID')?:'','client_secret'=>getenv('GOOGLE_CLIENT_SECRET')?:'','refresh_token'=>getenv('GOOGLE_REFRESH_TOKEN')?:''],
 'security'=>['allowed_origins'=>[getenv('APP_URL')?:'https://example.com/compass'],'max_document_bytes'=>25*1024*1024],
];
