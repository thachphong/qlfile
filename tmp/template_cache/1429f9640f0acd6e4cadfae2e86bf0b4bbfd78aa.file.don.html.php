<?php /* Smarty version Smarty-3.1.14, created on 2016-04-09 13:12:33
         compiled from "D:\xampp\htdocs\qlfile\app\template\don.html" */ ?>
<?php /*%%SmartyHeaderCode:8534570881317e1b78-09097730%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1429f9640f0acd6e4cadfae2e86bf0b4bbfd78aa' => 
    array (
      0 => 'D:\\xampp\\htdocs\\qlfile\\app\\template\\don.html',
      1 => 1460128419,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8534570881317e1b78-09097730',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'mode' => 0,
    'search_data' => 0,
    'data_rows' => 0,
    'key' => 0,
    'row' => 0,
    'menu' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.14',
  'unifunc' => 'content_570881318f7137_82760384',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_570881318f7137_82760384')) {function content_570881318f7137_82760384($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/base.dwt" codeOutsideHTMLIsLocked="false" --><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lịch sử xét duyệt</title>
		<?php echo $_smarty_tpl->getSubTemplate ('include/head.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

		<link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/baitai.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/css/jquery.datetimepicker.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
shared/js/jquery.datetimepicker.full.min.js"></script>
</head>
<body class="column_1">
<div class="bg">
<div class="container">
		<?php echo $_smarty_tpl->getSubTemplate ('include/menu.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<div class="contents_wrap"> 
<!-- InstanceBeginEditable name="top_contents" -->
    <?php if ($_smarty_tpl->tpl_vars['mode']->value!="main"){?>
	<div class="search_wrap">
		<form id="search_form" action="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/history" method="POST">
        <input type="hidden" name="default_flg" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['default_flg'], ENT_QUOTES, 'UTF-8');?>
"/>
		<table class="kensaku">
			<colgroup>
				<col width="85px" />
				<col width="200px" /> 
			</colgroup>
			<tr>
				<!--<th>Điều kiện</th>
                <td class="first">
                Loại bản vẽ 
                    <select name="loaidon">
                        <option ></option>
                        <option value="0" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['loaidon']=='0'){?>selected<?php }?> >Tạo mới</option>
                        <option value="1" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['loaidon']=='1'){?>selected<?php }?>>Cập nhật</option>
                    </select>
                </td>
                <td >Từ thời điểm <input type="text"  name="tu_ngay" class="datetimepicker" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['tu_ngay'], ENT_QUOTES, 'UTF-8');?>
" />
                </td>
                <td>đến<input type="text"  name="den_ngay" class="datetimepicker" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['den_ngay'], ENT_QUOTES, 'UTF-8');?>
" /></td>
                <td>Mã số duyệt <input maxlength="20" type="text" name="maso_duyet" id="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['maso_duyet'], ENT_QUOTES, 'UTF-8');?>
" />
                </td>
                <td>Tình trạng 
                    <select name="trangthai">
                        <option ></option>
                        <option value="0" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='0'){?>selected<?php }?>>Đang Chờ</option>
                        <option value="1" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='1'){?>selected<?php }?>>Đã duyệt</option>
                        <option value="2" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='2'){?>selected<?php }?>>Không duyệt</option>
                        <option value="3" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='2'){?>selected<?php }?>>Xin mượn bản vẽ</option>
                        <option value="4" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='4'){?>selected<?php }?>>Trả yêu xin mượn bản vẽ</option>
                        <option value="5" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='5'){?>selected<?php }?>>Đã duyệt xin mượn bản vẽ</option>
                        
                    </select>
                </td>                
                <td class="al_l"><button class="button_blue" type="button" id="btn_search">Tìm</button> <button type="button" id="btn_clear" class="button_blue">Xóa</button></td>-->
                <th>Điều kiện</th>
                <td class="first" style="padding: 0px 40px;">
                    <table class="table_search">
                    <tr>
                        <td >Loại bản vẽ 
                        <select name="loaidon">
                            <option ></option>
                            <option value="0" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['loaidon']=='0'){?>selected<?php }?> >Tạo mới</option>
                            <option value="1" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['loaidon']=='1'){?>selected<?php }?>>Cập nhật</option>
                        </select>
                        </td>
                        <td >Từ thời điểm <input type="text"  name="tu_ngay" class="datetimepicker" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['tu_ngay'], ENT_QUOTES, 'UTF-8');?>
" />
                </td>
                <td>đến<input type="text"  name="den_ngay" class="datetimepicker" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['den_ngay'], ENT_QUOTES, 'UTF-8');?>
" /></td>
                <!--<td>Mã số duyệt <input maxlength="20" type="text" name="maso_duyet" id="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['maso_duyet'], ENT_QUOTES, 'UTF-8');?>
" />-->
                </td>
                <td>Tình trạng 
                    <select name="trangthai">
                        <option ></option>
                        <option value="0" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='0'){?>selected<?php }?>>Đang Chờ</option>
                        <option value="1" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='1'){?>selected<?php }?>>Đã duyệt</option>
                        <option value="2" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='2'){?>selected<?php }?>>Không duyệt</option>
                        <option value="3" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='2'){?>selected<?php }?>>Xin mượn bản vẽ</option>
                        <option value="4" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='4'){?>selected<?php }?>>Trả yêu xin mượn bản vẽ</option>
                        <option value="5" <?php if ($_smarty_tpl->tpl_vars['search_data']->value['trangthai']=='5'){?>selected<?php }?>>Đã duyệt xin mượn bản vẽ</option>
                        
                    </select>
                </td>                 
                <td class="al_l"><button class="button_blue" type="button" id="btn_search">Tìm</button> <button type="button" id="btn_clear" class="button_blue">Xóa</button></td>
                    </tr>
                    <tr>
                        <td>Mã bản vẽ tổng <input maxlength="20" type="text" name="don_no" id="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['don_no'], ENT_QUOTES, 'UTF-8');?>
" />                        
                        <td>Mã số duyệt <input maxlength="20" type="text" name="maso_duyet" id="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['maso_duyet'], ENT_QUOTES, 'UTF-8');?>
" />
                        <td>Tên file <input maxlength="20" type="text" name="file_name" id="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['search_data']->value['file_name'], ENT_QUOTES, 'UTF-8');?>
" />
                    </tr>
                    </table>
                </td>
            </tr>            
		</table>
		</form>
	</div>
    <?php }?>
	<!--<ul class="topic_path">
		<li><h2>Quản lý user</h2></li>
	</ul>-->
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="side_contents" -->
<!-- InstanceEndEditable -->
<!-- InstanceBeginEditable name="main_contents" -->
<div class="main_contents">
<div class="main_innner height2">
	<div class="baitai_wrap">
		<!--<p class="clear_fix" style="text-align: right;"><input type="button" value="Thêm mới" id="btn_new" /></p>-->
		<table id="data_list">
			<thead>
			<tr>
				<th style="width: 40px;">No.</th>               
                <th style="width: 60px;">Mã bản vẻ</th>
                <th style="width: 200px;">Tiêu đề</th>
                <th style="width: 70px;">Ngày lập</th>
                <th style="width: 100px;">Người tạo</th>
                <th style="width: 100px;">Người duyệt cuối</th>
                <th style="width: 120px; text-align: center;">tình trạng</th>
                <th style="width: 200px;">Nội dung</th>
			</tr>
			</thead>
			<tbody>
<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['data_rows']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value){
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['row']->key;
?>
			<tr>
                <td style="text-align: center;">
					<?php echo htmlspecialchars(($_smarty_tpl->tpl_vars['key']->value+1), ENT_QUOTES, 'UTF-8');?>

				</td>				
				<td style="text-align: center;">
					<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['don_no'], ENT_QUOTES, 'UTF-8');?>

				</td>
				<td >
                    <input type="hidden" class="m_user_id" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['don_id'], ENT_QUOTES, 'UTF-8');?>
" />
					<a href="<?php echo htmlspecialchars(@constant('ACW_BASE_URL'), ENT_QUOTES, 'UTF-8');?>
don/edit/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['don_id'], ENT_QUOTES, 'UTF-8');?>
" class="btn_edit"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['tieude'], ENT_QUOTES, 'UTF-8');?>
</a>
				</td>
                <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['add_datetime'], ENT_QUOTES, 'UTF-8');?>
 </td>
                <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['add_user'], ENT_QUOTES, 'UTF-8');?>
</td>
                <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['user_name_disp'], ENT_QUOTES, 'UTF-8');?>
</td>
                <td>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['status_name'], ENT_QUOTES, 'UTF-8');?>

                </td>
                <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['row']->value['noidung'], ENT_QUOTES, 'UTF-8');?>
</td>
			</tr>
<?php } ?>
			</tbody>
		</table>
	</div><!-- / .top_wrap -->
</div><!-- / .main_innner -->
</div><!-- / .main_contents -->
<!-- InstanceEndEditable -->
<?php echo $_smarty_tpl->getSubTemplate ('include/footer.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

</div><!-- / .contents_wrap -->
</div><!-- / .container -->
</div><!-- / .bg -->
<?php echo $_smarty_tpl->getSubTemplate ('include/argo_ajax.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<?php echo $_smarty_tpl->getSubTemplate ('include/table_shima.html', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

<script type='text/javascript'>
$(function() {
    //jQuery('#datetimepicker').datetimepicker();
    //jQuery.datetimepicker.setLocale('de');
    /*jQuery('#datetimepicker').datetimepicker({
     i18n:{
      de:{
       months:[
        'Januar','Februar','März','April',
        'Mai','Juni','Juli','August',
        'September','Oktober','November','Dezember',
       ],
       dayOfWeek:["CN", "T2", "T3", "T4", "T5", "T6", "T7"    ]
      }
     },
     timepicker:true,
     format:'d/m/Y H:i'
    });*/
    console.log('<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['menu']->value, ENT_QUOTES, 'UTF-8');?>
');
    $('<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['menu']->value, ENT_QUOTES, 'UTF-8');?>
').addClass('selected');
    $('.datetimepicker').datetimepicker({
          //format:'Y/m/d H:i',
          format:'d/m/Y',
          inline:false,
          timepicker:false,
          lang:'ru'
    });
	// ダイアログ準備
	$("#edit_dialog").dialog({
		autoOpen: false,
		width: 1000,
		/*height: $(window).height() - 100,*/
		modal: true,
		title: 'Thông đơn vị'
	});
	
        //Add Start - Trung VNIT - 2014/08/21
        var search_user_ajax = new Argo_ajax('json');
        search_user_ajax.done_func = function(data) {
            if (data.error) {
                if (data.error.length > 0) {
                    $('#data_list tbody').empty();
                   Argo_ajax.message_box_error('', data.error[0]['info']);
                }else{
                    $('#search_form').submit();
                }
            }
        };	
        
	// 検索ボタン
	$('#btn_search').click(function(){        
            $arr = $('#search_form').serializeArray();
            search_user_ajax.connect('POST', 'donvi/checkmaxlenght', $arr);
          
	});
            
    $("#search_donvi_name").keydown(function (e) {
        if (e.keyCode == 13) {
                $('#btn_search').click();
        }
    });
 
	
	$('#btn_clear').click(function(){
		$('#search_form').find("textarea, :text, select").val("");
	});
	
	$(document).on("click", "#btn_new", function () {
		open_user_edit(null);
	});

	$(document).on("click", ".btn_edit", function () {		
		var donvi_id = $(this).prev().val();
		open_user_edit(donvi_id);
	});
	table_shimashima('#data_list');
	
	$(window).on("load resize", function(){
		var wh = $(window).height();
		$(".height2").height(wh - 206);	
	});
});

function open_user_edit(donvi_id)
{
	var html_ajax = new Argo_ajax("html");
	html_ajax.done_func = function(html) {
		$("#edit_dialog").html(html);
		$("#edit_dialog").dialog("open");
	};
	html_ajax.connect("POST", "donvi/edit", {
		"donvi_id": donvi_id
	});
}


</script>
<div id="edit_dialog">
	
</div>
</body>
<!-- InstanceEnd --></html><?php }} ?>