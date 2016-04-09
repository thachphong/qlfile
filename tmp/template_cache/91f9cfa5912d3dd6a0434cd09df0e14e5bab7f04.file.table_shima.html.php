<?php /* Smarty version Smarty-3.1.14, created on 2016-04-09 13:12:33
         compiled from "D:\xampp\htdocs\qlfile\app\template\include\table_shima.html" */ ?>
<?php /*%%SmartyHeaderCode:1445157088131ba2b57-05630961%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '91f9cfa5912d3dd6a0434cd09df0e14e5bab7f04' => 
    array (
      0 => 'D:\\xampp\\htdocs\\qlfile\\app\\template\\include\\table_shima.html',
      1 => 1459864589,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1445157088131ba2b57-05630961',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_57088131ba69d0_93224154',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57088131ba69d0_93224154')) {function content_57088131ba69d0_93224154($_smarty_tpl) {?><script type="text/javascript">
	// テーブル背景緑
	var table_shimashima = function (table_selecter) {
		$(table_selecter).find("tbody").find("tr").css("background-color", "");
		$(table_selecter).find("tbody").find("tr:odd").css("background-color", "#ecf8eb");
	};
</script>
<?php }} ?>