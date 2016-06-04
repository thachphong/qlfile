<?php
define('ACW_PROJECT', 'qlfile');	
define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__));;
define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);
define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');	
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');
define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');
require ACW_USER_CONFIG_DIR . '/initialize.php';
require_once ACW_APP_DIR . '/lib/Path.php';

require_once ACW_APP_DIR . '/lib/FileWindows.php';
require_once ACW_APP_DIR . '/lib/File.php';
//require_once ACW_APP_DIR . '/model/Itemfile.php';

set_time_limit(0);

class ODB_Data extends ACWModel {
    
    public function exec_sql()
	{
		$table = $this->get_table();
		$file_bakup=ACW_LOG_DIR.'/backup_'.date('Ymdhi').'.sql';
		foreach($table as $item )
		{
			$table_name=$item['table_name'];
		
			//$table_name="banve";
			$sql = "select * from $table_name	";	        
	        $result= $this->query($sql);  
	        $header = 0 ;
	        $count = count($result);
	        $colnm_datatype = $this->get_datatype($table_name);
	        ODB_Data::write_log($file_bakup,"-- -------------------------$table_name---------------------");
	        ODB_Data::write_log($file_bakup,"Delete from $table_name ;");
	        ODB_Data::write_log($file_bakup,"-- --------");
	        foreach($result as $key=>$row) {
	        	if($key == $header *300 && ( $key < ($count - 1) || $count==1) ){
					$header++;
					$ins_header ="INSERT INTO `$table_name` (";
					$column =array();
					foreach($row as $col=>$val){
						$column[] ="`$col`";
					}
					$ins_header .= implode(',',$column).") VALUES ";
					ODB_Data::write_log($file_bakup,$ins_header);
				}
				
				$data_column  = array();
				foreach($row as $col=>$val){
					if( $val === NULL){
						$data_column[] ="NULL";
					}else{
						if($colnm_datatype[$col] =='int'){
							$data_column[] ="$val";
						}else{
							$data_column[] ="'$val'";
						}
					}
				}
				$ins_val ="(".implode(',',$data_column).")";
				if($key == (($header *300)-1) || $key== $count-1 ){
					$ins_val.=";";
				}else{
					$ins_val .=",";
				}
				ODB_Data::write_log($file_bakup,$ins_val);
			} 
		}
		
	}
	public function get_datatype($table_name){
		$sql ="SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA ='qlfile'				
				and table_name = '$table_name'";
		$res = $this->query($sql);
		$coulmn = array();
		foreach($res as $key => $row){
			$coulmn[$row['COLUMN_NAME']]= $row['DATA_TYPE'];
		}
		return $coulmn;
	}
	public function get_table(){
		$sql ="SELECT DISTINCT table_name FROM INFORMATION_SCHEMA.COLUMNS 
				WHERE TABLE_SCHEMA ='qlfile' 
				and table_name <> 'level'
				and table_name <> 'message_lang'
				and table_name <> 'screen'";
		$res = $this->query($sql);
		
		return $res;
	}
	public static function write_log($filepath, $str, $lv = 'OK')
	{
		$filepath = mb_convert_encoding($filepath, 'sjis-win', 'UTF-8');
		if (($fp = @fopen($filepath, "a")) === false) {
			return;
		}

		if (!flock($fp, LOCK_EX)) {
			@fclose($fp);
			return;
		}

		if (fwrite($fp, $str . "\r\n") === false) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!fflush($fp)) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!flock($fp, LOCK_UN)) {
			@fclose($fp);
			return;
		}

		if (!fclose($fp)) {
			return;
		}
	}
	
}
echo "Start ... ";
//echo date('YmdHis');
$db = new ODB_Data();
echo $db->exec_sql();
echo " end";
		