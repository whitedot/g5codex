// 관리자 리팩터 안전망.
// 사람이 모든 diff를 따라가지 않아도 아래 퇴행을 잡는 것이 목적이다:
// - controller/partial에 extract(), 직접 SQL, 전역 상태 조립이 다시 들어오는 경우
// - page view shell 적용을 admin_apply_page_view() 대신 수동 대입으로 되돌리는 경우
// - export 화면/stream에서 예전 inline JS, legacy view key, PHPExcel 경로가 부활하는 경우
// - aggregate loader에 include 선언 외 업무 로직이 들어오는 경우
const {
  path,
  projectRoot,
  listFiles,
  phpFiles,
  runPhpLint,
  assertNoMatches,
  assertFileMissing,
} = require('./lib/refactor-check-utils');

const admDir = path.join(projectRoot, 'adm');
const adminDomainDir = path.join(projectRoot, 'lib/domain/admin');

const lintFiles = [
  ...phpFiles(admDir, false),
  ...phpFiles(adminDomainDir, false),
];
runPhpLint(lintFiles, 'PHP executable was not found; admin PHP lint skipped.');

const forbiddenChecks = [
  {
    name: 'extract() usage',
    pattern: /extract\s*\(/,
    files: [...phpFiles(admDir, true), ...phpFiles(adminDomainDir, true)],
  },
  {
    name: 'implicit member list globals',
    pattern: /isset\(\$sfl\)|isset\(\$stx\)|isset\(\$sst\)|isset\(\$sod\)|isset\(\$page\)|isset\(\$w\)|isset\(\$url\)/,
    files: phpFiles(admDir, true),
  },
  {
    name: 'legacy admin template helpers',
    pattern: /subject_sort_link\s*\(|get_sideview\s*\(/,
    files: phpFiles(admDir, true),
  },
  {
    name: '$GLOBALS access',
    pattern: /\$GLOBALS\[/,
    files: [...phpFiles(admDir, true), ...phpFiles(adminDomainDir, true)],
  },
];

for (const check of forbiddenChecks) {
  assertNoMatches(check.name, check.files, check.pattern);
}

assertNoMatches(
  'legacy admin template helpers',
  phpFiles(admDir, true),
  /subject_sort_link\s*\(|get_sideview\s*\(/
);

assertNoMatches(
  'raw request access inside admin helper libs',
  listFiles(admDir, { recursive: true, filter: filePath => filePath.endsWith('.lib.php') }),
  /\$_GET|\$_POST|\$_REQUEST/
);

assertNoMatches(
  'legacy member form aliases',
  phpFiles(path.join(admDir, 'member_form_parts'), false),
  /\$w\b|\$mb\b|\$qstr\b|\$sfl\b|\$stx\b|\$sst\b|\$sod\b|\$page\b|\$member_level_options\b|\$mb_cert_history\b|\$display_mb_id\b|\$mask_preserved_id\b|\$sound_only\b|\$required_mb_id\b|\$required_mb_id_class\b|\$required_mb_password\b|\$mb_certify_yes\b|\$mb_certify_no\b|\$mb_adult_yes\b|\$mb_adult_no\b|\$mb_mailling_yes\b|\$mb_mailling_no\b|\$mb_open_yes\b|\$mb_open_no\b|\$mb_marketing_agree_yes\b|\$mb_marketing_agree_no\b/
);

assertNoMatches(
  'legacy member form view-model keys',
  [path.join(adminDomainDir, 'member-form.lib.php')],
  /'sound_only'|'required_mb_id'|'required_mb_id_class'|'required_mb_password'|'mb_certify_yes'|'mb_certify_no'|'mb_adult_yes'|'mb_adult_no'|'mb_mailling_yes'|'mb_mailling_no'|'mb_open_yes'|'mb_open_no'|'mb_marketing_agree_yes'|'mb_marketing_agree_no'/
);

const explicitRequestPages = [
  'index.php',
  'member_list.php',
  'member_form.php',
  'member_list_update.php',
  'member_form_update.php',
  'member_delete.php',
  'config_form.php',
  'member_list_exel.php',
  'member_list_exel_export.php',
  'member_list_file_delete.php',
].map(file => path.join(admDir, file));

assertNoMatches(
  'legacy admin entry superglobal request access',
  explicitRequestPages.concat([path.join(admDir, 'ajax.token.php'), path.join(admDir, 'config_form_update.php')]),
  /\$_GET|\$_POST|\$_REQUEST|\$_SERVER/
);

assertNoMatches(
  'legacy request global extract opt-out define',
  explicitRequestPages,
  /define\('G5_SKIP_REQUEST_GLOBAL_EXTRACT', true\);/
);

assertNoMatches(
  'legacy query state alias opt-out define',
  explicitRequestPages,
  /define\('G5_SKIP_QUERY_STATE_ALIAS_ASSIGNMENT', true\);/
);

assertNoMatches(
  'legacy admin page view shell assignment',
  explicitRequestPages,
  /\$g5\['title'\]\s*=\s*\$[A-Za-z_][A-Za-z0-9_]*\['title'\]|\$admin_container_class\s*=\s*\$[A-Za-z_][A-Za-z0-9_]*\['admin_container_class'\]|\$admin_page_subtitle\s*=\s*\$[A-Za-z_][A-Za-z0-9_]*\['admin_page_subtitle'\]/
);

assertNoMatches(
  'legacy member export page wiring',
  [path.join(admDir, 'member_list_exel.php')],
  /onclick="location\.href='\\?'"|member_list_exel_export\.php\?\$\{query\}/
);

assertNoMatches(
  'legacy member list inline behavior',
  [path.join(admDir, 'member_list.php')],
  /<script>|onclick=|onsubmit=|function deleteMember|function fmemberlist_submit/
);

assertNoMatches(
  'legacy member list partial request state',
  phpFiles(path.join(admDir, 'member_list_parts'), false),
  /\$member_list_request|\$g5\['title'\]|get_paging\(|number_format\(|get_sanitize_input\(/
);

assertNoMatches(
  'legacy member list summary raw quick links',
  [path.join(admDir, 'member_list_parts', 'summary.php')],
  /\['(?:list_all_url|blocked_url|left_url|quick_view)'\]|\?\s*' aria-current=/
);

assertNoMatches(
  'legacy member list summary view-model keys',
  [path.join(adminDomainDir, 'member-list-view.lib.php')],
  /'(?:list_all_url|blocked_url|left_url)'\s*=>/
);

assertNoMatches(
  'legacy member list search raw option fields',
  [path.join(admDir, 'member_list_parts', 'search.php')],
  /\['value'\]|\['label'\]|\['selected'\]/
);

assertNoMatches(
  'legacy member list table raw column/action fields',
  [path.join(admDir, 'member_list_parts', 'table.php')],
  /\$column\['(?:id|class|href|label)'\]|\$action\['(?:class|href|label|mb_id)'\]/
);

assertNoMatches(
  'legacy member export inline behavior',
  [path.join(admDir, 'member_list_exel.php')],
  /<script>|EventSource\(|function startExcelDownload|function showDownloadPopup|function handleProgressUpdate|htmlspecialchars\(|environment_ready'\]\s*\?/
);

assertNoMatches(
  'legacy member export partial hidden state',
  phpFiles(path.join(admDir, 'member_list_exel_parts'), false),
  /\$filter_state|\$member_export_links|\$member_export_view\['form_token'\]|\$member_export_view\['sfl_option_items'\]|\$member_export_view\['level_start_options'\]|\$member_export_view\['level_end_options'\]|\$member_export_view\['intercept_option_items'\]|\$member_export_view\['ad_range_option_items'\]|\$member_export_view\['environment_error'\]|number_format\(|\['value'\]|\['label'\]|\['selected'\]|active_ad_range_text/
);

assertNoMatches(
  'legacy member export filter raw output keys',
  [path.join(admDir, 'member_list_exel_parts', 'filter.php')],
  /\['(?:form_token|use_stx_checked|stx_value|stx_cond_like_checked|stx_cond_equal_checked|use_level_checked|use_date_checked|date_start_value|date_end_value|use_intercept_checked|use_hp_checked|ad_range_only_checked|ad_range_wrap_class(?:_attr)?|custom_period_class(?:_attr)?|channel_row_class(?:_attr)?|agree_date_start_value|agree_date_end_value|ad_mailling_checked|reset_url)'\]/
);

assertNoMatches(
  'legacy member export filter view-model keys',
  [path.join(adminDomainDir, 'export-view.lib.php')],
  /'(?:form_token|use_stx_checked|stx_value|stx_cond_like_checked|stx_cond_equal_checked|use_level_checked|use_date_checked|date_start_value|date_end_value|use_intercept_checked|use_hp_checked|ad_range_only_checked|ad_range_wrap_class(?:_attr)?|custom_period_class(?:_attr)?|channel_row_class(?:_attr)?|agree_date_start_value|agree_date_end_value|ad_mailling_checked)'\s*=>/
);

assertNoMatches(
  'legacy member form inline behavior',
  [path.join(admDir, 'member_form.php'), path.join(admDir, 'member_form_parts/history.php')],
  /onsubmit=|onclick=|fmember_submit\(|member_form_parts\/script\.php|\$member_form_page_state|get_admin_token\(\)|get_sanitize_input\(|\$member_form_request\['w'\]|\$member_list_request\['sfl'\]|\$member_list_request\['stx'\]|\$member_list_request\['sst'\]|\$member_list_request\['sod'\]|\$member_list_request\['page'\]/
);

assertNoMatches(
  'legacy config form inline behavior',
  [path.join(admDir, 'config_form.php'), ...phpFiles(path.join(admDir, 'config_form_parts'), false)],
  /onsubmit=|document\.addEventListener|function fconfigform_submit|check_config_captcha_open|toggleConfigCaptchaFields|\$captcha_js/
);

assertNoMatches(
  'legacy config form partial hidden state',
  phpFiles(path.join(admDir, 'config_form_parts'), false),
  /\$config\[|\$config_form_view\[/
);

assertNoMatches(
  'legacy admin select raw option fields',
  [
    ...phpFiles(path.join(admDir, 'config_form_parts'), false),
    path.join(admDir, 'member_list_parts', 'search.php'),
    path.join(admDir, 'member_form_parts', 'basic.php'),
    path.join(admDir, 'member_list_exel_parts', 'filter.php'),
  ],
  /<option[^>]+<\?php echo \$option\['value'\]|\$option\['selected'\]\s*\?|\$option\['label'\]/
);

assertNoMatches(
  'legacy member form radio raw option fields',
  [
    path.join(admDir, 'member_form_parts', 'contact.php'),
    path.join(admDir, 'member_form_parts', 'consent.php'),
  ],
  /\$option\['(?:value|label|checked|id)'\]/
);

assertFileMissing(path.join(admDir, 'config_form_parts', 'script.php'), 'legacy config form script partial found');

assertNoMatches(
  'legacy dashboard shell hook wiring',
  [path.join(admDir, 'index.php')],
  /run_replace\('adm_index_addtional_content_/
);

assertNoMatches(
  'legacy dashboard template count formatting',
  [path.join(admDir, 'index.php')],
  /number_format\(/
);

assertNoMatches(
  'legacy member form update shell wiring',
  [path.join(admDir, 'member_form_update.php')],
  /register\.lib\.php|admin_read_member_form_request\(\$_POST\)|member_read_admin_member_request\(\$_POST\)|admin_build_member_list_qstr\(\$_POST|admin_complete_member_form_update_request\(\$member_form_request/
);

assertNoMatches(
  'legacy member delete shell wiring',
  [path.join(admDir, 'member_delete.php')],
  /check_demo\(|admin_build_member_list_qstr\(\$_POST|admin_read_member_delete_request\(\$_POST\)|admin_complete_member_delete_request\(\$request,/
);

assertNoMatches(
  'legacy member export shell wiring',
  [path.join(admDir, 'member_list_exel.php'), path.join(admDir, 'member_list_exel_export.php')],
  /member_list_exel\.lib\.php|check_demo\(|admin_build_member_export_runtime_context\(\$g5|admin_complete_member_export_stream_request\(\$_GET|admin_run_member_export\(/
);

assertNoMatches(
  'legacy member export stream count formatting',
  [path.join(adminDomainDir, 'export-stream.lib.php')],
  /number_format\(/
);

assertNoMatches(
  'legacy admin head runtime link access',
  [path.join(admDir, 'admin.head.php')],
  /G5_ADMIN_URL|G5_MEMBER_URL|G5_URL|\$member\['mb_id'\]|\$config\['cf_title'\]|admin_csrf_token_key\(\)|\$_COOKIE|\$adm_menu_cookie|\$admin_profile_|\$admin_navigation_items|\$admin_container_class_attr|\$admin_page_subtitle_text|\$g5\['title'\](?!\)\s*\?|\]\s*:\s*'')|\$admin_head_view\['adm_menu_cookie'\]|\$admin_head_view\['admin_(?:profile_(?:name|id|mail|initial)|profile_manage_url|logout_url|home_url|site_title)'\]|\$sub_item\['(?:href|title|item_class|menu_code)'\]|\$nav_item\['(?:title|icon_id|item_class|panel_class|aria_expanded)'\]/
);

assertNoMatches(
  'legacy admin head inline behavior',
  [path.join(admDir, 'admin.head.php')],
  /document\.addEventListener|function imageview|var tempX|var tempY|syncDesktopSidebarState|updateMenuScrollbar/
);

assertNoMatches(
  'legacy admin tail inline behavior',
  [path.join(admDir, 'admin.tail.php')],
  /onclick=|document\.addEventListener|\$_SERVER\['HTTP_HOST'\]|G5_ADMIN_URL|<script src="|\$script_src_view|\['script_src_views'\]|\$admin_(?:(?:core|config_form|member_export|member_form|member_list|shell)_js_src|js_src)|\$admin_tail_view\['(?:copyright_host|print_version|admin_(?:(?:core|config_form|member_export|member_form|member_list|shell)_js_src|js_src))'\]/
);

assertNoMatches(
  'legacy admin head sub shell state',
  [path.join(admDir, 'head.sub.admin.php')],
  /\$g5\['request_context'\]\['query_state'\]|isset\(\$g5\['body_script'\]\)|var g5_url|var g5_member_url|G5_IS_MOBILE|G5_JS_URL|G5_MEMBER_URL|G5_ADMIN_URL|json_encode\(|echo '<(?:link|meta)|<script src="|https:\/\/cdn\.jsdelivr\.net|if\s*\(\$is_member\b|\$member_logout_url_attr|\$login_status_text|\$show_login_status|\$head_sub_view\['(?:page_title|head_title|pretendard_font_href|common_css_href|admin_css_href|sticky_anchor_tabs_ver|member_logout_url|login_status_text|show_login_status)'\]/
);

assertNoMatches(
  'legacy admin head sub raw js globals',
  [path.join(admDir, 'head.sub.admin.php'), path.join(adminDomainDir, 'ui-shell.lib.php')],
  /js_globals/
);

assertNoMatches(
  'legacy admin shell output keys',
  [path.join(adminDomainDir, 'ui-shell.lib.php')],
  /'(?:adm_menu_cookie|admin_sidebar_collapsed|admin_profile_name|admin_profile_id|admin_profile_mail|admin_profile_initial|admin_profile_manage_url|admin_logout_url|admin_home_url|admin_site_title|admin_csrf_token_key|page_title|head_title|pretendard_font_href|common_css_href|admin_css_href|sticky_anchor_tabs_ver|member_logout_url|show_login_status|login_status_text|copyright_host|print_version|menu_key|is_current|is_open|admin_(?:core|config_form|member_export|member_form|member_list|shell)_js_src|admin_js_src)'\s*=>/
);

assertNoMatches(
  'legacy admin shell global menu lookup',
  [path.join(adminDomainDir, 'ui-shell.lib.php')],
  /function\s+admin_menu_find_by\s*\(|global\s+\$menu/
);

assertFileMissing(path.join(admDir, 'member_list_exel.lib.php'), 'legacy member export helper file found');

const exportFiles = listFiles(adminDomainDir, {
  recursive: false,
  filter: filePath => /^export.*\.php$/.test(path.basename(filePath)),
});

const adminAggregateLoaders = [
  'helper.lib.php',
  'config.lib.php',
  'member.lib.php',
  'member-form.lib.php',
  'member-list.lib.php',
  'export.lib.php',
  'security.lib.php',
  'ui.lib.php',
].map(file => path.join(adminDomainDir, file));

assertNoMatches(
  'admin aggregate loader business functions',
  adminAggregateLoaders,
  /^function\s+/m
);

assertNoMatches(
  'admin aggregate loader SQL or branching logic',
  adminAggregateLoaders,
  /\bsql_query\s*\(|\bsql_fetch\s*\(|^(?!if \(!defined\('_GNUBOARD_'\)).*\b(?:if|foreach|for|while)\s*\(/
);

assertNoMatches(
  'legacy export naming',
  exportFiles,
  /\bget_export_config\s*\(|\bget_member_export_params\s*\(|\bmember_export_get_total_count\s*\(|\bmember_export_build_where\s*\(|\bmember_export_get_config\s*\(|\bmember_export_open_statement\s*\(|\bmember_export_fetch_sheet_rows\s*\(|\bmember_export_create_xlsx\s*\(|\bmember_export_create_zip\s*\(|\bmember_export_ensure_directory\s*\(|\bmember_export_delete\s*\(|\bmember_export_delete_directory\s*\(|\bmember_export_write_log\s*\(|\bmember_export_send_progress\s*\(|\bmember_export_set_sse_headers\s*\(/
);

assertNoMatches(
  'legacy export constant naming',
  exportFiles,
  /\bMEMBER_EXPORT_PAGE_SIZE\b|\bMEMBER_EXPORT_MAX_SIZE\b|\bMEMBER_BASE_DIR\b|\bMEMBER_BASE_DATE\b|\bMEMBER_EXPORT_DIR\b|\bMEMBER_LOG_DIR\b/
);

assertNoMatches(
  'legacy export globals',
  [path.join(adminDomainDir, 'export.lib.php')],
  /global \$g5|global \$member/
);

assertNoMatches(
  'legacy admin request access',
  [path.join(adminDomainDir, 'bootstrap.lib.php'), path.join(adminDomainDir, 'token.lib.php')],
  /\$_REQUEST/
);

assertNoMatches(
  'legacy admin session access',
  phpFiles(adminDomainDir, true).filter(file => path.basename(file) !== 'token.lib.php'),
  /get_session\(|\$_SESSION/
);

assertNoMatches(
  'admin domain raw html escaping outside output helpers',
  phpFiles(adminDomainDir, true).filter(file => ![
    'token.lib.php',
    'view-helper.lib.php',
    'xlsx-writer.lib.php',
  ].includes(path.basename(file))),
  /htmlspecialchars\(/
);

assertNoMatches(
  'admin domain raw json encoding outside response helpers',
  phpFiles(adminDomainDir, true).filter(file => ![
    'token.lib.php',
    'view-helper.lib.php',
    'export-stream.lib.php',
  ].includes(path.basename(file))),
  /json_encode\(/
);

const adminTemplateFiles = phpFiles(admDir, true);

assertNoMatches(
  'raw Tailwind table head utility bundle',
  adminTemplateFiles,
  /class="[^"]*\bborder-default-300\b[^"]*\bbg-default-100\b[^"]*\bborder-b\b[^"]*\bfont-semibold\b[^"]*\btext-xs\b/
);

assertNoMatches(
  'raw Tailwind flex action utility bundle',
  adminTemplateFiles,
  /class="[^"]*\bflex\b[^"]*\bitems-center\b[^"]*\bjustify-end\b[^"]*\bgap-2\b[^"]*\bpx-0\b[^"]*\bpt-2\b/
);

assertNoMatches(
  'raw Tailwind export alert utility bundle',
  adminTemplateFiles,
  /class="[^"]*\brounded-lg\b[^"]*\bborder\b[^"]*\bborder-danger\/30\b[^"]*\bbg-danger\/5\b[^"]*\bpx-4\b[^"]*\bpy-3\b/
);

assertNoMatches(
  'raw Tailwind admin form action utility bundle',
  adminTemplateFiles,
  /class="[^"]*\badmin-form-sticky-actions\b[^"]*\bflex\b[^"]*\bitems-center\b[^"]*\bborder-t\b[^"]*\bborder-dashed\b/
);

assertNoMatches(
  'raw Tailwind export responsive width utility',
  phpFiles(path.join(admDir, 'member_list_exel_parts'), false),
  /class="[^"]*\b(?:lg:w-auto|lg:w-72|xl:w-80)\b/
);

assertNoMatches(
  'raw Tailwind primary link utility bundle',
  adminTemplateFiles.concat(phpFiles(adminDomainDir, true)),
  /class="[^"]*\bfont-semibold\b[^"]*\btext-primary\b[^"]*\bhover:underline\b/
);

assertNoMatches(
  'legacy member export progress class names',
  [path.join(admDir, 'admin-member-export.js')],
  /class="(?:excel-download-progress|progress-desc|progress-summary|progress-message|progress-error|progress-spinner|progress-box|progress-download-box|loading-message|spinner)"|querySelector\('\.(?:progress-summary|progress-message|progress-error|progress-spinner|progress-download-box|loading-message)'\)/
);

assertNoMatches(
  'legacy admin popup shell class names',
  [path.join(admDir, 'admin.tail.php'), path.join(admDir, 'admin-member-export.js')],
  /popup-close-btn/
);

assertNoMatches(
  'legacy admin popup id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#adminPopupContainer\s+#popupOverlay|#adminPopupContainer\s+#popupTitle|#adminPopupContainer\s+#popupBody|#adminPopupContainer\s+#popupFooter|#adminPopupContainer\s+\.popup-close-btn/
);

assertNoMatches(
  'legacy admin footer class names',
  [path.join(admDir, 'admin.tail.php'), path.join(admDir, 'admin-shell.js')],
  /scroll_top/
);

assertNoMatches(
  'legacy admin footer id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#ft\s*\{|#ft\s+p\s*\{|\.scroll_top/
);

assertNoMatches(
  'legacy member list id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#fmemberlist\s+/
);

assertNoMatches(
  'legacy config/member tab id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#(?:config|member)_tabs_nav/
);

assertNoMatches(
  'legacy admin content id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#container(?:\b|[ >.#:])|#container_title|#container_subtitle/
);

assertNoMatches(
  'legacy admin topbar id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#hd_top|#tnb\b|#btn_gnb_mobile/
);

assertNoMatches(
  'legacy admin shell layout id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#hd\s*\{|#gnb\s*\{|#wrapper(?:\s*\{|,)|#adminSidebarBackdrop\s*\{/
);

assertNoMatches(
  'legacy admin sidebar internals id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#gnb\s+[.#>]|#btn_gnb\b/
);

assertNoMatches(
  'unused legacy content tab id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#content_tabs_(?:bar|nav)/
);

assertNoMatches(
  'legacy admin id-based styling',
  [path.join(projectRoot, 'tailwind4/admin.css')],
  /#[A-Za-z0-9_-]+/
);

assertNoMatches(
  'legacy shared form row id-based alignment',
  [path.join(projectRoot, 'tailwind4/common.css')],
  /:has\(#sodr_request_log_wrap\)/
);

console.log('Admin refactor checks passed.');
