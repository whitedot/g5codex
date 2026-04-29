# 사이트 관리 기능

사이트 관리 도메인은 커뮤니티와 분리된 독립 기능을 담당한다.

## 페이지 관리

- 관리자 메뉴: `페이지 관리 > 전체 페이지 관리`
- 관리자 파일: `adm/site_page_list.php`, `adm/site_page_form.php`, `adm/site_page_form_update.php`
- 공개 주소: `/page.php?slug={페이지ID}`
- 도메인 라이브러리: `lib/domain/site`
- 테이블: `site_page`
- 설치 스키마: `install/site_schema.sql`

`site_page`는 페이지 ID, 제목, 요약, 본문, 본문 형식, 접근 레벨, PC/모바일 노출 여부와 상태를 저장한다.
사이트 메뉴에서는 메뉴 유형 `페이지`를 선택하고 연결 대상 ID에 페이지 ID를 입력해 독립 페이지로 연결한다.
