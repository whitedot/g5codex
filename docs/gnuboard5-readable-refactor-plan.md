# Gnuboard5 Readable Refactor Plan

기준일: 2026-04-26

## 목표

이 계획의 목표는 그누보드5 기반 코드를 과도하게 현대화하지 않으면서, 레거시 PHP 개발자와 AI 에이전트가 함께 읽고 고칠 수 있는 상태로 정리하는 것이다.

현대적인 프레임워크 전환, Composer autoload 전면 도입, ORM 도입, SPA 전환 같은 구조 변경은 이번 목표가 아니다. 현재 저장소의 include 기반 실행 흐름과 절차형 PHP 스타일을 유지하되, 파일 책임과 함수 이름, 입력/검증/저장/화면 조립 경계를 더 명확하게 만든다.

## 판단 기준

좋은 리팩토링은 아래 조건을 만족해야 한다.

- 기존 G5 개발자가 `_common.php`, controller, `lib/*.lib.php` 흐름을 따라갈 수 있다.
- AI가 파일명과 함수명만 보고 request, validation, persist, flow, render 책임을 추론할 수 있다.
- controller는 짧고 예측 가능하며, SQL과 화면 배열 조립을 길게 품지 않는다.
- 호환 로더는 include 경로를 보존하기 위한 역할만 하고, 새 업무 규칙을 담지 않는다.
- Tailwind는 화면 마크업에 직접 노출하는 유틸리티 체계가 아니라, `ui-*`와 `admin-*` 시맨틱 클래스를 만드는 내부 도구로 사용한다.
- 공용 스타일은 `tailwind4/` 원천에서 관리하고 생성 CSS는 빌드 결과로만 갱신한다.
- 변경 단위가 작아 수동 검토, 테스트, 되돌리기 판단이 쉽다.

## 하지 않을 일

- Laravel, Symfony, CodeIgniter 등 프레임워크로 이전하지 않는다.
- Composer autoload를 전면 도입하지 않는다.
- 기존 G5 전역 변수와 include 흐름을 한 번에 제거하지 않는다.
- DB 접근 계층을 ORM이나 query builder로 전면 교체하지 않는다.
- 관리자 화면을 SPA로 바꾸지 않는다.
- PHP 8 대응을 이유로 동작 의미를 바꾸는 대형 리라이트를 하지 않는다.
- 삭제한 BBS, SHOP, 소셜, SMS, 포인트 등 제외 범위를 호환 목적으로 되살리지 않는다.

## 리팩토링 원칙

### 1. 절차형 구조를 존중한다

기본 실행 흐름은 `controller -> _common.php -> domain/support helper -> view/include` 형태로 유지한다. 객체지향 구조는 이미 존재하거나 명확히 이득이 있는 곳에만 제한적으로 사용한다.

### 2. controller를 얇게 유지한다

controller는 실행 순서를 보여주는 파일이다. 입력 정리, 검증, 저장, 화면 데이터 조립은 `lib/domain/*` 또는 `lib/support/*`로 옮긴다.

권장 흐름:

```php
require_once './_common.php';

$request = admin_read_example_request(g5_get_runtime_get_input());
$page_view = admin_build_example_page_view($request, $member, $config);

$g5['title'] = $page_view['title'];
require_once './admin.head.php';
// render
require_once './admin.tail.php';
```

### 3. 파일 책임을 이름으로 드러낸다

파일명은 기능보다 책임을 먼저 드러낸다.

- `request*.lib.php`: 입력 정규화, 기본값, 타입 변환
- `validation*.lib.php`: 필수값, 권한, 상태 검증
- `persist*.lib.php`: DB 조회, 저장, 삭제
- `flow*.lib.php`: alert, redirect, session, mail, hook 조합
- `render*.lib.php`, `page*.lib.php`: 화면 데이터와 템플릿 렌더 준비
- `ui*.lib.php`: 관리자 shell, 반복 UI 조각, view helper

화면 데이터 이름은 출력 위치를 드러내야 한다. HTML 속성은 `*_attr`, 화면 텍스트는 `*_text`, JavaScript 리터럴은 `*_json`, 의도적으로 준비한 HTML 조각은 `*_html`을 사용한다. `head.sub.admin.php`와 `admin.tail.php`처럼 공통 shell에서 반복되는 `link`, `meta`, `script` 태그는 view model이 `tag_html`로 완성해 넘길 수 있다. 태그 생성 함수는 속성 escape를 끝내고, 템플릿은 HTML 문자열을 새로 조립하지 않는다.

