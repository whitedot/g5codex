# 커뮤니티 성능 점검 체크리스트

## 목적

커뮤니티 복구 후 주요 조회 흐름이 의도한 인덱스를 사용하는지 확인한다. 실제 운영 DB의 데이터 분포에 따라 실행 계획은 달라질 수 있으므로, 배포 전과 트래픽 증가 시점에 반복 점검한다.

## 공통 기준

- `type`은 가능하면 `ref`, `range`, `const` 범위에 머문다.
- `key`는 아래 기대 인덱스 중 하나가 선택되어야 한다.
- `rows`가 게시글/댓글/포인트 전체 건수에 비례해 커지면 재검토한다.
- `Extra`에 `Using filesort`가 반복적으로 보이면 정렬 조건과 인덱스를 다시 본다.

## 게시글 목록

```sql
EXPLAIN
SELECT *
  FROM g5_community_post
 WHERE board_id = 'notice'
   AND status = 'published'
   AND is_notice = 0
 ORDER BY last_activity_at DESC, post_id DESC
 LIMIT 0, 15;
```

기대 인덱스: `idx_board_list`

카테고리 필터 사용 시:

```sql
EXPLAIN
SELECT *
  FROM g5_community_post
 WHERE board_id = 'notice'
   AND status = 'published'
   AND category_id = 1
   AND is_notice = 0
 ORDER BY last_activity_at DESC, post_id DESC
 LIMIT 0, 15;
```

기대 인덱스: `idx_board_category`

## 공지글

```sql
EXPLAIN
SELECT *
  FROM g5_community_post
 WHERE board_id = 'notice'
   AND status = 'published'
   AND is_notice = 1
   AND (notice_started_at = '0000-00-00 00:00:00' OR notice_started_at <= NOW())
   AND (notice_ended_at = '0000-00-00 00:00:00' OR notice_ended_at >= NOW())
 ORDER BY notice_order ASC, notice_started_at DESC, post_id DESC;
```

기대 인덱스: `idx_board_notice`

## 관리자 댓글 목록

```sql
EXPLAIN
SELECT c.*, p.board_id AS post_board_id, p.title AS post_title
  FROM g5_community_comment c
  LEFT JOIN g5_community_post p ON p.post_id = c.post_id
 WHERE c.status = 'published'
 ORDER BY c.comment_id DESC
 LIMIT 0, 20;
```

기대 인덱스: `idx_status_comment`

## 최신글

```sql
EXPLAIN
SELECT *
  FROM g5_community_latest_index
 WHERE scope = 'board'
 ORDER BY last_activity_at DESC, post_id DESC
 LIMIT 20;
```

기대 인덱스: `idx_scope_latest`

게시판별 최신글:

```sql
EXPLAIN
SELECT *
  FROM g5_community_latest_index
 WHERE scope = 'board'
   AND board_id = 'notice'
 ORDER BY last_activity_at DESC, post_id DESC
 LIMIT 20;
```

기대 인덱스: `idx_scope_latest`

## 알림 로그

```sql
EXPLAIN
SELECT *
  FROM g5_community_notification_log
 WHERE status = 'failed'
 ORDER BY created_at DESC
 LIMIT 0, 20;
```

기대 인덱스: `idx_status_created`

## 포인트 원장

```sql
EXPLAIN
SELECT *
  FROM g5_community_point_ledger
 WHERE mb_id = 'member1'
 ORDER BY ledger_id DESC
 LIMIT 0, 20;
```

기대 인덱스: `idx_member_ledger`

## 사용 가능 포인트 차감 대상

```sql
EXPLAIN
SELECT *
  FROM g5_community_point_available
 WHERE mb_id = 'member1'
   AND amount_remaining > 0
   AND (expires_at = '0000-00-00 00:00:00' OR expires_at >= NOW())
 ORDER BY CASE WHEN expires_at = '0000-00-00 00:00:00' THEN 1 ELSE 0 END ASC,
          expires_at ASC,
          available_id ASC;
```

기대 인덱스: `idx_member_available`

## 만료 포인트 정산 대상

```sql
EXPLAIN
SELECT *
  FROM g5_community_point_available
 WHERE amount_remaining > 0
   AND expires_at <> '0000-00-00 00:00:00'
   AND expires_at < NOW()
 ORDER BY expires_at ASC,
          available_id ASC;
```

기대 인덱스: `idx_available_expiry`

## 조치 기준

- 기대 인덱스가 선택되지 않으면 실제 `WHERE`, `ORDER BY`, cardinality를 확인한다.
- 검색어 `LIKE '%keyword%'`는 기본 인덱스를 활용하기 어렵다. 트래픽이 커지면 별도 검색 인덱스 adapter로 분리한다.
- 최신글과 게시판 메타데이터는 캐시가 비어도 DB 기준으로 정상 동작해야 한다.
