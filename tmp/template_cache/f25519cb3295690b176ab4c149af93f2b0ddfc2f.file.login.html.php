<?php /* Smarty version Smarty-3.1.14, created on 2016-04-09 13:12:28
         compiled from "D:\xampp\htdocs\qlfile\app\template\login.html" */ ?>
<?php /*%%SmartyHeaderCode:94675708812c3d94a0-73183932%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'f25519cb3295690b176ab4c149af93f2b0ddfc2f' => 
    array (
      0 => 'D:\\xampp\\htdocs\\qlfile\\app\\template\\login.html',
      1 => 1459864593,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '94675708812c3d94a0-73183932',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user_id' => 0,
    'acw_error' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5708812c452649_56405282',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5708812c452649_56405282')) {function content_5708812c452649_56405282($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Đăng nhập</title>
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/login.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="main_contents">
	<div class="login_wrap">
        
		<form action="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
login/auth" name="login_form" method="POST" id="login_form">
		<table>
			<tr><td colspan="3"><h1><?php echo htmlspecialchars(@constant('AKAGANE_TITLE'), ENT_QUOTES, 'UTF-8');?>
</h1></td></tr>
			<tr><td colspan="3"></td></tr>
            <tr><td colspan="3"></td></tr>
			<tr><th></th><th>TÊN ĐĂNG NHẬP</th><td><input type="text" size="20" name="user_id" id="user_id" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user_id']->value, ENT_QUOTES, 'UTF-8');?>
"/></td></tr>
			<tr><th></th><th>MẬT KHẨU</th><td><input type="password" size="20" name="passwd" id="passwd" /></td></tr>
            <tr><td colspan="3"></td></tr>
			<tr>
                <th></th>
				<td colspan="2" style="text-align: right !important">
					<a href="javascript:void(0);" style="float:right" class="btn img_opacity" id="btn_login">Đăng nhập</a>
                    <a href="javascript:void(0);" style="float:right" class="btn img_opacity" id="btn_clear">Xóa</a>
				</td>
			</tr>
			<tr>
                <th></th>
                <td colspan="2"><span id="msg"><?php if (count($_smarty_tpl->tpl_vars['acw_error']->value)>0){?><?php if (isset($_smarty_tpl->tpl_vars['acw_error']->value['ip'])){?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['acw_error']->value['ip'], ENT_QUOTES, 'UTF-8');?>
<?php }else{ ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['acw_error']->value['message'], ENT_QUOTES, 'UTF-8');?>
<?php }?><?php }?></span></td>
            </tr>
		</table>
		<input type="submit" style="position: absolute; left: -9999px; width: 1px; height: 1px;" tabindex="-1"/>
		</form>
		<p></p>
	</div>
</div><!-- / .main_contents -->
<script src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-1.9.1.js"></script>
<script type="text/javascript">
$(function() {
	// クリアボタン
	$('#btn_clear').click(function () {
		$('#user_id').val('');
		$('#passwd').val('');
		$('#msg').text('');
	});
	// ログインボタン
	$('#btn_login').click(function () {
		$('#login_form').submit();
	});
	$('#user_id').focus();
	var elements = "input";
	$("#user_id").on("keypress", function(e) {
		if (e.keyCode == 13) { 
			$("#passwd").focus();
			e.preventDefault();
		}
	});
	$("#passwd").on("keypress", function(e) {
		if (e.keyCode == 13) { 
			$("#btn_login").click();
			e.preventDefault();
		}
	});
});
</script>
</body>
</html><?php }} ?>