### 4. AI가 추론 가능한 함수명을 쓴다

함수명은 동사와 책임을 맞춘다.

- 입력 읽기: `*_read_*_request()`
- 검증: `*_validate_*()`
- 조회: `*_find_*()`, `*_list_*()`
- 저장: `*_store_*()`, `*_update_*()`, `*_delete_*()`
- 흐름 완료: `*_complete_*()`
- 화면 데이터: `*_build_*_page_view()`

### 5. 호환 레이어는 얇게 둔다

`lib/common.*.lib.php`, `lib/member.*.lib.php`, admin aggregate loader는 기존 include 경로를 살리기 위한 장치다. 새 업무 로직은 실제 도메인 파일에 둔다.

### 6. 현대화보다 안전한 반복을 우선한다

한 번의 변경은 한 화면, 한 흐름, 한 책임 경계 단위로 제한한다. 동작 변경과 코드 이동을 한 커밋에 섞지 않는 것을 기본으로 한다.

### 7. Tailwind는 시맨틱 레이어 뒤에 숨긴다

PHP 화면 파일에는 Tailwind utility class를 길게 직접 쌓지 않는다. Tailwind의 `@apply`는 `tailwind4/common.css`와 `tailwind4/admin.css`에서 시맨틱 클래스를 정의하는 용도로 사용한다.

권장:

```html
<input class="ui-form-input">
<button class="ui-btn ui-btn-primary">저장</button>
<div class="admin-form-actions">...</div>
```

지양:

```html
<input class="w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:ring-2">
```

시맨틱 클래스 이름은 화면 모양보다 역할을 먼저 표현한다.

- `ui-form-*`: 사이트와 관리자에서 공유하는 폼 구조
- `ui-link-*`: 안내문, 설명문 안에서 반복되는 공용 링크 표현
- `ui-btn-*`: 공용 버튼 역할과 상태
- `ui-table-*`: 목록, 헤더, 셀, 빈 상태
- `ui-alert-*`: 오류, 안내, 성공 메시지
- `admin-shell-*`: 관리자 레이아웃과 내비게이션
- `admin-sidebar-*`, `admin-topbar-*`, `admin-content-*`: 관리자 shell의 세부 영역
- `admin-form-*`: 관리자 설정/회원 폼 전용 배치
- `admin-export-*`: 관리자 export 화면 전용 상태와 진행 표시

기존 G5와 JS 호환을 위해 `id`를 유지하더라도 스타일 책임은 의미 클래스에 둔다. `id`는 위치를 찾는 이름이고, `ui-*`/`admin-*`는 화면 역할을 표현하는 이름으로 구분한다.

## 실행 단계

### 1단계: 기준선 정리

- `README.md`, 유지보수 문서, 아키텍처 문서의 오래된 계획 참조를 현재 계획으로 교체한다.
- 현재 유지 범위가 `docs/member-only-scope.md`와 일치하는지 확인한다.
- 레거시 개발자가 먼저 읽을 문서 순서를 `docs/legacy-maintainer-guide.md`에 맞춘다.
- `npm run check:refactor`와 `git diff --check`를 기본 검증 명령으로 유지한다.

완료 기준:

- 문서에서 제거된 이전 기준 파일을 참조하지 않는다.
- 새 작업자는 README와 maintainer guide만 보고 현재 목표와 금지 범위를 이해할 수 있다.

### 2단계: controller 읽기 비용 축소

- `adm/*.php`, `member/*.php` controller를 화면형, 완료형, AJAX형, stream형으로 분류한다.
- controller 안의 긴 입력 처리, 권한 분기, SQL, 화면 배열 조립을 도메인 파일로 이동한다.
- 완료형 controller는 가능하면 `*_complete_*()` 호출 1회 중심으로 정리한다.
- 화면형 controller는 가능하면 `*_build_*_page_view()` 결과를 렌더에 넘긴다.

완료 기준:

