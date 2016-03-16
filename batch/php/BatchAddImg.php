<?php

/**
 * batch import item
 * @author VNIT
 * @since 2014/12/23
 */
$_SERVER["REMOTE_ADDR"] = "batch in process...";

define('ACW_PUBLIC_DIR', str_replace("\\", '/', dirname(dirname(__DIR__))));

define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);

define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');	// ルートディレクトリ
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');

/**
* 一時ディレクトリ
*/
define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');

// プロジェクトの初期化処理
require ACW_USER_CONFIG_DIR . '/initialize.php';

require_once ACW_APP_DIR . '/lib/Validate.php';
require_once ACW_APP_DIR . '/lib/Excel.php';
require_once ACW_APP_DIR . '/lib/Path.php';


require_once ACW_APP_DIR . '/lib/FileWindows.php';
require_once ACW_APP_DIR . '/lib/File.php';
require_once ACW_APP_DIR . '/lib/Image.php';
require_once ACW_APP_DIR . '/lib/ImgToPdf.php';


set_time_limit(0);

class BatchAddImg_model extends ACWModel {
        
    public static function init() {
        
    }
    
    public function main()
    {       
    	$argv =$_SERVER["argv"];
        $cvt = new ImgToPdf_lib();
        $condau_path =  ACW_ROOT_DIR.'/shared/img/condau.jpg';
        $cvt->addimg($condau_path,$argv[1]);
    }
}

$batch = new BatchAddImg_model();
$batch->main();
