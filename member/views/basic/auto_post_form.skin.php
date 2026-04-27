<?php
// 자동 POST form skin이다. field escape와 target/action 값은 render-response.lib.php에서 준비한다.
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title><?php echo $title_text; ?></title>
</head>
<body>
<form name="fmemberautopost" method="post" action="<?php echo $action_attr; ?>">
<?php foreach ($fields as $field) { ?>
<input type="hidden" name="<?php echo $field['name_attr']; ?>" value="<?php echo $field['value_attr']; ?>">
<?php } ?>
</form>
<script>
<?php if ($message_json !== '') { ?>
alert(<?php echo $message_json; ?>);
<?php } ?>
document.fmemberautopost.submit();
</script>
</body>
</html>
