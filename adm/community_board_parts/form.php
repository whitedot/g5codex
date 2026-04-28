<?php
if (!defined('_GNUBOARD_')) {
    exit;
}
?>

<form method="post" action="<?php echo $community_board_form_view['form_action_attr']; ?>" class="admin-form-layout ui-form-theme">
    <input type="hidden" name="token" value="<?php echo $community_board_form_view['admin_token']; ?>">
    <input type="hidden" name="original_board_id" value="<?php echo $community_board_form_view['original_board_id_attr']; ?>">

    <section class="admin-form-section">
        <h2 class="h2_frm">기본 설정</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <tbody>
                <tr>
                    <th scope="row"><label for="board_id">게시판 ID</label></th>
                    <td><input type="text" name="board_id" value="<?php echo $community_board_form_view['board_id_value']; ?>" id="board_id" required class="frm_input required" maxlength="50"<?php echo $community_board_form_view['board_id_readonly_attr']; ?>></td>
                </tr>
                <tr>
                    <th scope="row"><label for="name">게시판 이름</label></th>
                    <td><input type="text" name="name" value="<?php echo $community_board_form_view['name_value']; ?>" id="name" required class="frm_input required" maxlength="255"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description">설명</label></th>
                    <td><textarea name="description" id="description" rows="4"><?php echo $community_board_form_view['description_value']; ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">상태</label></th>
                    <td>
                        <select name="status" id="status">
                            <?php foreach ($community_board_form_view['status_options'] as $option) { ?>
                                <option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="list_order">정렬</label></th>
                    <td><input type="number" name="list_order" value="<?php echo $community_board_form_view['list_order_value']; ?>" id="list_order" class="frm_input"></td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="h2_frm">권한과 기능</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <tbody>
                <tr>
                    <th scope="row">권한 레벨</th>
                    <td>
                        읽기 <select name="read_level"><?php foreach ($community_board_form_view['read_level_options'] as $option) { ?><option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option><?php } ?></select>
                        쓰기 <select name="write_level"><?php foreach ($community_board_form_view['write_level_options'] as $option) { ?><option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option><?php } ?></select>
                        댓글 <select name="comment_level"><?php foreach ($community_board_form_view['comment_level_options'] as $option) { ?><option value="<?php echo $option['value_attr']; ?>"<?php echo $option['selected_attr']; ?>><?php echo $option['label_text']; ?></option><?php } ?></select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">사용 기능</th>
                    <td>
                        <label><input type="checkbox" name="use_category" value="1"<?php echo $community_board_form_view['use_category_checked']; ?>> 카테고리</label>
                        <label><input type="checkbox" name="use_latest" value="1"<?php echo $community_board_form_view['use_latest_checked']; ?>> 최신글</label>
                        <label><input type="checkbox" name="use_comment" value="1"<?php echo $community_board_form_view['use_comment_checked']; ?>> 댓글</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">메일 알림</th>
                    <td>
                        <label><input type="checkbox" name="use_mail_post" value="1"<?php echo $community_board_form_view['use_mail_post_checked']; ?>> 게시물 작성자</label>
                        <label><input type="checkbox" name="use_mail_comment" value="1"<?php echo $community_board_form_view['use_mail_comment_checked']; ?>> 댓글 작성자</label>
                        <label><input type="checkbox" name="mail_admin" value="1"<?php echo $community_board_form_view['mail_admin_checked']; ?>> 관리자</label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="categories">카테고리</label></th>
                    <td>
                        <textarea name="categories" id="categories" rows="6" placeholder="한 줄에 하나씩 입력"><?php echo $community_board_form_view['categories_value']; ?></textarea>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-form-section">
        <h2 class="h2_frm">첨부와 포인트</h2>
        <div class="tbl_frm01 tbl_wrap">
            <table>
                <tbody>
                <tr>
                    <th scope="row">첨부 제한</th>
                    <td>
                        개수 <input type="number" name="upload_file_count" value="<?php echo $community_board_form_view['upload_file_count_value']; ?>" class="frm_input" min="0">
                        크기(byte) <input type="number" name="upload_file_size" value="<?php echo $community_board_form_view['upload_file_size_value']; ?>" class="frm_input" min="0">
                        확장자 <input type="text" name="upload_extensions" value="<?php echo $community_board_form_view['upload_extensions_value']; ?>" class="frm_input" placeholder="jpg|png|pdf">
                    </td>
                </tr>
                <tr>
                    <th scope="row">포인트</th>
                    <td>
                        <label><input type="checkbox" name="use_point" value="1"<?php echo $community_board_form_view['use_point_checked']; ?>> 사용</label>
                        글 <input type="number" name="point_write" value="<?php echo $community_board_form_view['point_write_value']; ?>" class="frm_input">
                        댓글 <input type="number" name="point_comment" value="<?php echo $community_board_form_view['point_comment_value']; ?>" class="frm_input">
                        읽기 <input type="number" name="point_read" value="<?php echo $community_board_form_view['point_read_value']; ?>" class="frm_input">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </section>

    <div class="btn_fixed_top">
        <a href="<?php echo $community_board_form_view['list_url_attr']; ?>" class="btn btn-surface-default-soft">목록</a>
        <button type="submit" class="btn btn-solid-primary">저장</button>
    </div>
</form>