- controller 하나를 열었을 때 실행 순서가 1분 안에 파악된다.
- controller 내부에 새 helper/function 정의가 없다.

### 3단계: 도메인 파일 책임 정렬

- `lib/domain/member/`와 `lib/domain/admin/` 파일을 request, validation, persist, flow, render/page 책임 기준으로 점검한다.
- 책임이 섞인 함수는 같은 동작을 유지하며 적절한 파일로 이동한다.
- aggregate loader에는 include 선언만 남긴다.
- 함수명과 파일명이 맞지 않는 경우, 호환 영향이 작을 때부터 이름을 정리한다.

완료 기준:

- 새 기능을 추가할 위치를 파일명만 보고 추정할 수 있다.
- AI가 비슷한 패턴의 기존 파일을 찾아 재사용하기 쉽다.

### 4단계: Tailwind 시맨틱 레이어 전환

- PHP 템플릿에서 반복되는 Tailwind utility class 묶음을 찾는다.
- 반복 UI를 `ui-*` 또는 `admin-*` 시맨틱 클래스로 이름 붙인다.
- 사이트와 관리자에서 함께 쓰는 규칙은 `tailwind4/common.css`에 둔다.
- 관리자 화면에만 필요한 shell, sticky action, export, dashboard 규칙은 `tailwind4/admin.css`에 둔다.
- `ui-form-*` 같은 공용 form class는 사이트와 관리자 화면이 함께 쓰는 규칙으로 관리한다.
- 새 PHP 마크업에는 유틸리티 클래스보다 시맨틱 클래스를 우선 사용한다.
- 생성 CSS 직접 수정은 예외로만 허용한다.
- 변경 후 `npm run build:admin` 또는 필요한 범위의 빌드 명령을 실행한다.

완료 기준:

- 관리자 설정/회원 화면의 form, button, table, shell 규칙이 시맨틱 클래스와 CSS 원천으로 설명된다.
- PHP 화면 파일에서 긴 Tailwind utility class 조합이 새로 늘어나지 않는다.
- 스타일 변경 후 필요한 빌드 명령이 문서와 실제 산출물에 반영된다.

#### 시맨틱 레이어 전환 순서

1. `ui-form-*`와 `admin-form-*`를 먼저 안정화한다.
2. 관리자 목록과 대시보드의 table 표현을 `ui-table-*` 또는 `admin-table-*`로 감싼다.
3. 안내문, 오류문, empty state를 `ui-alert-*`, `ui-empty-*`로 정리한다.
4. export 화면의 필터, 상태, 진행 표시를 `admin-export-*`로 분리한다.
5. 마지막에 남은 단발성 utility class는 실제 반복 여부를 보고 유지하거나 시맨틱 클래스로 옮긴다.

#### 시맨틱 레이어 판단 기준

- 같은 utility 조합이 2곳 이상 반복되면 시맨틱 클래스 후보로 본다.
- 레거시 개발자가 클래스 이름만 보고 용도를 알 수 있으면 시맨틱 클래스로 둔다.
- 한 화면에서만 쓰이는 임시 배치는 `admin-*` 화면 전용 클래스로 제한한다.
- `mt-*`, `px-*`, `text-*`, `border-*` 같은 utility가 길게 섞이면 먼저 이름 붙일 수 있는 UI 역할을 찾는다.
- 단순 숨김, 접근성 보조, 아주 작은 one-off 조정은 utility 또는 기존 G5 class를 유지할 수 있다.

### 5단계: 검증 자동화와 수동 점검 루틴 고정

- 기존 refactor check 스크립트를 유지하고, 새 금지 패턴이 반복되면 작은 check를 추가한다.
- PHP 구문 검사, `npm run check:refactor`, `npm run build`, `git diff --check`를 변경 범위에 맞게 실행한다.
- Excel export, 회원 저장, 로그인, 비밀번호 재설정처럼 운영 리스크가 큰 흐름은 수동 시나리오를 문서화한다.

완료 기준:

- 리팩토링 후 최소 검증 명령이 명확하다.
- 자동 검사로 잡기 어려운 운영 흐름은 수동 확인 목록이 있다.

## 작업순서 기획

