# Legacy Maintainer Guide

기준일: 2026-04-27

## 목적

이 문서는 기존 G5 절차형 PHP 개발자가 현재 저장소를 빠르게 이해하고 유지보수할 수 있도록 만든 입문 안내서다.

현재 구조는 Composer autoload나 프레임워크 전환 없이, 기존 include 기반 실행 흐름을 유지한다. 대신 controller를 얇게 만들고 실제 입력 정리, 검증, 저장, 화면 데이터 조립 책임을 `lib/domain/*`와 `lib/support/*`로 나누는 방식으로 정리되어 있다.

## 먼저 읽을 문서

1. `README.md`
2. `docs/gnuboard5-readable-refactor-plan.md`
3. `docs/member-only-scope.md`
4. `docs/architecture/member-controller-pattern.md`
5. `docs/architecture/admin-controller-pattern.md`
6. `docs/architecture/admin-include-map.md`
7. `docs/architecture/admin-export-pattern.md`
8. `docs/architecture/manual-test-scenarios.md`

## 큰 구조

- `common.php`: 사이트 공통 부트스트랩 진입점이다. 새 업무 로직을 넣는 곳이 아니다.
- `adm/_common.php`: 관리자 공통 부트스트랩 진입점이다. 관리자 controller는 이 파일을 먼저 포함한다.
- `member/`: 회원/인증 화면과 action controller가 남아 있는 영역이다.
- `adm/member_*`, `adm/config_*`, `adm/index.php`: 현재 유지 중인 관리자 화면 controller다.
- `lib/domain/member/`: 회원 도메인의 입력 정리, 검증, 저장, 흐름, 화면 데이터 조립 구현 원천이다.
- `lib/domain/admin/`: 관리자 도메인의 화면, 저장, export, 관리자 shell 구현 원천이다.
- `lib/support/`: 여러 화면에서 공유하는 공용 유틸리티 구현 원천이다.
- `lib/bootstrap/`: 런타임 request context, 인증, 세션 같은 실행 준비 구현 원천이다.
- `lib/common.*.lib.php`, `lib/member.*.lib.php`: 기존 include 경로를 살리기 위한 호환 로더다. 새 로직을 추가하기 전에 실제 include 대상 파일을 먼저 확인한다.

## 작업별 파일 지도

| 작업 | 시작 controller | 주 구현 파일 |
| --- | --- | --- |
| 로그인 | `member/login.php`, `member/login_check.php` | `lib/domain/member/request-auth.lib.php`, `validation-auth.lib.php`, `flow-auth.lib.php`, `render-page-view.lib.php`, `render-response.lib.php` |
| 로그아웃 | `member/logout.php` | `lib/domain/member/request-auth.lib.php`, `flow-auth.lib.php` |
| 회원가입/정보수정 화면 | `member/register_form.php` | `lib/domain/member/request-register.lib.php`, `render-register-form.lib.php`, `page.lib.php` |
| 회원가입/정보수정 저장 | `member/register_form_update.php`, `member/member_leave.php` | `lib/domain/member/validation-register.lib.php`, `persist-register.lib.php`, `persist-register-email.lib.php`, `flow-register.lib.php` |
| 비밀번호 찾기/재설정 | `member/password_*` | `lib/domain/member/request-auth.lib.php`, `validation-auth.lib.php`, `persist-auth.lib.php`, `flow-auth.lib.php` |
| 이메일 인증/수신중지 | `member/email_*`, `member/register_email*` | `lib/domain/member/request-auth.lib.php`, `validation-auth.lib.php`, `validation-register-email.lib.php`, `persist-register-email.lib.php`, `flow-auth.lib.php`, `flow-register-email.lib.php` |
| 회원 AJAX 중복 검사 | `member/ajax.mb_*.php` | `lib/domain/member/request-ajax.lib.php`, `validation-ajax.lib.php`, `flow-ajax.lib.php` |
| 관리자 대시보드 | `adm/index.php` | `lib/domain/admin/dashboard.lib.php`, `lib/domain/admin/ui.lib.php` |
| 관리자 회원 목록 | `adm/member_list.php` | `lib/domain/admin/member-list-request.lib.php`, `member-list-query.lib.php`, `member-list-view.lib.php`, `adm/member_list_parts/*` |
| 관리자 회원 일괄 처리 | `adm/member_list_update.php` | `lib/domain/admin/member-list-request.lib.php`, `member-list-validation.lib.php`, `member-list-persist.lib.php`, `member-list-update.lib.php` |
| 관리자 회원 등록/수정 | `adm/member_form.php`, `adm/member_form_update.php` | `lib/domain/admin/member-form-request.lib.php`, `member-form-view.lib.php`, `member-form-validation.lib.php`, `member-form-persist.lib.php`, `member-form-update.lib.php`, `adm/member_form_parts/*` |
| 관리자 설정 | `adm/config_form.php`, `adm/config_form_update.php` | `lib/domain/admin/config.lib.php`, `adm/config_form_parts/*` |
| 관리자 회원 export 화면 | `adm/member_list_exel.php` | `lib/domain/admin/export-request.lib.php`, `export-filter.lib.php`, `export-runtime.lib.php`, `export-view.lib.php`, `export-config.lib.php` |
| 관리자 회원 export 다운로드 | `adm/member_list_exel_export.php` | `lib/domain/admin/export-runtime.lib.php`, `export-stream.lib.php`, `export-file.lib.php`, `xlsx.lib.php` |
| 관리자 shell/menu | `adm/admin.head.php`, `adm/admin.tail.php`, `adm/head.sub.admin.php` | `lib/domain/admin/ui.lib.php`, `lib/domain/admin/bootstrap.lib.php`, `adm/admin-core.js`, `adm/admin-config-form.js`, `adm/admin-member-export.js`, `adm/admin-member-form.js`, `adm/admin-member-list.js`, `adm/admin-shell.js`, `adm/admin.js` |

