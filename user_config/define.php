<?php
define('AKAGANE_COPYRIGHT', 'Copyright (C) 2016');

define('AKAGANE_HEADER_EQUAL_2', ' ↑2');
define('AKAGANE_HEADER_EQUAL_3', ' ↑3');
define('AKAGANE_HEADER_EQUAL_4', ' ↑4');
define('AKAGANE_HEADER_EQUAL_5', ' ↑5');

define('AKAGANE_TITLE', 'HỆ THỐNG XÉT DUYỆT BẢN VẼ'); 


define('UNFOLD_TEXT', 'Mở rộng');
define('FOLD_TEXT', 'Thu nhỏ');

define('AKAGANE_STRAGE_PATH', ACW_ROOT_DIR . '/data');
define('DATA_TMP_PATH', AKAGANE_STRAGE_PATH . '/tmp');	
define('DATA_MAIN_PATH', AKAGANE_STRAGE_PATH . '/main');	

define('IMG_CON_DAU_WIDTH', 30);
define('IMG_CON_DAU_HEIGHT', 30);
define('IMG_CON_DAU_PADDING_BOTTOM', 10);
define('IMG_CON_DAU_PADDING_RIGHT', 2);

define('DON_STATUS_NEW', '0'); // TAO MOI CHUA KIEM TRA
define('DON_STATUS_KT', '1'); // DA KIEM TRA
define('DON_STATUS_DUYET', '2'); // DA DUYET
define('DON_STATUS_TTQL', '3'); // TRUNG TAM QUAN LY DA DUYET
define('DON_STATUS_TRA_KT', '4'); // TRA KIEM TRA
define('DON_STATUS_TRA_DUYET', '5'); // TRA DUYET
define('DON_STATUS_TRA_TTQL', '6'); // TRUNG TAM QUAN LY TRA DUYET
define('DON_STATUS_XIN_CN', '7'); // xin cập nhật
define('DON_STATUS_TRA_CN', '8'); //TRA xin cập nhật
define('DON_STATUS_DUYET_CN', '9'); //ttql da chap nhan cho cap nhat


define('AKAGANE_TOP_COMMENT_MAX', 50);	// 
define('AKAGANE_STRING_CUT_SUFFIX', '...');	//


define('AKAGANE_ICON_PATH', AKAGANE_STRAGE_PATH . 'icon');
define('AKAGANE_ICON_TMP_PATH', ACW_TMP_DIR . '/icon');	// 
define('AKAGANE_ICON_THUMB_WIDTH', 160);	
define('AKAGANE_ICON_THUMB_HEIGHT', 160);	
define('AKAGANE_ICON_THUMB_EXT', 'jpg');	


define('AKAGANE_HEADER_COLOR', 'EEEEEE');	// 


define('AKAGANE_EXPORT_PROCESSES_COUNT', 4);
define('AKAGANE_IMPORT_PROCESSES_COUNT', 4);



define('THUMBNAIL_IMAGE_WIDTH',     760); // 
define('THUMBNAIL_IMAGE_HEIGHT',    760); // 
define('THUMBNAIL_IMAGE_FORMATT', 'jpg'); //


define('AKAGANE_WEB_PROCESSES_COUNT', 4);


define('AKAGANE_SERIES_EDIT_TIME_LIMIT', 1800);


define('AKAGANE_HELP_STRAGE_PATH', AKAGANE_STRAGE_PATH . 'help');



define('BATCH_TRY_AGAIN_IF_CONFLICT_TIME_OUT', 500); //Unit is Seconds



error_reporting(E_ALL);