작업은 UI 시맨틱 레이어를 먼저 안정화한 뒤 controller와 domain 책임 정렬로 들어간다. 이유는 관리자 화면의 반복 마크업을 먼저 줄여야 이후 controller와 view를 읽을 때 시각적 잡음이 줄어들기 때문이다.

### 0순위: 기준선 고정

목표:

- 현재 문서 기준을 단일화한다.
- 작업 전후 검증 명령을 고정한다.

작업:

1. `docs/gnuboard5-readable-refactor-plan.md`를 단일 계획 문서로 유지한다.
2. `README.md`, `docs/legacy-maintainer-guide.md`의 참조가 이 문서를 가리키는지 확인한다.
3. `git status --short`, `git diff --check`를 기본 점검으로 둔다.

완료 기준:

- 계획 문서는 이 파일 하나만 남는다.
- 새 작업자가 README에서 이 문서로 자연스럽게 이동할 수 있다.

### 1순위: Tailwind 시맨틱 레이어 목록화

목표:

- PHP 화면에 직접 노출된 utility class를 먼저 조사한다.
- 바로 치환할 반복 UI와 유지할 one-off 조정을 구분한다.

작업:

1. `adm/`, `member/`의 `class=""`에서 `flex`, `grid`, `px-*`, `py-*`, `mt-*`, `mb-*`, `rounded-*`, `text-*`, `bg-*`, `border-*` 사용 위치를 목록화한다.
2. 반복되는 조합을 form, table, alert, action bar, export 상태 표시로 분류한다.
3. `tailwind4/common.css`에 둘 공용 클래스와 `tailwind4/admin.css`에 둘 관리자 전용 클래스를 나눈다.

완료 기준:

- 우선 치환할 클래스 묶음이 `ui-form-*`, `ui-table-*`, `ui-alert-*`, `admin-form-*`, `admin-export-*` 중 하나로 분류된다.
- 단발성 utility는 유지 사유를 남긴다.

### 2순위: 폼 시맨틱 레이어 안정화

목표:

- 관리자 설정/회원 폼의 가장 반복적인 구조를 먼저 정리한다.
- 레거시 개발자가 폼 구조를 클래스 이름만 보고 이해할 수 있게 한다.

작업:

1. `tailwind4/common.css`의 `ui-form-*`, `.ui-form-theme` 규칙을 기준으로 삼는다.
2. `tailwind4/admin.css`의 `admin-form-*`, `af-grid`, sticky action 관련 규칙을 정리한다.
3. `adm/config_form.php`, `adm/member_form.php`, `adm/config_form_parts/*`, `adm/member_form_parts/*`에서 긴 utility 조합을 시맨틱 클래스로 치환한다.
4. 변경 후 `npm run build:admin`을 실행한다.

완료 기준:

- 폼 화면의 주요 레이아웃은 `ui-form-*`, `admin-form-*`, `af-grid` 같은 의미 클래스 중심으로 읽힌다.
- 폼 마크업에 새 긴 utility class 조합이 추가되지 않는다.

### 3순위: 테이블과 목록 시맨틱 레이어 정리

목표:

- 관리자 목록과 대시보드 테이블 표현을 반복 가능한 이름으로 감싼다.

작업:

1. `adm/member_list_parts/table.php`, `adm/index.php`의 table head, cell, empty state class를 비교한다.
2. 공통 table 규칙은 `ui-table-*`로 `tailwind4/common.css`에 둔다.
3. 관리자 회원 목록 전용 cell, action, status 표현은 `admin-member-list-*` 또는 기존 의미 클래스에 맞춘다.
4. 변경 후 `npm run build:admin`을 실행한다.

완료 기준:

- 테이블 헤더, 셀, empty state의 반복 utility 조합이 시맨틱 클래스로 이동한다.
- 회원 목록과 대시보드가 같은 table 언어를 공유한다.

### 4순위: 안내문, 오류문, export UI 정리

목표:

- export 화면처럼 운영 리스크가 있는 UI를 읽기 쉽게 만든다.
- 상태, 경고, 진행 표시를 시맨틱 클래스로 분리한다.

작업:

1. `adm/member_list_exel_parts/*`의 안내문, 오류문, 필터, 상태 표시 class를 조사한다.
2. 공용 메시지는 `ui-alert-*`, `ui-empty-*`로 `tailwind4/common.css`에 둔다.
3. export 전용 필터와 진행 상태는 `admin-export-*`로 `tailwind4/admin.css`에 둔다.
4. 변경 후 `npm run build:admin`을 실행한다.

