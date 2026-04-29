<?php
if (!defined('_GNUBOARD_')) {
    exit;
}

function community_member_table()
{
    global $g5;

    return $g5['member_table'];
}

function community_fetch_member($mb_id)
{
    $table = community_member_table();

    return sql_fetch_prepared(
        " select mb_id, mb_nick, mb_name, mb_email from {$table} where mb_id = :mb_id ",
        array('mb_id' => $mb_id)
    );
}

function community_fetch_comment_author_members($post_id, $exclude_mb_id)
{
    $comment_table = community_comment_table();
    $member_table = community_member_table();

    return sql_fetch_all_prepared(
        " select distinct m.mb_id, m.mb_nick, m.mb_name, m.mb_email
            from {$comment_table} c
           join {$member_table} m on m.mb_id = c.mb_id
           where c.post_id = :post_id
             and c.status = 'published'
             and c.mb_id <> :exclude_mb_id ",
        array(
            'post_id' => (int) $post_id,
            'exclude_mb_id' => $exclude_mb_id,
        )
    );
}

function community_add_notification_recipient(array &$recipients, array $recipient)
{
    $mb_id = isset($recipient['mb_id']) ? (string) $recipient['mb_id'] : '';
    $email = isset($recipient['mb_email']) ? (string) $recipient['mb_email'] : '';
    $key = $mb_id !== '' ? 'member:' . $mb_id : 'email:' . strtolower($email);

    if ($key === 'email:') {
        $key = 'empty:' . count($recipients);
    }

    $recipients[$key] = $recipient;
}

function community_build_admin_notification_recipient()
{
    global $config;

    return array(
        'mb_id' => 'admin',
        'mb_nick' => isset($config['cf_admin_email_name']) ? $config['cf_admin_email_name'] : '관리자',
        'mb_name' => isset($config['cf_admin_email_name']) ? $config['cf_admin_email_name'] : '관리자',
        'mb_email' => isset($config['cf_admin_email']) ? $config['cf_admin_email'] : '',
    );
}

function community_send_notification_mail($event_type, array $target, array $recipient, $subject, $content)
{
    global $config;

    if (empty($recipient['mb_email'])) {
        return false;
    }

    if (empty($config['cf_email_use'])) {
        return false;
    }

    include_once G5_LIB_PATH . '/support/mail.lib.php';

    $sent = mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $recipient['mb_email'], $subject, $content, 1);

    return $sent;
}

function community_render_notification_template($template, array $vars)
{
    $template_path = G5_COMMUNITY_VIEW_PATH . '/mail/' . $template . '.mail.php';

    if (!is_file($template_path)) {
        return '';
    }

    extract($vars, EXTR_SKIP);
    ob_start();
    include $template_path;
    return ob_get_clean();
}

function community_build_post_notification_content(array $board, array $post)
{
    $content = community_render_notification_template('post_created', array(
        'board' => $board,
        'post' => $post,
    ));

    if ($content !== '') {
        return $content;
    }

    $post_url = G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($board['board_id']) . '&post_id=' . (int) $post['post_id'];

    return '<div>'
        . '<h1>' . get_text($board['name']) . ' 새 게시글 알림</h1>'
        . '<p><strong>' . get_text($post['title']) . '</strong> 글이 등록되었습니다.</p>'
        . '<p>' . nl2br(get_text(cut_str($post['content'], 300))) . '</p>'
        . '<p><a href="' . community_escape_attr($post_url) . '" target="_blank">게시글 보기</a></p>'
        . '</div>';
}

function community_build_comment_notification_content(array $board, array $post, array $comment)
{
    $content = community_render_notification_template('comment_created', array(
        'board' => $board,
        'post' => $post,
        'comment' => $comment,
    ));

    if ($content !== '') {
        return $content;
    }

    $post_url = G5_COMMUNITY_URL . '/view.php?board_id=' . rawurlencode($board['board_id']) . '&post_id=' . (int) $post['post_id'];

    return '<div>'
        . '<h1>' . get_text($board['name']) . ' 댓글 알림</h1>'
        . '<p><strong>' . get_text($post['title']) . '</strong> 글에 새 댓글이 등록되었습니다.</p>'
        . '<p>' . nl2br(get_text(cut_str($comment['content'], 300))) . '</p>'
        . '<p><a href="' . community_escape_attr($post_url) . '" target="_blank">게시글 보기</a></p>'
        . '</div>';
}

function community_notify_post_created(array $board, array $post)
{
    if (empty($post['post_id']) || empty($board['mail_admin'])) {
        return;
    }

    $recipient = community_build_admin_notification_recipient();
    $subject = '[' . $board['name'] . '] 새 게시글이 등록되었습니다.';
    $content = community_build_post_notification_content($board, $post);
    $target = array(
        'post_id' => (int) $post['post_id'],
        'comment_id' => 0,
    );

    community_send_notification_mail('post_created', $target, $recipient, $subject, $content);
}

function community_notify_comment_created(array $board, array $post, array $comment, $actor_mb_id)
{
    if (empty($comment['comment_id'])) {
        return;
    }

    $recipients = array();

    if (!empty($board['use_mail_post']) && $post['mb_id'] !== '' && $post['mb_id'] !== $actor_mb_id) {
        $post_author = community_fetch_member($post['mb_id']);
        if (!empty($post_author['mb_id'])) {
            community_add_notification_recipient($recipients, $post_author);
        }
    }

    if (!empty($board['use_mail_comment'])) {
        foreach (community_fetch_comment_author_members($post['post_id'], $actor_mb_id) as $comment_author) {
            community_add_notification_recipient($recipients, $comment_author);
        }
    }

    if (!empty($board['mail_admin'])) {
        community_add_notification_recipient($recipients, community_build_admin_notification_recipient());
    }

    if (empty($recipients)) {
        return;
    }

    $subject = '[' . $board['name'] . '] 새 댓글이 등록되었습니다.';
    $content = community_build_comment_notification_content($board, $post, $comment);
    $target = array(
        'post_id' => (int) $post['post_id'],
        'comment_id' => (int) $comment['comment_id'],
    );

    foreach ($recipients as $recipient) {
        community_send_notification_mail('comment_created', $target, $recipient, $subject, $content);
    }
}
