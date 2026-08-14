# Compass — 기업 성장 진단 MVP

공유 대화의 요구사항을 바탕으로 구현한 PHP 7.4 + MariaDB 10.0 서비스 골격과 실행형 UI입니다. MariaDB 10.0에는 네이티브 JSON 타입이 없으므로 구조화 데이터 컬럼은 UTF-8 `LONGTEXT`로 저장합니다.

## 구현 범위

- 기업 현황 대시보드와 진단 파이프라인
- 기업 등록, 검색, 상세 화면(브라우저 localStorage 저장)
- 종합 진단, AI Readiness, 재무 점수와 레이더 시각화
- Pain Point, KPI, AI Opportunity 화면
- 기업별 맞춤 인터뷰 질문과 미팅 가이드
- 진단자료 체크리스트, 다중 파일 등록과 AI 사전분석 흐름
- 공모사업 공고 분석, 기업 적합성 매칭과 공모별 맞춤 질문지
- MariaDB 9개 테이블 및 기업 목록/등록 API
- 반응형 모바일 UI

## 데모 실행

`public/index.html`을 브라우저에서 열면 설치 없이 동작합니다. 또는 웹서버의 document root를 이 폴더로 지정하세요.

## 카페24 연결

1. phpMyAdmin에서 `database/schema.sql`을 실행합니다.
2. `config/config.example.php`를 `config/config.php`로 복사합니다.
3. `config.php`의 MariaDB 계정정보를 입력합니다.
4. 이 폴더 전체를 웹 루트에 업로드하고 Apache rewrite를 활성화합니다.
5. AI 기능을 붙일 때 서버 환경변수 `OPENAI_API_KEY`를 설정합니다. 키를 JS나 Git에 넣지 마세요.

현재 UI는 실제 업무 흐름을 검증하는 1차 MVP입니다. 다음 구현 우선순위는 문서 업로드 → 텍스트 추출 → 구조화된 AI 분석 → 규칙 기반 점수 계산 → PDF 보고서 생성입니다.
