<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_attachment_table()
{
    global $g5;

    return $g5['community_attachment_table'];
}

function community_attachment_base_path()
{
    return G5_DATA_PATH . '/file/community';
}

function community_attachment_relative_dir($post_id)
{
    return 'file/community/' . (int) $post_id;
}

function community_attachment_absolute_path(array $attachment)
{
    return G5_DATA_PATH . '/' . ltrim($attachment['path'], '/');
}

function community_fetch_attachment($attachment_id)
{
    $table = community_attachment_table();

    return sql_fetch_prepared(
        " select * from {$table} where attachment_id = :attachment_id and status = 'active' ",
        array('attachment_id' => (int) $attachment_id)
    );
}

function community_fetch_post_attachments($post_id)
{
    $table = community_attachment_table();

    return sql_fetch_all_prepared(
        " select * from {$table}
          where post_id = :post_id and status = 'active'
          order by attachment_id asc ",
        array('post_id' => (int) $post_id)
    );
}

function community_count_post_attachments($post_id)
{
    $table = community_attachment_table();
    $row = sql_fetch_prepared(
        " select count(*) as cnt from {$table}
          where post_id = :post_id and status = 'active' ",
        array('post_id' => (int) $post_id)
    );

    return isset($row['cnt']) ? (int) $row['cnt'] : 0;
}

function community_update_post_attachment_count($post_id)
{
    $table = community_post_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set attachment_count = :attachment_count,
                 updated_at = :updated_at
           where post_id = :post_id ",
        array(
            'post_id' => (int) $post_id,
            'attachment_count' => community_count_post_attachments($post_id),
            'updated_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_normalize_upload_files(array $files)
{
    if (!isset($files['name'])) {
        return array();
    }

    $normalized = array();
    $is_multiple = is_array($files['name']);
    $count = $is_multiple ? count($files['name']) : 1;

    for ($i = 0; $i < $count; $i++) {
        $file = array(
            'name' => $is_multiple ? $files['name'][$i] : $files['name'],
            'type' => $is_multiple ? $files['type'][$i] : $files['type'],
            'tmp_name' => $is_multiple ? $files['tmp_name'][$i] : $files['tmp_name'],
            'error' => $is_multiple ? $files['error'][$i] : $files['error'],
            'size' => $is_multiple ? $files['size'][$i] : $files['size'],
        );

        if ((int) $file['error'] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $normalized[] = $file;
    }

    return $normalized;
}

function community_allowed_upload_extensions(array $board)
{
    $extensions = preg_split('/[|,]+/', (string) $board['upload_extensions']);
    $allowed = array();

    foreach ($extensions as $extension) {
        $extension = strtolower(trim($extension, " .\t\n\r\0\x0B"));
        if ($extension !== '') {
            $allowed[] = $extension;
        }
    }

    return $allowed;
}

function community_validate_upload_file(array $board, array $file)
{
    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        return '파일 업로드에 실패했습니다.';
    }

    if ((int) $board['upload_file_size'] > 0 && (int) $file['size'] > (int) $board['upload_file_size']) {
        return '첨부파일 크기가 허용 범위를 초과했습니다.';
    }

    $allowed = community_allowed_upload_extensions($board);
    if (!empty($allowed)) {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, $allowed, true)) {
            return '허용되지 않은 첨부파일 확장자입니다.';
        }
    }

    return '';
}

function community_validate_upload_files(array $board, array $files, $active_count = 0)
{
    if (empty($files)) {
        return '';
    }

    if ((int) $board['upload_file_count'] < 1) {
        return '첨부파일을 사용할 수 없는 게시판입니다.';
    }

    if ((int) $active_count + count($files) > (int) $board['upload_file_count']) {
        return '첨부파일 개수가 허용 범위를 초과했습니다.';
    }

    foreach ($files as $file) {
        $error = community_validate_upload_file($board, $file);
        if ($error !== '') {
            return $error;
        }
    }

    return '';
}

