# 그누보드5 대비 변경 사항

이 문서는 G5 Codex가 공식 그누보드5와 달라진 지점을 운영, 개발, 마이그레이션 관점에서 정리한다.

## 비교 기준

- G5 Codex 기준: `main` `05a57f7e0278de1a12714b16435a2b4cbf215b5a`, 2026-04-29
- 공식 그누보드5 기준: `gnuboard/gnuboard5` `master` `65a419e9cdf1b86c231d57ec0904960f9932f8ec`, 2026-04-16, 버전 5.6.26
- 비교 준비: `git fetch https://github.com/gnuboard/gnuboard5.git master:refs/remotes/gnuboard5/current-master`
- 비교 명령: `git diff --shortstat remotes/gnuboard5/current-master HEAD`
- 차이 규모: 3205개 파일 변경, 34951줄 추가, 455267줄 삭제

G5 Codex는 그누보드5 전체 배포판을 그대로 확장한 버전이 아니다. 전역 함수와 include 기반 실행 모델 일부를 유지하되, 회원 중심 애플리케이션과 커뮤니티 도메인에 필요한 기능만 남기고 구조를 크게 줄인 별도 런타임이다.

## 방향성 요약

- 쇼핑몰, SMS, FAQ, 설문, QA, 방문 통계, 기본 테마/스킨 묶음 등 범용 포털/쇼핑몰 기능을 제거했다.
- 기존 `bbs/` 중심 회원/게시판 흐름을 `member/`와 `community/` 도메인으로 분리했다.
- 회원, 관리자, 커뮤니티, 사이트 관리 기능을 `lib/domain/*` 아래 request, validation, persist, render, flow 계층으로 나눴다.
- 게시판별 동적 테이블 대신 커뮤니티 단일 테이블 세트를 사용한다.
- 전역 request escape는 레거시 호환을 위해 남기되, 신규 코드는 raw input과 prepared SQL helper를 사용하도록 정리했다.
- 설정, 설치, 보안 토큰, 임시 파일, 프론트엔드 빌드 흐름을 운영 환경 기준으로 재구성했다.

## 디렉터리 구조 변경

### 추가 또는 재구성

- `member/`: 로그인, 로그아웃, 회원가입, 이메일 인증, 비밀번호 재설정, 회원 탈퇴 등 회원 기능 전용 진입점
- `community/`: 게시판, 게시글, 댓글, 첨부파일, 최신글, 스크랩, 커뮤니티 포인트 사용자 진입점
- `lib/domain/member/`: 회원 request, validation, persist, render, flow 계층
- `lib/domain/admin/`: 관리자 인증, UI shell, 회원 관리, XLSX export, 설정 저장 계층
- `lib/domain/community/`: 커뮤니티 런타임, 권한, 게시글/댓글/첨부/최신글/스크랩/포인트 처리 계층
- `lib/domain/site/`: 사이트 메뉴, 배너, 독립 페이지 관리 계층
- `tailwind4/`: Tailwind 4 입력 CSS
- `docs/`: 현행 구조, 보안 정책, 성능 점검 문서
- `scripts/`: 보안 정책 점검 스크립트

### 제거 또는 축소

- `bbs/`: 기존 그누보드5의 게시판/회원/검색/쪽지/포인트/설문 진입점을 제거하고 필요한 기능을 `member/`, `community/`로 이전했다.
- `shop/`, `adm/shop_admin/`, `shop.config.php`, `gnuboard5shop.sql`: 영카트 쇼핑몰 기능을 제거했다.
- `adm/sms_admin/`: SMS 관리 기능을 제거했다.
- `theme/`, `skin/`, `mobile/`, 대량 이미지 자산: 기본 테마/스킨 묶음을 제거하고 프로젝트 전용 화면으로 대체했다.
- `extend/`: 범용 확장 자동 로딩 경로를 제거했다.
- `g4_import.php`, `yc4_import.php` 등 구버전 마이그레이션 도구를 제거했다.

## 런타임과 설정

- 최소 PHP 버전은 카페24 뉴아우토반 신규 신청 환경의 최저 제공 버전에 맞춰 7.4.0으로 둔다.
- DB 요구사항은 MariaDB 10.x 또는 MySQL 5.7 호환 환경으로 명시한다.
- `config.runtime.php`를 추가해 앱 이름, 도메인, HTTPS 도메인, 쿠키 도메인, debug, SQL debug, DB 설정 파일명, DB engine/charset, SMTP host/port를 환경별로 분리했다.
- `G5_VERSION`은 고정 버전 문자열 대신 런타임 설정의 `app_name`을 사용한다.
- `G5_MEMBER_ONLY`가 true로 설정되어 회원 중심 런타임임을 명시한다.
- `G5_MEMBER_DIR`, `G5_COMMUNITY_DIR`와 관련 URL/PATH 상수를 추가했다.
- `common.php`는 bootstrap을 `lib/bootstrap/*`로 분리하고 raw request snapshot을 먼저 저장한 뒤 레거시 escape를 적용한다.
- 세션, 자동 로그인 복원, IP 접근 정책, 회원 상태 해석, 기기 모드 판별을 bootstrap helper로 이동했다.