완료 기준:

- export 화면의 상태와 오류 표현을 클래스 이름만 보고 구분할 수 있다.
- 운영 안내문이 utility 조합보다 의미 클래스 중심으로 읽힌다.

### 5순위: controller 읽기 비용 축소

목표:

- UI 마크업 잡음을 줄인 뒤 controller의 책임을 정리한다.

작업:

1. `adm/config_form.php`, `adm/member_form.php`, `adm/member_list.php`, `adm/member_list_exel.php`를 화면형 controller로 점검한다.
2. 긴 입력 처리, 화면 데이터 조립, 권한 분기가 controller에 남아 있으면 `lib/domain/admin/*`로 옮긴다.
3. 화면형 controller는 `admin_build_*_page_view()` 결과를 받아 렌더하는 형태로 맞춘다.
4. 완료형 controller는 가능하면 `admin_complete_*()` 호출 중심으로 정리한다.

완료 기준:

- controller 하나를 열었을 때 include, request, page view, render 흐름이 바로 보인다.
- controller 내부에 새 helper/function 정의가 없다.

### 6순위: member/admin 도메인 책임 재점검

목표:

- controller에서 빠진 책임이 올바른 domain 파일에 들어갔는지 확인한다.

작업:

1. `lib/domain/admin/` 파일을 request, validation, persist, flow, view/page 책임 기준으로 재분류한다.
2. `lib/domain/member/`의 인증/가입 흐름도 같은 기준으로 확인한다.
3. aggregate loader에는 include 선언만 남긴다.
4. 함수명은 `read`, `validate`, `find/list`, `store/update/delete`, `complete`, `build_page_view` 규칙과 맞춘다.

완료 기준:

- 새 기능을 추가할 파일을 이름만 보고 결정할 수 있다.
- AI가 기존 함수명을 따라 새 코드를 만들기 쉬운 상태가 된다.

### 7순위: 검증 루틴과 금지 패턴 자동화

목표:

- 반복해서 어긋나는 패턴을 사람이 기억하지 않아도 잡히게 한다.

작업:

1. `npm run check:refactor`가 현재 기준을 잘 반영하는지 확인한다.
2. PHP 템플릿에 긴 Tailwind utility 조합이 다시 늘어나면 잡을 수 있는 check script 추가를 검토한다.
3. 변경 범위별 검증 명령을 README 또는 maintainer guide에 반영한다.

완료 기준:

- `git diff --check`, `npm run check:refactor`, 필요한 `npm run build:*` 실행 기준이 명확하다.
- controller 비대화, aggregate loader 업무 로직 추가, 생성 CSS 직접 수정 같은 금지 패턴이 반복되지 않는다.

## 우선순위

1. 문서 참조와 목표 정렬
2. 관리자 controller와 export 흐름의 읽기 비용 축소
3. 회원 인증/가입 흐름의 request, validation, flow 책임 재점검
4. Tailwind utility class를 시맨틱 레이어 뒤로 감추기
5. 반복되는 금지 패턴을 check script로 고정

## 작업 체크리스트

새 리팩토링 작업을 시작할 때마다 아래 순서로 진행한다.

1. `git status --short`로 기존 변경을 확인한다.
2. 대상 controller와 연결된 `lib/domain/*` 파일을 먼저 읽는다.
3. 변경 목표가 동작 변경인지, 코드 이동인지 구분한다.
4. 한 번에 한 책임만 옮긴다.
5. Tailwind 스타일은 `tailwind4/` 원천에서 시맨틱 클래스로 수정하고 필요한 빌드를 실행한다.
6. 변경 범위에 맞는 check/build 명령을 실행한다.
7. 문서의 파일 지도나 금지 규칙이 바뀌면 같은 작업에서 갱신한다.

## 성공 상태

이 계획이 달성된 상태는 최신 PHP 프로젝트처럼 보이는 상태가 아니다. 기존 G5 개발자가 낯설지 않게 읽을 수 있고, AI가 파일 책임을 안정적으로 추론하며, 운영자가 작은 단위로 검토하고 배포할 수 있는 상태다.
