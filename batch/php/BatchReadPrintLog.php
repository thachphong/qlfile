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
require_once ACW_APP_DIR . '/lib/ImportExport.php'; 

require_once ACW_APP_DIR . '/lib/FileWindows.php';
require_once ACW_APP_DIR . '/lib/File.php';
require_once ACW_APP_DIR . '/lib/Image.php';

require_once ACW_APP_DIR . '/lib/Image.php';


set_time_limit(0);

class BatchReadPrintLog_model extends ACWModel {
    
    /**
     * 共通初期化
     */
    public static function init() {
        
    }
    
    public function main()
    {
    	ACWLog::debug_var('DEBUG_ReadPrintLog', "Go to main function!");
        try {
            $file = new File_lib();
            $filelist = $file->FileList(FOLDER_PRINT_LOG);
            foreach($filelist as $item){
                if($this->check_log_name($item)){
                    $data_csv = $this->Read_CSV(FOLDER_PRINT_LOG.'/'.$item);
                    $this->insert_database($data_csv,$item);
                }
                //ACWLog::debug_var('DEBUG_ReadPrintLog', $data_csv);
            }
            return ACWView::OK;
        } catch (Exception $e) {
            ACWLog::debug_var('DEBUG_ReadPrintLog', 'ERROR : '.$e->getMessage()); 
        }

    }
    
    public function Read_CSV($file_name){
        $row = 1;
  
        $pdf_data= array();
        $file = new File_lib();
        if (($handle = fopen($file_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle,1000,"\t")) !== FALSE) {
                $num = count($data);                
                if($row > 1){
                    if($file->GetExtensionName($data[6])=="pdf" && $data[7] =='Success'){
                        $arr_new = array();
                        $arr_new['user_name'] = $data[0];
                        $arr_new['file_name'] = $data[6];
                        $arr_new['soluong'] = $data[9];
                        $arr_new['ngay_in'] = $data[4];
                        $pdf_data[] = $arr_new;
                    }
                }
                $row++;
            }
            fclose($handle);
        }
        return $pdf_data;
    }
    public function insert_database($data,$log_name)
    {
        $this->begin_transaction();
        $sql="insert into lichsu_in 
                (user_name
                ,file_name
                ,soluong
                ,ngay_in
                ,add_datetime
                ,log_name)
                values(
                :user_name
                ,:file_name
                ,:soluong
                ,STR_TO_DATE(:ngay_in,'%c/%e/%Y %l:%i:%s %p')
                ,NOW()
                ,:log_name) ";
        foreach($data as $row){
            $row['log_name']=$log_name;
            $this->execute($sql,$row);
        }  
        $this->commit();              
    }
    public function check_log_name($log_name){
        $sql="select count(*) as cnt from lichsu_in
                where log_name = :log_name";
        $res= $this->query($sql,array('log_name'=>$log_name));
        if(count($res) >0 && $res[0]['cnt'] > 0){
            return FALSE;
        }
        return TRUE;       
    }

}
ACWLog::debug_var('DEBUG_ReadPrintLog', "Start Batch!");
$batch = new BatchReadPrintLog_model();
$batch->main();