## 입력, SQL, 보안 토큰

- 전역 `$_GET`, `$_POST`, `$_COOKIE`, `$_REQUEST` escape는 기존 호환을 위해 유지한다.
- escape 이전 원본 입력은 `g5_get_runtime_raw_get_input()`, `g5_get_runtime_raw_post_input()`, `g5_get_runtime_raw_cookie_input()`, `g5_get_runtime_raw_request_input()`으로 접근한다.
- PDO 기반 prepared helper를 추가했다.
  - `sql_query_prepared()`
  - `sql_statement_prepared()`
  - `sql_fetch_prepared()`
  - `sql_fetch_all_prepared()`
  - `sql_fetch_value_prepared()`
- 신규 토큰 생성은 `g5_generate_hex_token()`, `g5_secure_random_int()`, `g5_build_hmac_token()`을 사용한다.
- 토큰 비교는 `g5_hash_equals()` 기반 상수시간 비교를 사용한다.
- 설치 시 `G5_TOKEN_ENCRYPTION_KEY`를 `data/dbconfig.php`에 기록해 HMAC 기반 검증 키에 사용한다.
- 보안 작성 기준은 `docs/security-input-sql-policy.md`와 `scripts/check-security-policy.sh`로 별도 관리한다.

## 회원 도메인

기존 그누보드5의 `bbs/login.php`, `bbs/register_form.php`, `bbs/password_lost.php` 같은 회원 진입점을 `member/` 아래로 분리했다.

주요 변경 사항:

- 회원 페이지 controller와 skin을 `member/` 및 `member/views/basic/`로 재배치
- 이메일 인증, 비밀번호 찾기/재설정, 회원정보 확인, 본인인증 갱신 흐름을 `lib/domain/member/flow-*`로 분리
- request 정규화, 검증, DB 저장, 응답 렌더링을 각각 `request-*`, `validation-*`, `persist-*`, `render-*` 파일로 분리
- AJAX 중복 확인을 `member/ajax.mb_id.php`, `member/ajax.mb_email.php`, `member/ajax.mb_nick.php`, `member/ajax.mb_hp.php`로 제공
- 회원 메일 템플릿을 `member/views/mail/`로 이동

## 관리자 도메인

관리자 기능은 범용 그누보드5 관리자에서 회원/사이트/커뮤니티 운영 중심으로 재구성했다.

주요 변경 사항:

- 관리자 shell, 메뉴, 공통 UI helper를 `lib/domain/admin/`과 `adm/admin-*.js`로 분리
- 기본환경 설정 화면을 `adm/config_form_parts/` 부분 템플릿으로 분리
- 회원 목록, 검색, 상태 필터, 회원 수정 화면을 도메인 함수와 부분 템플릿으로 재구성
- 회원 XLSX export, 대용량 export stream, export 산출물 정리 기능을 추가
- 관리자 메뉴는 회원 관리, 커뮤니티 관리, 사이트 관리 중심으로 재배치

제거된 관리자 기능:

- 게시판별 기존 관리 화면
- 쇼핑몰 관리자
- SMS 관리자
- 메일 발송, FAQ, 설문, 접속 통계, 테마 관리 등 현재 프로젝트 범위 밖 기능

## 커뮤니티 도메인

기존 그누보드5 게시판은 게시판마다 `write_{bo_table}` 테이블을 만드는 구조였지만, G5 Codex는 단일 커뮤니티 테이블 세트를 사용한다.

사용자 기능:

- 게시판 목록과 게시글 목록
- 게시글 작성, 수정, 삭제
- 댓글 작성과 삭제
- 첨부파일 업로드와 다운로드
- 최신글 조회
- 스크랩 등록과 목록 조회
- 게시글/댓글 메일 알림
- 커뮤니티 전용 포인트 조회

관리자 기능:

- 커뮤니티 기본환경 설정
- 게시판 그룹 관리
- 게시판과 카테고리 관리
- 게시글/댓글 목록과 상태 변경
- 커뮤니티 포인트 원장 조회
- 포인트 수동 지급/차감
- 포인트 만료 정산

핵심 구현 차이:

