# 커뮤니티 기능 현행 구조

## 목적

이 문서는 현재 저장소에 복구된 커뮤니티 기능의 구조와 운영 기준을 설명한다. 초기 설계 계획 문서가 아니라, 2026-04-28 현재 코드 기준의 구현 현황 문서다.

커뮤니티는 기존 그누보드5의 게시판별 동적 테이블 구조를 그대로 되살리지 않고, 단일 도메인 테이블 세트와 `lib/domain/community/` 함수 계층으로 구성한다.

## 사용자 기능

사용자 진입점은 `community/` 아래에 있다.

```text
community/
  index.php
  board.php
  view.php
  write.php
  write_update.php
  delete.php
  comment_update.php
  comment_delete.php
  download.php
  latest.php
  scrap.php
  scrap_update.php
  views/basic/
  views/mail/
```

지원 흐름은 다음과 같다.

- 게시판 목록, 게시글 보기, 작성, 수정, 삭제
- 댓글 작성과 삭제
- 첨부파일 업로드와 다운로드
- 최신글 조회
- 스크랩 등록과 목록 조회
- 게시글/댓글 메일 알림
- 커뮤니티 전용 포인트 지급, 차감, 만료 정산

## 관리자 기능

관리자 진입점은 `adm/` 아래에 있다.

```text
adm/
  community_board_list.php
  community_board_form.php
  community_board_form_update.php
  community_post_list.php
  community_post_list_update.php
  community_comment_list.php
  community_comment_list_update.php
  community_point_list.php
  community_point_adjust.php
  community_point_expire.php
```

관리자 화면은 `adm/community_*_parts/`의 부분 템플릿과 `lib/domain/community/admin-*.lib.php`의 request/persist/render 함수로 구성한다.

## 도메인 라이브러리

커뮤니티 도메인은 다음 파일로 분리한다.

```text
lib/domain/community/
  community.lib.php
  runtime.lib.php
  request.lib.php
  permission.lib.php
  post-persist.lib.php
  comment-persist.lib.php
  attachment-persist.lib.php
  latest-persist.lib.php
  scrap-persist.lib.php
  notification.lib.php
  point.lib.php
  search.lib.php
  cache.lib.php
  render.lib.php
  admin.lib.php
  admin-request.lib.php
  admin-persist.lib.php
  admin-render.lib.php
```

`community.lib.php`는 aggregate loader 역할만 맡는다. 요청 정규화, 권한, 저장, 렌더링, 관리자 처리는 각 전용 파일에 둔다.

## 런타임 테이블

`lib/domain/community/runtime.lib.php`는 `G5_TABLE_PREFIX`를 기준으로 아래 테이블명을 등록한다. 설치 시 `install/install_db.php`도 같은 테이블명을 `data/dbconfig.php`에 기록한다.

```text
community_config_table
community_board_group_table
community_board_table
community_board_category_table
community_post_table
community_comment_table
community_latest_table
community_point_ledger_table
community_point_available_table
community_point_wallet_table
site_menu_table
site_banner_table
community_attachment_table
community_scrap_table
```

실제 스키마 원본은 `install/community_schema.sql`이다. 운영 DB에 수동 적용할 때는 `g5_` prefix를 배포 환경의 `G5_TABLE_PREFIX`에 맞춘다.

## 스키마 요약

- `community_config`: 포인트 만료 기준, 신규 게시판 기본 권한과 공통 기본값
- `community_board_group`: 게시판 그룹과 그룹별 기본 권한
- `community_board`: 게시판 설정, 권한 레벨, 업로드 정책, 메일/포인트 사용 여부
- `community_board_category`: 게시판별 카테고리
- `community_post`: 게시글 본문, 공지/비밀글 상태, 댓글/조회/첨부 집계
- `community_comment`: 댓글 본문과 상태
- `community_latest_index`: 최신글 조회용 인덱스
- `community_point_wallet`: 회원별 커뮤니티 포인트 잔액
- `community_point_ledger`: 포인트 원장
- `community_point_available`: 만료 및 차감 가능한 포인트 묶음
- `site_menu`: 페이지, 게시판 그룹, 게시판, 직접 URL 기반 사이트 메뉴
- `site_banner`: 기능별로 등록 가능한 위치 기반 배너 이미지, 링크, 노출 기간
- `community_attachment`: 첨부파일 메타데이터
- `community_scrap`: 회원별 스크랩

성능 점검 쿼리와 기대 인덱스는 `docs/community-performance-checklist.md`를 따른다.

## 구현 원칙

- 게시판별 동적 테이블을 만들지 않는다.
- 최신글은 `community_latest_index`를 사용한다.
- 댓글 수, 첨부 수, 마지막 활동일은 쓰기 시점에 갱신한다.
- 사용자 입력은 request 파일에서 정규화하고, SQL 값은 prepared helper로 바인딩한다.
- HTML 출력은 저장 전 escape가 아니라 출력 직전 escape helper를 사용한다.
- 게시글/댓글/포인트 상태 변경은 가능한 한 한 흐름 안에서 일관되게 처리한다.
- 배너 위치는 `site_banner_position_groups` replace hook으로 기능별 항목을 추가한다.

## 운영 점검

배포 후 다음 흐름을 실제 DB에서 확인한다.

1. 게시판 생성과 카테고리 저장
2. 게시글 작성, 수정, 삭제
3. 댓글 작성과 삭제
4. 첨부파일 업로드와 다운로드
5. 최신글 조회
6. 스크랩 등록과 목록 조회
7. 게시글/댓글 메일 알림
8. 포인트 지급, 차감, 만료 정산
9. 관리자 게시글/댓글 상태 일괄 변경

## 남은 개선 후보

- 커뮤니티/관리자 request 입력도 회원 도메인처럼 raw input context로 단계 이전
- 포인트 처리에서 DB 엔진이 InnoDB인 환경의 row lock 기준 보강
- 최신글/게시판 메타 캐시 무효화 시나리오 자동 테스트 추가
- PHPUnit 또는 별도 HTTP 회귀 테스트 도입