## 어디에 코드를 추가할까

- 새 입력 파싱은 controller가 아니라 `request*.lib.php`에 둔다.
- 필수값, 권한, 상태 검사는 `validation*.lib.php`에 둔다.
- DB 조회, 저장, 삭제는 `persist*.lib.php`나 admin 전용 query/export 파일에 둔다.
- redirect, alert, 세션 정리, 후처리는 `flow*.lib.php`에 둔다.
- 화면에 넘길 배열 조립은 `render*.lib.php`, `page*.lib.php`, admin view 파일에 둔다.
- 화면형 admin controller는 page view를 만든 뒤 `admin_apply_page_view()`로 shell 제목과 container 상태를 적용한다.
- 여러 도메인이 공유하는 순수 유틸리티는 `lib/support/`에 둔다.
- 기존 include 경로가 꼭 필요할 때만 `lib/common.*.lib.php` 또는 `lib/member.*.lib.php` 호환 로더를 손댄다.

## 화면 데이터 이름 규칙

PHP 화면 파일은 escape, 숫자 포맷, JS 문자열 직렬화 방법을 직접 알지 않는 방향으로 유지한다. 템플릿에서 반복되는 출력 데이터는 도메인 view model에서 아래 이름으로 미리 준비한다.

- HTML 속성 값은 `*_attr`를 사용한다. 예: `value_attr`, `href_attr`, `class_attr`, `name_attr`.
- 화면에 그대로 출력할 텍스트는 `*_text`를 사용한다. 예: `label_text`, `count_text`, `error_text`.
- HTML 조각을 의도적으로 넘길 때만 `*_html`을 사용하고, 값을 만든 함수에서 안전성을 책임진다.
- `link`, `meta`, `script`처럼 태그 자체가 반복되는 shell 자산은 `tag_html`로 완성된 조각을 넘길 수 있다. 이때 조립 함수가 모든 속성 escape를 끝내야 하며, 템플릿은 반복 출력만 담당한다.
- JavaScript 문자열 리터럴로 넣을 값은 `*_json`을 사용한다. 회원 화면은 `member_json_string()`, 관리자 화면은 `admin_json_string()`으로 만든 값을 넘긴다.
- 조건 분기는 원문 문자열 비교 대신 `has_*`, `show_*`, `is_*` 같은 boolean view field를 우선 사용한다.
- 반복 option/radio/hidden field는 `value_attr`, `selected_attr`, `checked_attr`, `label_text`, `id_attr`처럼 출력용 필드로 맞춘다.
- 관리자 화면에서 공통 escape가 필요하면 `admin_escape_attr()`, 숫자 표시가 필요하면 `admin_format_count_text()`를 우선 사용한다.
- 회원 화면 렌더링에서 HTML 속성 escape가 필요하면 `member_escape_attr()`를 우선 사용한다.

