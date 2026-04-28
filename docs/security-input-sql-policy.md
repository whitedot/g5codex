# 입력, SQL, 인증 토큰 정책

이 문서는 레거시 전역 escape와 PDO prepared statement가 공존하는 과도기에서 신규 코드를 안전하게 작성하기 위한 기준이다.

## 입력 처리

- 신규 도메인 코드는 raw input을 읽고 request 계층에서 정규화한다.
- `common.php`는 기존 호환을 위해 `$_GET`, `$_POST`, `$_COOKIE`, `$_REQUEST`에 전역 escape를 유지한다.
- 전역 escape 전 원본 값은 `g5_get_runtime_raw_get_input()`, `g5_get_runtime_raw_post_input()`, `g5_get_runtime_raw_request_input()`으로 접근한다.
- 회원 도메인의 `member_get_runtime_request_context()`는 raw GET/POST/request 값을 우선 사용한다.
- request 파일은 raw 값을 업무 타입으로 변환하는 경계다. DB 조회, 저장, 출력 escape를 request 파일에서 처리하지 않는다.

## SQL 작성

- 사용자 입력 값은 SQL 문자열에 직접 결합하지 않는다.
- 신규 코드에서는 `sql_query_prepared()`, `sql_fetch_prepared()`, `sql_fetch_all_prepared()`, `sql_fetch_value_prepared()`를 기본값으로 사용한다.
- 테이블명, 컬럼명처럼 바인딩할 수 없는 식별자는 `sql_quote_identifier()` 또는 도메인별 allow-list helper를 통과한 값만 사용한다.
- `sql_query()`와 `sql_fetch()`는 상수 SQL, 검증된 식별자 SQL, 레거시 호환 경로에서만 허용한다.

## 인증 토큰 및 검증 키

- CSRF 토큰, 인증 메일 토큰, 비밀번호 재설정 nonce, 자동로그인 키는 `g5_generate_hex_token()`, `g5_secure_random_int()`, `g5_build_hmac_token()`을 사용한다.
- 토큰 비교는 `g5_hash_equals()`를 사용한다.
- `md5()` 기반 키는 레거시 호환 또는 외부 인증사 프로토콜 호환 목적으로만 남긴다.
- 새 검증 키가 비밀값을 필요로 하면 단순 해시가 아니라 HMAC을 사용한다.

## 마이그레이션 원칙

1. 기존 전역 escape 제거는 한 번에 하지 않는다.
2. 신규/수정 도메인부터 raw input request 계층으로 옮긴다.
3. prepared helper 사용을 우선 적용한다.
4. 레거시 `md5()`/`sql_query()` 사용은 주석 또는 문서로 목적을 남긴다.
5. 충분한 회귀 테스트가 생긴 뒤 전역 escape 제거 범위를 넓힌다.
