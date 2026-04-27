# Member Controller Pattern

기준일: 2026-04-27

## 목적

이 문서는 `member/*.php` 화면 컨트롤러를 어떤 책임 경계로 유지할지 정의한다.

`member` 도메인은 이후 `community`, `shop`, `booking` 컨트롤러 패턴의 기준 템플릿으로 사용한다. 신규 도메인 스타터 예시는 `docs/architecture/procedural-domain-starter.md`에서 이 구조를 복제해 사용한다.

## 기본 흐름

```text
controller
-> request.lib.php
-> validation.lib.php
-> persist.lib.php
-> flow.lib.php
-> page.lib.php / render.lib.php
```

## 역할 규칙

### controller

- `_common.php` 포함
- 필요한 최소 외부 라이브러리 포함
- 도메인 함수 호출 순서만 조합
- 화면 렌더 또는 redirect/alert로 종료

### request.lib.php

- `$_POST`, `$_GET`, `$_SESSION` 입력을 정규화
- trim, 기본값, 간단한 형식 정리 수행
- DB 조회나 화면 출력 금지
- 현재는 `request-register.lib.php`, `request-auth.lib.php`, `request-account.lib.php`, `request-ajax.lib.php`를 로드하는 진입 로더 역할만 수행

### validation.lib.php

- 필수값, 권한, 접근 가능 여부, 상태 체크 수행
- 실패 시 `alert`, `confirm`, `alert_close`로 즉시 종료 가능
- 쿼리 자체는 되도록 `persist`를 통해 가져온 결과를 기준으로 판단
- 현재는 `validation-register.lib.php`, `validation-register-email.lib.php`, `validation-auth.lib.php`, `validation-account.lib.php`, `validation-ajax.lib.php`를 로드하는 진입 로더 역할만 수행

### persist.lib.php

- 회원 조회, 저장, 토큰 갱신, 비밀번호 변경 같은 DB 접근 담당
- 화면 메시지와 redirect를 직접 처리하지 않음
- 현재는 `persist-core.lib.php`, `persist-register.lib.php`, `persist-register-email.lib.php`, `persist-auth.lib.php`, `persist-account.lib.php`를 로드하는 진입 로더 역할만 수행

### flow.lib.php

- 여러 단계가 묶인 처리 조합 담당
- 메일 발송, 세션 정리, 트랜잭션 보조, 후처리 담당
- 현재는 `flow-core.lib.php`, `flow-auth.lib.php`, `flow-register.lib.php`, `flow-register-email.lib.php`, `flow-account.lib.php`, `flow-ajax.lib.php`를 로드하는 진입 로더 역할만 수행
- 템플릿 없는 완료 응답도 가능한 한 이 레이어에서 마무리한다. 예: 메일 인증 완료 alert, 비밀번호 재설정 완료 alert, AJAX 중복검사 후 세션 반영
- 완료형 컨트롤러는 가능한 한 `member_complete_*` 또는 `member_process_*` 호출 1회로 끝나도록 유지

### page.lib.php / render.lib.php

- view 렌더링
- 공통 페이지 조합
- 자동 post, 공통 alert script 같은 응답 보조
- 현재 `render.lib.php`는 `render-template.lib.php`, `render-view.lib.php`, `render-response.lib.php`, `render-register-form.lib.php`, `render-page-view.lib.php`를 로드하는 진입 로더 역할만 수행
- 화면 컨트롤러가 직접 템플릿 데이터 조합을 만들지 않고, 가능하면 `render-page-view.lib.php` 계열 helper에서 `title`, `data`, `options`를 반환받아 사용
- 현재 `page.lib.php`는 `page-controller.lib.php`, `page-hook.lib.php`, `page-shell.lib.php`를 로드하는 진입 로더 역할만 수행
- `member/_head.php`, `_tail.php`, `_head.sub.php`, `_tail.sub.php`는 레이아웃 include를 직접 결정하지 않고 `page-shell.lib.php` 함수만 호출

## 공용 wrapper 경로 범위

- `member/_head.php`, `member/_tail.php`, `member/_head.sub.php`, `member/_tail.sub.php`는 기존 G5 include 경로 호환을 위한 얇은 wrapper로만 유지한다.
- 실제 layout include 경로 선택은 `lib/domain/member/page-shell.lib.php`의 `member_include_layout_head()`, `member_include_layout_tail()`에서만 담당한다.
- 화면 controller는 직접 `_head.php`, `_tail.php`, `head.sub.php`, `tail.sub.php`를 include하지 않고 `MemberPageController::renderPage()` 또는 `member_include_page_head()`/`member_include_page_tail()` 경유로 처리한다.
- 새 wrapper가 필요하면 먼저 기존 G5 호환 경로인지 확인하고, 업무 로직이나 화면 데이터 조립은 wrapper에 넣지 않는다.

## 현재 표준 대상

- `ajax.mb_email.php`
- `ajax.mb_hp.php`
- `ajax.mb_id.php`
- `ajax.mb_nick.php`
- `email_stop.php`
- `login.php`
- `login_check.php`
- `logout.php`
- `member_cert_refresh.php`
- `member_cert_refresh_update.php`
- `member_confirm.php`
- `member_leave.php`
- `password_lost.php`
- `password_reset.php`
- `password_lost_certify.php`
- `register.php`
- `register_form.php`
- `register_form_update.php`
- `password_lost2.php`
- `password_reset_update.php`
- `register_result.php`
- `email_certify.php`
- `register_email.php`
- `register_email_update.php`
- `member_cert_refresh_update.php`

## 금지 사항

- 컨트롤러에서 직접 SQL 작성
- 컨트롤러에서 세션 키 문자열을 반복 정의
- 컨트롤러에서 메일 발송 상세 조립
- 컨트롤러에서 중복 검증 로직 재구현

## 신규 도메인 적용 기준

- `community` 같은 신규 도메인은 먼저 `request.lib.php`, `validation.lib.php`, `persist.lib.php`, `flow.lib.php`, `render.lib.php`, `page.lib.php` aggregate loader를 만들고, loader에는 include 선언만 둔다.
- 첫 화면형 controller는 `domain_read_*_request()`와 `domain_build_*_page_view()` 호출 중심으로 만든다.
- 첫 완료형 controller는 `domain_complete_*()` 호출 1회로 끝내고, 검증/저장/후처리는 validation, persist, flow 파일로 나눈다.
- 이메일 인증처럼 한 업무 안에서도 저장 책임이 커지면 `persist-register-email.lib.php`처럼 업무 단위 persist 파일로 분리한다.

## 다음 정리 우선순위

1. 회원 인증/가입 흐름은 `docs/architecture/manual-test-scenarios.md` 기준으로 수동 확인한다.
2. 공용 wrapper가 늘어날 때는 이 문서의 wrapper 범위와 `check:member-request-refactor` 기준을 함께 갱신한다.
