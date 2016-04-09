<?php /* Smarty version Smarty-3.1.14, created on 2016-04-09 13:12:33
         compiled from "D:\xampp\htdocs\qlfile\app\template\include\menu.html" */ ?>
<?php /*%%SmartyHeaderCode:3245257088131964752-78732046%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e033ce7d7318fa3aaf1af1b0ea3802589054f3a' => 
    array (
      0 => 'D:\\xampp\\htdocs\\qlfile\\app\\template\\include\\menu.html',
      1 => 1460122979,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3245257088131964752-78732046',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'user_info' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_57088131a52c06_11059446',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57088131a52c06_11059446')) {function content_57088131a52c06_11059446($_smarty_tpl) {?><div class="header clear_fix">
    
	<p><img src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/img/logo_small.jpg" style="height: 40px;margin-top: 5px;">
    <?php echo htmlspecialchars(@constant('AKAGANE_TITLE'), ENT_QUOTES, 'UTF-8');?>
</p>
	<ul class="subnav">
		<li><a href="#">Help</a></li>
		<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
login/delete">Thoát</a></li>
		<li>Xin chào　<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user_info']->value['user_name'], ENT_QUOTES, 'UTF-8');?>
</li>
	</ul>
	<ul class="gnav">
		<!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
category" class="gnav_1">Folder</a></li>-->
        <!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mscolor" class="gnav_1">mscolor</a></li>-->
        <?php if ($_smarty_tpl->tpl_vars['user_info']->value['upload']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['kiemtra']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['duyet']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['trungtam_quanly']=="1"){?>
        <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don" id="menu_kt" class="gnav_1">Kiểm tra yêu cầu XD</a></li>
        <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/edit" id="menu_lapyc" class="gnav_1">Lập yêu cầu XD</a></li>
        <li><a href="javascript:void(0);" id="menu_lsyc" class="gnav_2">Lịch sử yêu cầu XD</a>
            <ul class="sub">
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/history" >Lịch sử yêu cầu XD</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
file/mtblank" >Lịch sử mượn trả file</a></li>
                <?php if ($_smarty_tpl->tpl_vars['user_info']->value['trungtam_quanly']=="1"){?>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
file/cublank" >Lịch sử file cũ</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
file/existfile" >Convert lại pdf</a></li>
                <?php }?>
            </ul>
        </li>
         <?php if ($_smarty_tpl->tpl_vars['user_info']->value['upload']=="1"){?>  
        <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/muonblank" id="menu_muon" class="gnav_1">Mượn bản vẽ</a></li>
        <?php }?>
        <!--<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/muon" class="gnav_1">Đưa Bản vẽ vào máy</a></li>-->
        <?php }?> 
        <?php if ($_smarty_tpl->tpl_vars['user_info']->value['trungtam_quanly']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['duyet']=="1"){?>
        <li><a href="javascript:void(0);" id="menu_phanbo" class="gnav_2">Phân bổ file</a>
            <ul class="sub">
                <?php if ($_smarty_tpl->tpl_vars['user_info']->value['trungtam_quanly']=="1"){?>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
folderfile" >Phân bổ file</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
folderfile/history" >Lịch sử phân bổ file</a></li>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['user_info']->value['admin']=="1"){?>
        <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
banve" id="menu_banve" class="gnav_1">Danh mục bản vẽ</a></li>
                <?php }?>               
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
may" >Danh sách máy</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
mayfile" >Đưa Bản vẽ vào máy</a></li>
            </ul>
        </li>        
        <?php }?>
        <?php if (($_smarty_tpl->tpl_vars['user_info']->value['upload']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['kiemtra']=="1")&&$_smarty_tpl->tpl_vars['user_info']->value['admin']!="1"){?>
        <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
banve" id="menu_banve" class="gnav_1">Danh mục bản vẽ</a></li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['user_info']->value['print']=="1"){?>
        <li><a href="javascript:void(0);" id="menu_in" class="gnav_2">IN File</a>
            <ul class="sub">
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
file/blank" >In File</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
print" >Lịch sử In file</a></li>
            </ul>
        </li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['user_info']->value['print']=="1"||$_smarty_tpl->tpl_vars['user_info']->value['trungtam_quanly']=="1"){?>
        <li><a href="javascript:void(0);" id="menu_capphat" class="gnav_2">Cấp phát file</a>
            <ul class="sub">
                <?php if ($_smarty_tpl->tpl_vars['user_info']->value['print']=="1"){?>
                    <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
file/capphat" >Cấp phát file</a></li>
                <?php }?>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
capphat/history" >Lịch sử Cấp phát file</a></li>
            </ul>
        </li>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['user_info']->value['admin']=="1"){?>
		<li><a href="javascript:void(0);" id="menu_admin" class="gnav_2">Admin</a>
			<ul class="sub">				
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
donvi">Đơn vị</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
phongban">Phòng ban</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
tonhom">Tổ nhóm</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
user">User</a></li>
                <li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
category">Folder</a></li>
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
foldernhom">Phân quyền</a></li>						
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
download">Lịch sử download</a></li>								
				<li><a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/xoadon">Xóa Đơn</a></li>							
			</ul>
        </li>
		<?php }?>
	</ul>
</div><!-- / .header -->
<?php }} ?>