function community_store_uploaded_attachments($post_id, array $board, array $files)
{
    if ((int) $board['upload_file_count'] < 1 || empty($files)) {
        return array('error' => '', 'count' => 0);
    }

    $active_count = community_count_post_attachments($post_id);
    $validation_error = community_validate_upload_files($board, $files, $active_count);

    if ($validation_error !== '') {
        return array('error' => $validation_error, 'count' => 0);
    }

    $base_path = community_attachment_base_path();
    $relative_dir = community_attachment_relative_dir($post_id);
    $absolute_dir = G5_DATA_PATH . '/' . $relative_dir;

    if (!is_dir($base_path)) {
        @mkdir($base_path, G5_DIR_PERMISSION, true);
        @chmod($base_path, G5_DIR_PERMISSION);
    }

    if (!is_dir($absolute_dir)) {
        @mkdir($absolute_dir, G5_DIR_PERMISSION, true);
        @chmod($absolute_dir, G5_DIR_PERMISSION);
    }

    if (!is_dir($absolute_dir) || !is_writable($absolute_dir)) {
        return array('error' => '첨부파일 저장 디렉터리를 준비하지 못했습니다.', 'count' => 0);
    }

    $saved_count = 0;
    foreach ($files as $file) {
        $safe_name = get_safe_filename(basename($file['name']));
        $stored_name = replace_filename($safe_name);
        $relative_path = $relative_dir . '/' . $stored_name;
        $absolute_path = G5_DATA_PATH . '/' . $relative_path;

        if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $absolute_path)) {
            return array('error' => '첨부파일을 저장하지 못했습니다.', 'count' => $saved_count);
        }

        @chmod($absolute_path, G5_FILE_PERMISSION);

        community_insert_attachment(array(
            'post_id' => $post_id,
            'path' => $relative_path,
            'original_name' => $safe_name,
            'mime_type' => (string) $file['type'],
            'file_size' => (int) $file['size'],
        ));
        $saved_count++;
    }

    community_update_post_attachment_count($post_id);

    return array('error' => '', 'count' => $saved_count);
}

function community_insert_attachment(array $payload)
{
    $table = community_attachment_table();

    return (bool) sql_query_prepared(
        " insert into {$table}
            set post_id = :post_id,
                storage = 'local',
                path = :path,
                original_name = :original_name,
                mime_type = :mime_type,
                file_size = :file_size,
                status = 'active',
                created_at = :created_at ",
        array(
            'post_id' => (int) $payload['post_id'],
            'path' => $payload['path'],
            'original_name' => $payload['original_name'],
            'mime_type' => $payload['mime_type'],
            'file_size' => (int) $payload['file_size'],
            'created_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_delete_attachment_file(array $attachment)
{
    $absolute_path = community_attachment_absolute_path($attachment);

    if (!is_file($absolute_path)) {
        return true;
    }

    return @unlink($absolute_path);
}

function community_mark_attachment_deleted($attachment_id)
{
    $table = community_attachment_table();

    return (bool) sql_query_prepared(
        " update {$table}
             set status = 'deleted',
                 deleted_at = :deleted_at
           where attachment_id = :attachment_id and status = 'active' ",
        array(
            'attachment_id' => (int) $attachment_id,
            'deleted_at' => G5_TIME_YMDHIS,
        ),
        false
    );
}

function community_delete_attachment($post_id, $attachment_id)
{
    $attachment = community_fetch_attachment($attachment_id);
    if (empty($attachment['attachment_id']) || (int) $attachment['post_id'] !== (int) $post_id) {
        return array('error' => '존재하지 않는 첨부파일입니다.');
    }

    if (!community_delete_attachment_file($attachment)) {
        return array('error' => '첨부파일을 삭제하지 못했습니다.');
    }

    if (!community_mark_attachment_deleted($attachment['attachment_id'])) {
        return array('error' => '첨부파일 삭제 상태를 저장하지 못했습니다.');
    }

    community_update_post_attachment_count($post_id);

    return array('error' => '');
}

function community_delete_post_attachments($post_id)
{
    foreach (community_fetch_post_attachments($post_id) as $attachment) {
        $result = community_delete_attachment($post_id, $attachment['attachment_id']);
        if ($result['error'] !== '') {
            return $result;
        }
    }

    return array('error' => '');
}
