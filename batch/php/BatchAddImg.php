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
require_once ACW_APP_DIR . '/model/Don.php';

set_time_limit(0);

class BatchAddImg_model extends ACWModel {
        
    public static function init() {
        
    }
    
    public function main()
    {       
    	$argv =$_SERVER["argv"];
        $cvt = new ImgToPdf_lib();
        $condau_path =  ACW_ROOT_DIR.'/condau/';
        $pdf_file = $argv[1];
        $file = new File_lib();
        //$pattern ='/\\[D\d]{11}\\/';
        $info = explode("\\",$pdf_file);
        if(count($info) >=8){
            $don_id =str_replace("D","", str_replace("\\","",$info[6]));
            $file_name =$file->GetBaseName($info[7]).'%';
        }
        if(!$file->FileExists($pdf_file)){// khong ton tai
            $this->update_exist($don_id,$file_name,0);
        }else{ // da ton tai
            $db = new Don_model();            
            $data = $db->get_user_name_don($don_id);
            if(isset($data['usr_kt'])){
                $condau_path .=$data['usr_kt']."_".$data['usr_duyet'].".png";
            }
            $this->update_exist($don_id,$file_name,1);
            $cvt->addimg($condau_path,$argv[1]);
        }
    }
    public function update_exist($don_id,$file_name,$flg_exist)
    {
        $sql ="update file set file_exist = :file_exist
                where don_id = :don_id 
                and file_name like :file_name";
        $this->execute($sql,array('don_id'=>$don_id,'file_name'=>$file_name,'file_exist'=>$flg_exist));
    }
}

$batch = new BatchAddImg_model();
$batch->main();
