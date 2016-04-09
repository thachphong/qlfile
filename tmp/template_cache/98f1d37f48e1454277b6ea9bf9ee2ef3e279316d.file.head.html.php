<?php /* Smarty version Smarty-3.1.14, created on 2016-04-09 13:12:33
         compiled from "D:\xampp\htdocs\qlfile\app\template\include\head.html" */ ?>
<?php /*%%SmartyHeaderCode:55915708813191a3b6-13001539%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '98f1d37f48e1454277b6ea9bf9ee2ef3e279316d' => 
    array (
      0 => 'D:\\xampp\\htdocs\\qlfile\\app\\template\\include\\head.html',
      1 => 1459864589,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '55915708813191a3b6-13001539',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_5708813194d041_36648639',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5708813194d041_36648639')) {function content_5708813194d041_36648639($_smarty_tpl) {?><meta http-equiv="X-UA-Compatible" content="IE=100" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/reset.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/common.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/main_contents.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/side_contents.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/series.css" rel="stylesheet" type="text/css" />
<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
css/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-1.9.1.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/js/jquery.droppy.js"></script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/jquery-ui-1.10.3.custom.js"></script>
<script type='text/javascript'>
  $(function() {	
	$(".gnav_2,.gnav_3,.gnav_4,.gnav_5,.gnav_6,.gnav_7,.gnav_8, .gnav_9").click(function() {
		$(this).next(".sub").slideToggle("fast");
		$(this).toggleClass("active");
		return false;
	});
	$(".gnav_2,.gnav_3,.gnav_4,.gnav_5,.gnav_6,.gnav_7,.gnav_8, .gnav_9").mouseover(function() {
		$(".sub").hide();
	});
	$(":not([class='sub'],[class*='gnav_'])").click(function() {
		$(".sub").hide();
	});
    	
	$.fn.enterKey = function (fnc) {
	    return this.each(function () {
	        $(this).keypress(function (ev) {
	            var keycode = (ev.keyCode ? ev.keyCode : ev.which);
	            if (keycode == '13') {
	                fnc.call(this, ev);
	            }
	        })
	    })
	}
    
  });
  function htmlEncode(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function htmlDecode(value){
        return String(value)
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'")
            .replace(/&lt;/g, '<')
            .replace(/&gt;/g, '>')
            .replace(/&amp;/g, '&');
    }	
</script>
<script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
js/mustache.js"></script>
<?php }} ?>