- 게시글은 `community_post`에 저장한다.
- 댓글은 `community_comment`에 저장한다.
- 최신글은 `community_latest_index`에 별도 인덱싱한다.
- 첨부파일은 `community_attachment` 메타데이터와 `data/file/community/` 저장 경로를 사용한다.
- 스크랩은 `community_scrap`에 저장한다.
- 포인트는 지갑, 원장, 사용 가능 묶음을 `community_point_wallet`, `community_point_ledger`, `community_point_available`로 분리한다.
- 게시판 메타와 최신글 조회에 캐시 helper를 사용한다.

자세한 구조와 점검 쿼리는 `docs/community-restoration-plan.md`, `docs/community-performance-checklist.md`를 따른다.

## 사이트 관리

그누보드5의 기존 내용 관리, 메뉴 관리와 별도로 현재 프로젝트용 사이트 관리 도메인을 추가했다.

- `site_menu`: 페이지, 게시판 그룹, 게시판, 직접 URL을 연결할 수 있는 메뉴
- `site_banner`: 위치별 배너 이미지, 링크, 노출 기간 관리
- `site_page`: 독립 페이지 관리
- 공개 페이지 주소: `/page.php?slug={페이지ID}`
- 관리자 파일: `adm/site_menu_*`, `adm/site_banner_*`, `adm/site_page_*`

자세한 내용은 `docs/site-management.md`를 따른다.

## 설치와 DB 스키마

- 기본 설치 SQL은 `install/gnuboard5.sql`을 유지하되 현재 프로젝트 범위에 맞게 축소했다.
- `install/community_schema.sql`을 추가해 커뮤니티와 사이트 메뉴/배너 테이블을 설치한다.
- `install/site_schema.sql`을 추가해 독립 페이지 테이블을 설치한다.
- 설치 완료 시 `data/dbconfig.php`에 커뮤니티, 사이트 메뉴, 사이트 배너, 사이트 페이지 테이블명을 기록한다.
- 설치 완료 시 `G5_TOKEN_ENCRYPTION_KEY`를 기록한다.
- 쇼핑몰 설치 SQL은 제거했다.

## 프론트엔드와 자산

- 기본 그누보드5 CSS, 테마 CSS, 이미지 자산을 대량 제거했다.
- Tailwind 4 기반 빌드를 추가했다.
  - `npm run build:theme` -> `css/common.css`
  - `npm run build:theme:site` -> `css/theme.css`
  - `npm run build:admin` -> `adm/css/admin.css`
  - `npm run build` -> 위 세 빌드 전체 실행
- 관리자 JavaScript를 화면별 파일로 분리했다.
  - `adm/admin-shell.js`
  - `adm/admin-member-list.js`
  - `adm/admin-member-form.js`
  - `adm/admin-member-export.js`
  - `adm/admin-config-form.js`

## 호환성 변화

G5 Codex는 그누보드5 플러그인/테마/스킨과 완전 호환되지 않는다.

특히 다음 전제를 가진 코드는 그대로 동작하지 않을 가능성이 높다.

- `bbs/` 경로를 직접 호출하는 코드
- `write_{bo_table}` 동적 게시판 테이블에 직접 접근하는 코드
- 기존 `skin/board`, `theme/basic/skin/*` 구조를 전제로 한 스킨
- `extend/` 자동 로딩에 의존하는 확장 코드
- 영카트 쇼핑몰, SMS, FAQ, 설문, QA, 방문 통계 기능에 의존하는 코드
- 기존 관리자 메뉴 번호와 화면 파일명을 직접 참조하는 코드

마이그레이션이 필요하면 기능별로 다음 대응이 필요하다.

- 회원 기능: `bbs/*` 회원 경로를 `member/*`로 매핑
- 게시판 기능: `write_{bo_table}` 데이터를 `community_post`, `community_comment`, `community_attachment` 구조로 이전
- 최신글: 기존 최신글 캐시 대신 `community_latest_index` 재생성
- 포인트: 그누보드5 포인트와 커뮤니티 포인트 원장 정책을 분리해 설계
- 화면: 기존 테마/스킨을 현재 `member/views/basic`, `community/views/basic`, 관리자 부분 템플릿 기준으로 재작성

## 남은 과제

- 커뮤니티와 관리자 request 입력도 회원 도메인처럼 raw input context 기반으로 단계 이전
- 커뮤니티 포인트 처리에서 InnoDB row lock 기준 보강
- 최신글/게시판 메타 캐시 무효화 자동 테스트 추가
- HTTP 회귀 테스트 또는 PHPUnit 기반 테스트 도입
- 공식 그누보드5 최신 변경분을 계속 추적할 경우 비교 기준 브랜치와 문서 갱신 절차 추가

## 관련 문서

- `README.md`
- `docs/community-restoration-plan.md`
- `docs/community-performance-checklist.md`
- `docs/security-input-sql-policy.md`
- `docs/site-management.md`