## 스타일을 어디에 추가할까

- PHP 화면 파일에는 Tailwind utility class를 길게 직접 추가하지 않는다.
- 사이트와 관리자에서 함께 쓰는 폼, 버튼, 테이블, 안내문 규칙은 `tailwind4/common.css`의 `ui-*` 시맨틱 클래스로 둔다.
- 관리자 화면에만 쓰는 레이아웃, action bar, export 상태, dashboard 표현은 `tailwind4/admin.css`의 `admin-*` 시맨틱 클래스로 둔다.
- 예를 들어 table head는 `ui-table-head`, 안내 링크는 `ui-link-primary`, export 필터 배치는 `admin-export-*`, 관리자 폼 하단 버튼 영역은 `admin-form-actions-*`를 우선 사용한다.
- 폼 행의 예외 정렬은 특정 `id` 선택자 대신 `ui-form-row-start` 또는 `af-row-start` 같은 역할 클래스로 표시한다.
- `id`는 앵커, JS 연결, 기존 G5 호환을 위해 남길 수 있지만, 새 스타일 기준은 `admin-*` 또는 `ui-*` 클래스에 둔다.
- 관리자 shell 스타일은 `admin-sidebar-*`, `admin-topbar-*`, `admin-content-*`처럼 역할이 드러나는 클래스를 우선 사용한다.
- 스타일 원천을 수정한 뒤에는 변경 범위에 따라 `npm run build:admin` 또는 `npm run build`를 실행한다.

## 피해야 할 변경

- `common.php`, `adm/admin.lib.php`, `lib/common.util.lib.php`에 새 업무 로직을 추가하지 않는다.
- controller에 SQL, 큰 분기, 화면 배열 조립을 다시 쌓지 않는다.
- controller에서 `extract()`로 숨은 변수를 만들지 않는다.
- 관리자 entry와 domain helper에서 `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`를 직접 읽지 않는다. `g5_get_runtime_*_input()` 또는 도메인 request context를 사용한다.
- aggregate loader에 업무 로직을 넣지 않는다. include 흐름은 `docs/architecture/admin-include-map.md` 기준으로 유지한다.
- `lib/PHPExcel` 또는 `lib/PHPExcel.php`를 복구하지 않는다.
- Tailwind 생성 CSS를 직접 고치지 않는다. 스타일 변경은 `tailwind4/` 원천 파일을 고친 뒤 빌드한다.
- PHP 화면 파일에 `flex`, `border-*`, `text-*`, `px-*`, `py-*`, `rounded-*` 조합을 반복해서 쌓지 않는다. 반복되는 모양은 `ui-*` 또는 `admin-*` 클래스로 이름 붙인다.
- `tailwind4/admin.css`에 새 ID 선택자 스타일을 추가하지 않는다. 꼭 필요한 경우 먼저 의미 클래스를 붙일 수 있는지 확인한다.

## 작업 전후 확인

작업 전에는 먼저 `git status --short`로 다른 변경이 있는지 확인한다.

작업 후에는 변경 범위에 맞춰 아래 명령을 실행한다.

```sh
npm ci
npm run build
PATH=".tools/php:$PATH" npm run check:refactor
git diff --check
```

문서만 고친 경우에도 `git diff --check`는 실행한다. PHP include 경로나 controller 예시를 건드렸다면 `check:refactor`까지 실행한다.
`check:refactor`는 aggregate loader에 업무 로직이 들어오는 경우와 반복 Tailwind utility 조합이 PHP 화면 파일에 다시 추가되는 경우도 함께 잡는다.
자동 검사 후에도 Excel export, 회원 인증/가입, 관리자 회원 저장 같은 운영 흐름은 `docs/architecture/manual-test-scenarios.md` 기준으로 수동 확인한다.
