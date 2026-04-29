# G5 Codex

> 이 프로젝트는 더 이상 진행하지 않는다. G5 Codex를 진행하며 얻은 그누보드5 런타임 분해, 도메인 계층화, 관리자 기능 구현, Codex 기반 개발 흐름의 경험은 후속 [토이코어(ToyCore)](https://github.com/whitedot/toycore) 프로젝트로 이어간다.

G5 Codex는 Codex 에이전트를 이용하여 최소한의 프롬프팅을 통해 그누보드5 계열 런타임을 회원 중심 애플리케이션으로 정리하고, 커뮤니티 기능을 현재 프로젝트 구조에 맞춰 복구한 PHP 애플리케이션이다.

기존 G5의 전역 함수와 include 기반 실행 모델을 유지하면서도, 회원/관리자/커뮤니티 기능은 `lib/domain/*` 아래의 request, validation, persist, render, flow 계층으로 나누는 방향으로 현대화되어 있다.

## 프로젝트 목표

이 프로젝트의 목표는 검증된 G5 운영 모델을 버리지 않으면서, 오래 운영 가능한 회원/커뮤니티 런타임으로 재구성하는 것이다.
또한 Codex를 활용해 그누보드5 기반 기능을 얼마나 빠르게 구현하고 검증할 수 있는지 실험하는 프로젝트다.

구체적으로는 다음을 지향한다.

- Codex 기반 개발 흐름으로 그누보드5 기능 복원과 현대화 작업의 속도, 안정성, 한계를 확인한다.
- 기존 G5 기반 서비스에서 익숙한 회원, 관리자, 인증, 게시판 운영 흐름을 유지한다.
- 게시판별 동적 테이블, 전역 입력 escape 의존, 문자열 SQL 조립 같은 레거시 위험을 단계적으로 줄인다.
- 회원/관리자/커뮤니티 기능을 도메인별 request, validation, persist, render, flow 계층으로 나눠 변경 범위를 좁힌다.
- 런타임 설정, DB 설정, 보안 토큰, 업로드/세션/캐시 경로를 배포 환경 기준으로 분리한다.
- 커뮤니티 기능은 단순 복원이 아니라 단일 테이블 세트, 최신글 인덱스, 포인트 원장을 갖춘 운영형 구조로 제공한다.
- 대규모 프레임워크로 갈아타기보다 기존 PHP 호스팅/운영 환경에서 현실적으로 배포 가능한 형태를 유지한다.

비목표도 분명하다.

- Laravel, Symfony 같은 풀스택 프레임워크로 전면 재작성하지 않는다.
- 기존 그누보드5의 모든 플러그인/테마 호환성을 보장하지 않는다.
- 전역 함수와 include 기반 구조를 한 번에 제거하지 않는다.
- 테스트와 마이그레이션 없이 전역 request escape를 즉시 제거하지 않는다.

## 주요 기능

- 회원 가입, 로그인, 로그아웃
- 이메일 인증, 비밀번호 찾기와 재설정
- 본인인증 연동 준비
- 관리자 로그인, 회원 조회/수정/삭제
- 관리자 회원 XLSX export와 임시 파일 정리
- 커뮤니티 게시판, 게시글, 댓글, 첨부파일, 최신글, 스크랩
- 게시글/댓글 메일 알림
- 커뮤니티 전용 포인트 지갑, 원장, 사용 가능 포인트, 만료 정산
- Tailwind 4 기반 공통/사이트/관리자 CSS 빌드

## 실행 요구사항

- PHP 8.3 또는 호환 PHP 런타임
- MySQL 또는 MariaDB
- 웹 서버 document root를 이 저장소 루트로 지정
- 배포 서버에서 쓰기 가능한 `data/` 디렉터리
- Tailwind CSS를 다시 빌드할 때만 Node.js와 npm 필요

## 프로젝트 구조

```text
adm/          관리자 화면, 관리자 컨트롤러, 관리자 부분 템플릿
community/    커뮤니티 사용자 컨트롤러와 view/mail 스킨
member/       회원 인증, 가입, 계정 화면과 회원 스킨
lib/          공통 런타임, bootstrap, support, domain helper
plugin/       PHPMailer, HTMLPurifier, captcha, 본인인증 플러그인
install/      설치 화면과 기본/커뮤니티 스키마 SQL
data/         DB 설정, 세션, 캐시, 업로드, 임시 파일 경로
css/          빌드된 사용자 CSS
adm/css/      빌드된 관리자 CSS
js/           공통 JavaScript
tailwind4/    Tailwind 4 입력 CSS
docs/         구조, 보안 정책, 성능 점검 문서
scripts/      개발/보안 정책 점검 스크립트
tests/        테스트 디렉터리
```

## 런타임 흐름

모든 일반 요청은 `common.php`를 통해 공통 bootstrap을 거친다.

1. 경로와 설정 상수 로드
2. raw request snapshot 저장
3. 레거시 호환용 전역 request escape 적용
4. DB 연결과 세션 초기화
5. 회원 상태 복원, 자동 로그인 확인, IP 접근 정책 확인
6. 회원/관리자/커뮤니티 도메인 컨트롤러 실행

도메인 코드는 가능한 한 얇은 컨트롤러와 `lib/domain/*` 함수로 나뉜다. 신규 코드는 request 계층에서 입력을 정규화하고, DB 값은 prepared helper로 바인딩하는 방식을 따른다.

## 설정 파일

배포 환경별 설정은 `config.runtime.php`에서 관리한다.

주요 항목:

- `domain`: canonical site URL. 비워두면 요청 기준으로 자동 계산
- `https_domain`: HTTPS 회원 플로우에 사용할 URL
- `cookie_domain`: 공유 쿠키 도메인
- `debug`: PHP debug 표시 여부
- `collect_query`: SQL debug 수집 여부
- `display_sql_error`: SQL 에러 화면 표시 여부
- `dbconfig_file`: `data/` 아래 DB 설정 파일명
- `db_engine`: 설치 시 사용할 DB engine 기본값
- `db_charset`: DB charset 기본값
- `smtp_host`, `smtp_port`: 메일 발송 서버

운영 환경에서는 `debug`, `collect_query`, `display_sql_error`를 꺼둔다.

## 필수 로컬 파일과 쓰기 경로

DB 접속 정보는 배포 환경에서 `data/dbconfig.php`로 준비한다. 파일명은 `config.runtime.php`의 `dbconfig_file` 값으로 바꿀 수 있다.

런타임에서 사용하는 대표 쓰기 경로:

```text
data/session/
data/cache/
data/file/
data/tmp/
data/log/
data/member/
```

`data/` 아래에서는 PHP, HTML, CGI 계열 파일 실행이 차단되어야 한다. 설치 스크립트는 `data/.htaccess`를 생성해 이를 보조한다.

## 설치와 스키마

설치 관련 파일은 `install/` 아래에 있다.

```text
install/gnuboard5.sql
install/community_schema.sql
install/install_db.php
```

설치 과정은 기본 회원/관리자 테이블과 커뮤니티 테이블을 만들고, `data/dbconfig.php`에 테이블명과 `G5_TOKEN_ENCRYPTION_KEY`를 기록한다.

운영 설치 후에는 반드시 `install/` 디렉터리를 삭제하거나 웹 접근을 차단한다. 설치 디렉터리는 DB 설정 생성, schema 적용, 설치 토큰 검증 같은 민감한 기능을 포함한다.

## 프론트엔드 빌드

Tailwind 4 입력 파일은 `tailwind4/` 아래에 있다.

```text
npm ci
npm run build
```

빌드 결과:

```text
css/common.css
css/theme.css
adm/css/admin.css
```

## 커뮤니티 도메인

커뮤니티는 게시판별 동적 테이블을 만들지 않고, 단일 테이블 세트로 동작한다.

핵심 테이블:

- `community_config`
- `community_board_group`
- `community_board`
- `community_board_category`
- `community_post`
- `community_comment`
- `community_latest_index`
- `community_point_wallet`
- `community_point_ledger`
- `community_point_available`
- `community_attachment`
- `community_scrap`
- `site_menu`
- `site_banner`

현재 구조 문서는 `docs/community-restoration-plan.md`, 주요 조회 성능 점검은 `docs/community-performance-checklist.md`를 참고한다.

## 보안 개발 정책

입력, SQL, 토큰 생성 규칙은 `docs/security-input-sql-policy.md`를 따른다.

핵심 원칙:

- 신규 도메인 코드는 raw input을 request 계층에서 정규화한다.
- SQL 값은 `sql_query_prepared()`, `sql_fetch_prepared()` 계열로 바인딩한다.
- `sql_query()`와 `sql_fetch()` 직접 호출은 상수 SQL, 검증된 식별자 SQL, 레거시 호환 경로에만 남긴다.
- CSRF 토큰, 인증 메일 토큰, 비밀번호 재설정 nonce, 자동로그인 키는 보안 난수/HMAC helper를 사용한다.
- 토큰 비교는 `g5_hash_equals()` 또는 `hash_equals()` 기반 helper를 사용한다.
- `md5()` 기반 검증 키는 레거시 호환 또는 외부 프로토콜 호환 목적일 때만 남긴다.

정책 점검:

```text
scripts/check-security-policy.sh
```

## 배포 후 점검

실제 DB와 메일 설정이 연결된 환경에서 다음 흐름을 확인한다.

1. 로그인과 로그아웃
2. 회원 가입
3. 이메일 인증
4. 비밀번호 찾기와 재설정
5. 회원 정보 수정
6. 관리자 로그인
7. 관리자 회원 목록과 회원 수정
8. 회원 XLSX export와 export 임시 파일 정리
9. 커뮤니티 게시판 생성과 카테고리 저장
10. 게시글 작성, 수정, 삭제
11. 댓글 작성과 삭제
12. 첨부파일 업로드와 다운로드
13. 최신글과 스크랩
14. 커뮤니티 포인트 지급, 차감, 만료 정산
15. `adm/community_health.php` 테이블 상태 확인

## 참고 문서

```text
docs/community-restoration-plan.md
docs/community-performance-checklist.md
docs/security-input-sql-policy.md
AGENTS.md
```

## 라이선스

MIT License. 자세한 내용은 `LICENSE.txt`를 참고한다.
