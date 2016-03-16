<?php
ini_set('memory_limit', -1); // 上限メモリ //Edit LIXD-287 Tin VNIT 10/29/2015

/**
 * 商品情報ファイル用
 */
class Itemfile_model extends Lock_common_model
{
	/**
	 * 共通初期化
	 */
	public static function init()
	{
		Login_model::check(); // ログインチェック
	}

	/**
	 * 登録済みファイルダウンロード
	 * URLはviewfile/filename/head_id/mei_id/switch
	 * となる
	 */
	public static function action_view()
	{
		$param = self::get_param(array('acw_url'));
		return self::_viewfile($param['acw_url'], null);
	}

    //Add Start - LIXD-10 - TrungVNIT - 2015/06/22
    public static function action_viewhtml(){
        $param = self::get_param(array('acw_url'));
        if(isset($param['acw_url'][0])){
            $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
        }
        return self::_viewfile($param['acw_url'], null);
    }
    
    public static function replace_name_uploaded($name, $extend = null){
        if($name == ''){
            return FALSE;
        }
        $exp = explode('.', $name);
        $l = count($exp) - 2;
        $exp[$l] = $exp[$l] . '_uploaded';
        if($extend != null){
            $exp[count($exp) - 1] = $extend;
        }
        return implode('.', $exp);
    }
    
    public static function replace_ext($name, $ext){
        $exp = explode('.', $name);
        $exp[count($exp) - 1] = $ext;
        return implode('.', $exp);
    }

    public static function action_viewhtmltmp()
	{
        $param = self::get_param(array('acw_url'));
        if(isset($param['acw_url'][0])){
            $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
        }
		return self::_viewtmp($param['acw_url'], null);
	}
    
    public static function action_yoyakuviewhtml()
	{
		$param = self::get_param(array('acw_url'));
        if(isset($param['acw_url'][0])){
            $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
        }
		return self::_yoyaku_viewfile($param['acw_url'], null);
	}
    
    public static function action_yoyakuviewhtmltmp()
	{
        $param = self::get_param(array('acw_url'));
        if(isset($param['acw_url'][0])){
            $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
        }
		return self::_yoyaku_viewtmp($param['acw_url'], null);
    }
    
    public static function action_viewhtmlfree()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
            if(isset($param['acw_url'][0])){
                $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
            }
			return self::_viewfile($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}
    
    public static function action_yoyakuviewhtmlfree()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
            if(isset($param['acw_url'][0])){
                $param['acw_url'][0] = Itemfile_model::replace_name_uploaded($param['acw_url'][0]);
            }
			return self::_yoyaku_viewfile($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}
    //Add End - LIXD-10 - TrungVNIT - 2015/06/22

    /**
	 * 登録済みファイルダウンロード
	 * URLはviewfreefile/free_id/filename/head_id/mei_id/switch
	 * となる
	 */
	public static function action_viewfree()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
			return self::_viewfile($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}

	private static function _viewfile($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) > 3) {
			$sfile = new SeriesFile_lib($url_param[1], $url_param[2]);
			$sw = (count($url_param) == 4) ? $url_param[3] : null;
                        //add start NBKD-1033 Phong VNIT 2015/03/06
			$lang =	'';
			if(isset($url_param[4])){
				if( substr($url_param[4],0,5) =='lang=' ){
					$sw =  $url_param[3] ;
					$lang =substr($url_param[4], strlen('lang='),1);	
					$obst= new ObjectStorage_lib();
					$con_name =Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_SERIES, $lang);
					if($obst->set_used_container($con_name)){
						$obst->view_file($sfile->get_full_path(),$url_param[0], $sw, $free_id);
					}
				}
			}
			//add end NBKD-1033 Phong VNIT 2015/03/06
			$sfile->view_file($url_param[0], $sw, $free_id);			
		}

		return ACWView::OK;
	}
	
	/**
	 * 登録済みファイルダウンロード
	 * Add - miyazaki U_SYS - 2014/11/18
	 */
	public static function action_yoyakuview()
	{
		$param = self::get_param(array('acw_url'));
		return self::_yoyaku_viewfile($param['acw_url'], null);
	}

	/**
	 * 登録済みファイルダウンロード
	 * Add - miyazaki U_SYS - 2014/11/18
	 */
	public static function action_yoyakuviewfree()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
			return self::_yoyaku_viewfile($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}

	/**
	 * Add - miyazaki U_SYS - 2014/11/18
	 */
	private static function _yoyaku_viewfile($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) > 3) {
			$sfile = new YoyakuSeriesFile_lib($url_param[1], $url_param[2]);
			$sw = (count($url_param) == 4) ? $url_param[3] : null;
			//add start NBKD-1033 Phong VNIT 2015/03/06
			$lang =	'';
			if(isset($url_param[4])){
				if( substr($url_param[4],0,5) =='lang=' ){
					$sw =  $url_param[3] ;
					$lang =substr($url_param[4], strlen('lang='),1);	
					$obst= new ObjectStorage_lib();
					$con_name =Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_Y_SERIES, $lang);
					if($obst->set_used_container($con_name)){
						$obst->view_file($sfile->get_full_path(),$url_param[0], $sw, $free_id);
					}	
				}
			}			
			//add end NBKD-1033 Phong VNIT 2015/03/06
			$sfile->view_file($url_param[0], $sw, $free_id);			
		}

		return ACWView::OK;
	}

	/**
	 * 一時ファイル表示フリー用
	 * URLはviewfreetmp/free_id/filename/tmp_name/switch
	 * となる
	 */
	/*public static function action_viewfreetmp()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
			return self::_viewtmp($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}*/

	public static function action_dlno()
	{
		$param = self::get_param(array('t_series_head_id', 't_series_mei_id', 'yoyaku_flg','object_storage_flg'));//add NBKD-1033 Phong VNIT 2015/03/11

		// シリーズ明細PATHを求める
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$series_file = new YoyakuSeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
		} else {
			$series_file = new SeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		$item_no_path = new Path_lib($series_file->get_full_path());
		$item_no_path->combine('item_no.xlsx');
                //add start NBKD-1033 Phong VNIT - 2015/03/11
		if ((isset($param['object_storage_flg']) == true) && ($param['object_storage_flg'] == '1')){			
			return ACWView::download_file(basename($item_no_path->get_full_path()), $item_no_path->get_full_path(),TRUE);	
		}else{
			return ACWView::download(basename($item_no_path->get_full_path()), file_get_contents($item_no_path->get_full_path()));	
		}//add end NBKD-1033 Phong VNIT - 2015/03/11
	}
	
	public static function action_chkdlno()
	{
		$param = self::get_param(array('t_series_head_id', 't_series_mei_id', 'yoyaku_flg','m_lang_id'));//add NBKD-1033 Phong VNIT - 2015/03/11

		// シリーズ明細PATHを求める
		// Edit start - miyazaki U_SYS - 2014/11/19
		$container_key=''; //add NBKD-1033 Phong VNIT 2015/03/11
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$series_file = new YoyakuSeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
			$container_key = AKAGANE_CONTAINER_KEY_Y_SERIES; //add NBKD-1033 Phong VNIT 2015/03/11
		} else {
			$series_file = new SeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
			$container_key = AKAGANE_CONTAINER_KEY_SERIES; //add NBKD-1033 Phong VNIT 2015/03/11
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		$item_no_path = new Path_lib($series_file->get_full_path());
		$item_no_path->combine('item_no.xlsx');
		
        $file = new File_lib();
        $result = array();
        //add start NBKD-1033 Phong VNIT 2015/03/11
        if ((isset($param['m_lang_id']) == true) && ($param['m_lang_id'] != '')) {
        	$con_name =Container_common_model::get_container_name_query($container_key, $param['m_lang_id']);
			$obst = new ObjectStorage_lib();			
			$file_path=$item_no_path->get_full_path();
			$find_pos= strrpos($file_path,'data',1);
			$file_name =str_replace('\\','/',substr($file_path,$find_pos,strlen($file_path)));						
			if($obst->set_used_container($con_name)){
				if($obst->object_exists($file_name)){	
					$obst->get_file($file_name,$file_path);				
					$result['status'] = 'OK';
	            	$result['error'] = null;           
				}else{
					$result['status'] = 'NG';
	            	ACWError::add('document', '表示中のシリーズでは品番情報がアップロードされていないため、ダウンロードできません');
	            	$result['error'] = ACWError::get_list();  
				}	
			}else{
				$result['status'] = 'NG';
	            ACWError::add('document', '表示中のシリーズでは品番情報がアップロードされていないため、ダウンロードできません');
	            $result['error'] = ACWError::get_list();  
			}
		}else{//add end NBKD-1033 Phong VNIT 2015/03/11
	        if ($file->FileExists($item_no_path->get_full_path())) {
	            $result['status'] = 'OK';
	            $result['error'] = null;           
	        } else {
	            $result['status'] = 'NG';
	            ACWError::add('document', '表示中のシリーズでは品番情報がアップロードされていないため、ダウンロードできません');
	            $result['error'] = ACWError::get_list();    
	        }
        }//add NBKD-1033 Phong VNIT 2015/03/11
		
		return ACWView::json($result);
	}

	/**
	 * 組版ダウンロードチェック
	 * URLはchkindddl/mei_id or chkindddl/mei_id
	 * となる
	 */
	public static function action_chkindddl()
	{
		$param = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
		//Add Start LIXD-18 hungtn VNIT 20150813
		$media_type_id = -1;
		if(isset($_POST['m_media_type_id'])) {
			$media_type_id = $_POST['m_media_type_id'];
		}
		//Add End LIXD-18 hungtn VNIT 20150813
		if (isset($url_param) && (count($url_param) == 3 || count($url_param) == 4)) { // Edit - miyazaki U_SYS - 2015/01/08
           
			$path = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			if ($url_param[0] != 'log') {
				// 一時ファイルパス
				$db = new Itemfile_model();
				// Edit start - miyazaki U_SYS - 2014/11/20
				if ((isset($url_param[3]) == true) && ($url_param[3] == 'yoyaku')) { // Edit - miyazaki U_SYS - 2015/01/08
					$fileinfo = $db->get_yoyaku_typeset_files($url_param[1], $media_type_id); //Edit LIXD-18 hungtn VNIT 20150813
				} else {
					$fileinfo = $db->get_typeset_files($url_param[1], $media_type_id);//Edit LIXD-18 hungtn VNIT 20150813
				}
				// Edit end - miyazaki U_SYS - 2014/11/20				
				if (count($fileinfo) == 1) {
					if ($url_param[0] == 'file') {
						//Edit start NBKD-1033 TinVNIT 3/6/2015
						$url = $fileinfo[0]['zip_file_path'];
						$path->combine($url);
					} else if ($url_param[0] == 'pdf') {
						$url = $fileinfo[0]['pdf_file_path'];
						$path->combine($url);
						//Edit end NBKD-1033 TinVNIT 3/6/2015
					} else {
						$path = null;
					}
					//Add start NBKD-1033 TinVNIT 3/6/2015
					if((isset($path)) && $fileinfo[0]['object_storage_flg'] == 1)
					{
						$url = ltrim ($url,"/");
						//get_file
				        $obst = new ObjectStorage_lib();
				        $tmp = explode("/", $url);
				        $exten = end($tmp);
				        $container = Container_common_model::get_container_name_query(AKAGANE_CONTAINER_KEY_KUMIHAN);
						if($obst->set_used_container($container) == true)
	        			{
	        				if($result = $obst->get_file($url, $path->get_full_path()) == true)
	            			{
	            				$result = array();
								$result['status'] = 'OK';
								$result['object_storage_flg'] = 1;
								return ACWView::json($result);
	            			}
	            			else{
	            				ACWError::add('', 'ダウンロードできるデータがありません。');
								$result = array();
								$result['status'] = 'NG';
								$result['error'] = ACWError::get_list();
								return ACWView::json($result);
							}
	        			}
						else{
							ACWError::add('', 'ダウンロードできるデータがありません。');
							$result = array();
							$result['status'] = 'NG';
							$result['error'] = ACWError::get_list();
							return ACWView::json($result);
						}
					}
					//Add End NBKD-1033 TinVNIT 3/6/2015
				}
			} else {
				if ($url_param[0] == 'log') {
					//$path->combine('Typesetting/Log'.$url_param[1].'.log');
					$path->combine('Typesetting/Log/'.$url_param[2].'.txt');
				} else {
					$path = null;
				}
			}
			
			$file = new File_lib();
			if ((isset($path)) && ($file->FileExists($path->get_full_path()))) {
				return ACWView::json(array('status' => 'OK'));
			}

			ACWError::add('', 'ダウンロードできるデータがありません。');
		} else {
			ACWError::add('引数', '内部エラーです。パラメタが異なります。');
		}

		$result = array();
		$result['status'] = 'NG';
		$result['error'] = ACWError::get_list();
		return ACWView::json($result);
	}

	/**
	 * 組版ダウンロード
	 * URLはdlindd/pdf/mei_id or dlindd/file/mei_id
	 * となる
	 */
	public static function action_dlindd()
	{
		$param = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
		if (isset($url_param) && (count($url_param) == 4 || count($url_param) == 5)) { // Edit - miyazaki U_SYS - 2015/01/08
			
			$pathlib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			if ($url_param[0] != 'log') {
				// 一時ファイルパス
				$db = new Itemfile_model();
				// Edit start - miyazaki U_SYS - 2014/11/20
				if ((isset($url_param[4]) == true) && ($url_param[4] == 'yoyaku')) { // Edit - miyazaki U_SYS - 2015/01/08
					$fileinfo = $db->get_yoyaku_typeset_files($url_param[1], $url_param[3]); //Edit LIXD-18 hungtn VNIT 2015813
				} else {
					$fileinfo = $db->get_typeset_files($url_param[1], $url_param[3]);//Edit LIXD-18 hungtn VNIT 2015813
				}
				// Edit end - miyazaki U_SYS - 2014/11/20
				if (count($fileinfo) == 1) {
					if ($url_param[0] == 'file') {
						$pathlib->combine($fileinfo[0]['zip_file_path']);
						$head = '';
						$ext = 'zip';
					} else if ($url_param[0] == 'pdf') {
						$pathlib->combine($fileinfo[0]['pdf_file_path']);
						$head = '';
						$ext = 'pdf';
					} else {
						$pathlib = null;
					}
				}
			} else {
				//$pathlib->combine($fileinfo[0]['log_file_path']);
				$pathlib->combine('Typesetting/Log/'.$url_param[2].'.txt');
				$head = $url_param[2] . '_';
				$ext = 'txt';
			}
			if (isset($pathlib)) {
				//$basename = $pathlib->get_pathinfo('basename');
				$trigger_model = new Trigger_model();
				// Edit start - miyazaki U_SYS - 2014/11/20
				if ((isset($url_param[4]) == true) && ($url_param[4] == 'yoyaku')) { // Edit - miyazaki U_SYS - 2015/01/08
					$ver_data = $trigger_model->get_yoyaku_new_ver_data(null, $url_param[1]);
				} else {
					$ver_data = $trigger_model->get_new_ver_data(null, $url_param[1]);
				}
				// Edit end - miyazaki U_SYS - 2014/11/20
				$basename = sprintf(
						'%s%s_%s_%d.%d.%s'
					  , $head
					  , Trigger_model::_replace_under_ber($ver_data['series_id'])
					  , Trigger_model::_replace_under_ber($ver_data['lang_kbn'])
					  , $ver_data['major_ver']
					  , $ver_data['minor_ver']
					  , $ext
				  ); 

				//return ACWView::download($basename, file_get_contents($pathlib->get_full_path()));
				//Edit start LIXD-34 NBKD-1202 TinVNIT 8/21/2015
				//$full_path = mb_convert_encoding($pathlib->get_full_path(), "Shift_JIS", "UTF-8"); //edit LIXD-368 Phong VNIT-20151130
				$full_path = mb_convert_encoding($pathlib->get_full_path(), "sjis-win", "UTF-8"); //edit LIXD-368 Phong VNIT-20151130 
				return ACWView::download_file($basename, $full_path); 
				//Edit end LIXD-34 NBKD-1202 TinVNIT 8/21/2015				
			}
		}
		return ACWView::OK;
	}
	//Add start NBKD-1033 TinVNIT 3/6/2015
	public static function action_dlinddobst()
	{
		$param = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
		
		if (isset($url_param) && (count($url_param) == 4 || count($url_param) == 5)) { // Edit - miyazaki U_SYS - 2015/01/08
			
			$pathlib = new Path_lib(AKAGANE_DTPSERVER_IF_PATH);
			if ($url_param[0] != 'log') {
				// 一時ファイルパス
				$db = new Itemfile_model();
				// Edit start - miyazaki U_SYS - 2014/11/20
				if ((isset($url_param[4]) == true) && ($url_param[4] == 'yoyaku')) { // Edit - miyazaki U_SYS - 2015/01/08
					$fileinfo = $db->get_yoyaku_typeset_files($url_param[1], $url_param[3]); //Edit LIXD-18 hungtn VNIT 2015813
				} else {
					$fileinfo = $db->get_typeset_files($url_param[1], $url_param[3]); //Edit LIXD-18 hungtn VNIT 2015813
				}
				// Edit end - miyazaki U_SYS - 2014/11/20
				if (count($fileinfo) == 1) {
					if ($url_param[0] == 'file') {
						$pathlib->combine($fileinfo[0]['zip_file_path']);
						$head = '';
						$ext = 'zip';
					} else if ($url_param[0] == 'pdf') {
						$pathlib->combine($fileinfo[0]['pdf_file_path']);
						$head = '';
						$ext = 'pdf';
					} else {
						$pathlib = null;
					}
				}
			} else {
				//$pathlib->combine($fileinfo[0]['log_file_path']);
				$pathlib->combine('Typesetting/Log/'.$url_param[2].'.txt');
				$head = $url_param[2] . '_';
				$ext = 'txt';
			}
			if (isset($pathlib)) {
				//$basename = $pathlib->get_pathinfo('basename');
				$trigger_model = new Trigger_model();
				// Edit start - miyazaki U_SYS - 2014/11/20
				if ((isset($url_param[4]) == true) && ($url_param[4] == 'yoyaku')) { // Edit - miyazaki U_SYS - 2015/01/08
					$ver_data = $trigger_model->get_yoyaku_new_ver_data(null, $url_param[1]);
				} else {
					$ver_data = $trigger_model->get_new_ver_data(null, $url_param[1]);
				}
				// Edit end - miyazaki U_SYS - 2014/11/20
				$basename = sprintf(
						'%s%s_%s_%d.%d.%s'
					  , $head
					  , Trigger_model::_replace_under_ber($ver_data['series_id'])
					  , Trigger_model::_replace_under_ber($ver_data['lang_kbn'])
					  , $ver_data['major_ver']
					  , $ver_data['minor_ver']
					  , $ext
				  ); 

				//return ACWView::download($basename, file_get_contents($pathlib->get_full_path()));
				return ACWView::download_file($basename, $pathlib->get_full_path(), TRUE);
			}
		}

		return ACWView::OK;
	}
	//Add end NBKD-1033 TinVNIT 3/6/2015
	public static function action_checkdltmp()
	{
		$param = self::get_param(array('filename', 'tmp_name', 'yoyaku_flg'));
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$pathlib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		} else {
			$pathlib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		$pathlib->combine($param['tmp_name']);
		$pathlib->combine($param['filename']);
		
		$file = new File_lib();
		if ($file->FileExists($pathlib->get_full_path())) {
			$result['status'] = 'OK';
			$result['error'] = null;           
		} else {
			$result['status'] = 'NG';
			ACWError::add('document', '対象のファイルが存在しないため、ダウンロードできません');
            $result['error'] = ACWError::get_list();    
		}

		return ACWView::json($result);
	}
	
	/**
	 * 一時ファイルダウンロード
	 * URLはdltmp/org_filename/filename/tmp_name
	 * となる
	 */
	public static function action_dltmp()
	{
		$param = self::get_param(array('acw_url'));
		return self::_dltmp($param['acw_url'], null);
	}

	private static function _dltmp($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) == 3) {
			// 一時ファイルパス
			$pathlib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			$pathlib->combine($url_param[2]);
			if (is_null($free_id) == false) {
				$pathlib->combine($free_id);
			}
			$pathlib->combine($url_param[1]);
			return ACWView::download($url_param[0], file_get_contents($pathlib->get_full_path()));
		}

		return ACWView::OK;
	}
	
	/**
	 * 一時ファイルダウンロード
	 * URLはdltmp/org_filename/filename/tmp_name
	 * となる
	 * Add - miyazaki U_SYS - 2014/11/19
	 */
	public static function action_yoyakudltmp()
	{
		$param = self::get_param(array('acw_url'));
		return self::_yoyaku_dltmp($param['acw_url'], null);
	}

	/*
	 * Add - miyazaki U_SYS - 2014/11/19
	 */
	private static function _yoyaku_dltmp($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) == 3) {
			// 一時ファイルパス
			$pathlib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			$pathlib->combine($url_param[2]);
			if (is_null($free_id) == false) {
				$pathlib->combine($free_id);
			}
			$pathlib->combine($url_param[1]);
			return ACWView::download($url_param[0], file_get_contents($pathlib->get_full_path()));
		}

		return ACWView::OK;
	}

	/**
	 * 一時ファイル表示
	 * URLはviewtmp/filename/tmp_name/switch
	 * となる
	 */
	public static function action_viewtmp()
	{
		$param = self::get_param(array('acw_url'));
		return self::_viewtmp($param['acw_url'], null);
	}

    public static function action_viewtmpex() { //Add Start LIXD-357 hungtn VNIT 20151123
        $param = self::get_param(array('acw_url'));
        //var_dump($param); die;
        $file_lib = new File_lib();
        $folder = isset($param['acw_url'][1])? $param['acw_url'][1]:'';
        $full_path = '';
        if(!empty($folder)) {
            $filename = '';
            $file = isset($param['acw_url'][0])? $param['acw_url'][0]:'';
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $filename = $filename.'.html';
            $full_path = AKAGANE_SERIES_TMP_PATH.'/'.$folder."/excel/".$filename;
            if($file_lib->FileExists($full_path) == FALSE) {
                $exfile = pathinfo($file, PATHINFO_BASENAME);
                $expath = AKAGANE_SERIES_TMP_PATH.'/'.$folder."/excel/".$exfile;
                $excel = new ImportExport_lib();
                //$excel->load($fullpath);
                if ($excel->to_html($expath, $full_path) == false) {
                    ACWLog::debug_var('LIXD-357', 'Cannot convert Excel to HTML: ' . $fullpath);
                }
                //Add End LIXD-357 hungtn VNIT 20151123
            }
        }
        
		if ($file_lib->FileExists($full_path)) {
				echo file_get_contents($full_path);
		}
        return ACWView::OK;
    }//Add End LIXD-357 hungtn VNIT 20151123
	/**
	 * 一時ファイルダウンロードフリー用
	 * URLはdlfreetmp/free_id/org_filename/filename/tmp_name
	 * となる
	 */
	public static function action_dlfreetmp()
	{
		$param = self::get_param(array('acw_url'));
		if (is_array($param['acw_url']) && count($param['acw_url']) > 0) {
			$free_id = array_shift($param['acw_url']);
			return self::_viewtmp($param['acw_url'], $free_id);
		}
		return ACWView::OK;
	}

	private static function _viewtmp($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) > 2) {
			$sw = (count($url_param) == 3) ? $url_param[2] : null;
			// 一時ファイルパス
			
			$pathlib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			$pathlib->combine($url_param[1]);
			SeriesFile_lib::view($pathlib, $url_param[0], $sw, $free_id);
		}

		return ACWView::OK;
	}

	/**
	 * 一時ファイル表示
	 * URLはviewtmp/filename/tmp_name/switch
	 * となる
	 * Add - miyazaki U_SYS - 2014/11/19
	 */
	public static function action_yoyakuviewtmp()
	{
		$param = self::get_param(array('acw_url'));
		return self::_yoyaku_viewtmp($param['acw_url'], null);
	}
	
	/**
	 * 一時ファイル表示（フリー用）
	 * Add - miyazaki U_SYS - 2014/11/19
	 */
	public static function action_yoyakuviewtmpfree()
	{
		$param = self::get_param(array('acw_url'));
		$free_id = $param['acw_url'][3];
		unset($param['acw_url'][3]);
		return self::_yoyaku_viewtmp($param['acw_url'], $free_id);
	}
	
	/*
	 * Add - miyazaki U_SYS - 2014/11/19
	 */
	private static function _yoyaku_viewtmp($url_param, $free_id)
	{
		if (isset($url_param) && count($url_param) > 2) {
			$sw = (count($url_param) == 3) ? $url_param[2] : null;
			// 一時ファイルパス
			$pathlib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			$pathlib->combine($url_param[1]);
			YoyakuSeriesFile_lib::view($pathlib, $url_param[0], $sw, $free_id);
		}

		return ACWView::OK;
	}
    
    public static function action_uploadtmp(){
        $param = self::get_param(array(
            'name_file',
            'data_id',
            'obj_id',
            'fileup',
            'tmp_name',
            'mei_id',
            'series_id',
            't_ctg_head_id',
            't_ctg_section_id',
            't_series_head_id',
            'yoyaku_flg'));
        
        $yoyaku_flg = isset($param['yoyaku_flg']) ? 1 : '';
        $excel_file = $param['name_file'];
        $rs = Itemfile_model::uptmp($param, $excel_file, $yoyaku_flg);
        
        $result['file_name'] = $rs['file_name'];
        $result['obj_id'] = $param['obj_id'];
        $result['data_id'] = $param['data_id'];
        $result['org_file_name'] = $rs['org_file_name'];
        return ACWView::json($result);
    }

    protected static function uptmp(&$param, $excel_file, $yoyaku_flg = ''){ //Edit Start LIXD-321 hungtn VNIT 20151107
        $upl = new Upload_lib($_FILES[$excel_file]);
        
        $is_image = false;
        if($upl->get_ext() != "xlsx" && $upl->get_ext() != "xls") {
            $is_image = TRUE;
        }
        
        if ($upl->get_ext() == '') {
			ACWError::add('name_file', '拡張子がないファイルはアップロードできません。');
			return false;
		}
        if ($yoyaku_flg == 1) {
			$tmppath = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		} else {
			$tmppath = new Path_lib(AKAGANE_SERIES_TMP_PATH);
		}
		$tmppath->combine(/*'tmp_'.*/$param['tmp_name']);//edit LIXD-287 Phong VNIT-20151105
        
        $folder = $tmppath->get_full_path() . '/excel';
        
        if($is_image == TRUE) {
            $folder = $tmppath->get_full_path();
        }
        
        $file_name = $param['name_file'] . '.' . $upl->get_ext();
        if($is_image == TRUE) {
            //$file_name = $ . '.' . $upl->get_ext();
            $file_name = $tmppath->add_random_filename('', $upl->get_ext(),TRUE);//edit LIXD-9 Phong VNIT-20150624
        }
        
        $fullpath = $folder . '/' . $file_name;
        
        $flib = new File_lib();
        if($flib->FolderExists($folder) == FALSE){
			//Add Start - LIXD-10 - TrungVNIT - 2015/18/11
			if($flib->FolderExists($tmppath->get_full_path()) === FALSE){
				$flib->CreateFolder($tmppath->get_full_path());
			}
			//Add End - LIXD-10 - TrungVNIT - 2015/18/11
            $flib->CreateFolder($folder);
        }

		if ($upl->move_to($fullpath) == false) {
			ACWError::add('name_file', 'アップロードファイルの移動に失敗しました。');
			return false;
		}
        if($is_image == TRUE) {
            $file_name_tmp = pathinfo($file_name, PATHINFO_FILENAME);
            $file_name_thums = $file_name_tmp . '_s.' . $upl->get_ext();
            $fullpath_thums = $folder . '/' . $file_name_thums;
            $image = new Image_lib();
    		if ($image->thumb($fullpath, $fullpath_thums,
    					THUMBNAIL_IMAGE_WIDTH, THUMBNAIL_IMAGE_HEIGHT, AKAGANE_ICON_THUMB_EXT) == false) {
    			ACWError::add('name_file', '扱えない形式のファイルです。');
    		}
        } else {//Add Start LIXD-357 hungtn VNIT 20151123
            //$html_file = $folder.'/'.$param['name_file'] . '.html';
            //$excel = new ImportExport_lib();
            //$excel->load($fullpath);
            //if ($excel->to_html($fullpath, $html_file) == false) {//edit LIXD-287 Phong VNIT-20151105 //Edit LIXD-332 hungtn VNIT 20151113
            //    ACWLog::debug_var('LIXD-357', 'Cannot convert Excel to HTML: ' . $fullpath);
            //}
            //Add End LIXD-357 hungtn VNIT 20151123
            //add start LIXD-422 Phong VNIT 20160112
            $itemcode = new Itemcode_model();
            $itemcode->extract_txt_file($fullpath,'','','','');
            //add end LIXD-422 Phong VNIT 20160112
        }
//Add End LIXD-321 hungtn VNIT 20151107
        
        $return = array(
            'file_name' => $file_name,
            'org_file_name' => $upl->get_name(),
        );
        return $return;
    }

        /**
	 * シリーズ、アップロード
	 */
	public static function action_upload()
	{
		$param = self::get_param(array(
			'series_file'
			,'lang_id'
			,'uptype'
			,'tmp_name'
			,'old_name'
			,'mei_id'
			//Add Start - NBKD-73 - TrungVNIT - 2014/12/03
			,'series_id'
			,'t_ctg_head_id'
			,'t_ctg_section_id'
			//Add End - NBKD-73 - TrungVNIT - 2014/12/03
			// Add start - miyazaki U_SYS - 2015/01/14
			,'section_name'
			,'free_flg'
			// Add end - miyazaki U_SYS - 2015/01/14
			,'t_series_head_id' //Add LIXD-10 MinhVnit 2015/06/24
			,'sec_no' //Add LIXD-10 MinhVnit 2015/06/24
            ,'list_file_html' //Add - LIXD-10 - TrungVNIT - 2015/07/06
            ,'data_array' //Add - LIXD-10 - TrungVNIT - 2015/08/20
            ,'seq'//LIXD-10 Add MinhVnit 2015/10/08
			));
        
        $list_tags = array();  
        $tags_unset = array();
        $tags_array = array();

		$err = false;
		if (self::get_validate_result() == false) {
			$err = true;
		} else if ($param['uptype'] == 'imgup' || $param['uptype'] == 'multiimgup') { // Add Start LIXD-9 hungtn VNIT 20150622
			/*
			 * 画像サムネイル
			 */
			 if($param['uptype'] == 'imgup') {
			 	$err = (SeriesFile_lib::convert_image_file($param) == false) ? true : false;
			 } else {
			 	// multiimgup
			 	$err = (SeriesFile_lib::convert_multiimate_file($param) == false) ? true : false;
			 }
			
		} else if ($param['uptype'] == 'hdrexcelup' || $param['uptype'] == 'excelup') {
			//edit start LIXD-359 Phong VNIT 20151126
			$file = new File_lib();
			if($file->GetExtensionName($param['org_file_name'])=='xls'){
				ACWError::add('Excel_old', '古いバージョンのエクセルのため、アップロード出来ません' );
				$err=TRUE;
			}else{
			//edit end LIXD-359 Phong VNIT 20151126
				if ($param['uptype'] == 'hdrexcelup') {
					/*
					 * ヘッダーExcel
					 */
					if (self::_execute_header_excel($param) == false) {
						$err = true;
					}
				}
				if ($err == false) {
					/*
					 * エクセル変換
					 */
	               
	                //Add Start - LIXD-10 - TrungVNIT - 2015/08/20
	                $tmppath = $param['tmppath'];
	                $excel_path = $tmppath->get_full_path();
					//Edit start LIXD-284 Tin VNIT 10/29/2015
					try {
			            $excel = new ImportExport_lib();
			            $excel_file_new = self::SaveAsExcelFile($excel_path);//add LIXD-287 Phong VNIT-20151105
			            if ($excel->load($excel_file_new) === false) {			//edit LIXD-287 Phong VNIT-20151105
			                ACWLog::debug_var('LIXD-10', 'Can not open excel file: '.$excel_path);
			                return FALSE;
			            }
			        } catch (Exception $exc) {
			            ACWLog::debug_var('LIXD-10', $exc->getMessage());
			            return FALSE;
			        }
	                $tags_data = SeriesFile_lib::get_tags_excel($excel_path, $param, $excel);//Edit  LIXD-10 hungtn VNIT 20150922
	                //Edit end LIXD-284 Tin VNIT 10/29/2015
	                $list_tags = $tags_data['tags'];
	                $tags_unset = $tags_data['unset'];
	                $tags_array = $tags_data['data_array'];
	                //Add End - LIXD-10 - TrungVNIT - 2015/08/20
					$err = (SeriesFile_lib::convert_excel_file($param, $excel) == false) ? true : false; //Edit LIXD-284 Tin VNIT 10/29/2015
					//add start LIXD-422 Phong VNIT 20160112
					$itemcode = new Itemcode_model();
					$itemcode->extract_txt_file($excel_path,'','','','');
					//add end LIXD-422 Phong VNIT 20160112
				}
			}
		}

		if ($err){
			// 今アップロードしたファイルを削除
			//Add LIXD-9 hungtn VNIT 20150701
			if($param['uptype'] != 'multiimgup') {
				SeriesFile_lib::delete_old_file($param['tmp_name'], $param['file_name']);
			}
			//Add LIXD-9 hungtn VNIT 20150701
			$result = array();
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			//Add Start LIXD-9 hungtn VNIT 20150622
			if($param['uptype'] != 'multiimgup') {
				$param['error_flg'] = null;
			}
			//Add Start LIXD-9 hungtn VNIT 20150622
            
            //Add Start - LIXD-10 - TrungVNIT - 2015/07/06
            if ($param['uptype'] == 'imgup') {
                if(isset($param['old_name']) && $param['old_name'] != NULL){
                    $list_file = (isset($param['list_file_html'])) ? $param['list_file_html'] : '';
                    HTMLDOMParser_lib::replace_image_uploaded($param['sec_no'], $list_file, $param['tmp_name'], $param['old_name'], $param['file_name']);
                }
            }
            //Add End - LIXD-10 - TrungVNIT - 2015/07/06
            
			$result = ACWArray::filter($param
				,array(
					'file_name'
					,'org_file_name'
					,'org_header_file_name'
					,'header_file_name'
					,'error_flg'// Edit  LIXD-9 hungtn VNIT 20150630
					)
				);
			$result['status'] = 'OK';
			
			// その他ページ用ファイル拡張子
			$file_lib = new File_lib();
			// Edit Start LIXD-9 hungtn VNIT 20150622
			if($param['uptype'] == 'multiimgup') {
				foreach($result['org_file_name'] as $item) {
					$result['file_ext'][] = $file_lib->GetExtensionName($item);
				}
				$result['error'] = ACWError::get_list();
			} else {
				$result['file_ext'] = $file_lib->GetExtensionName($result['org_file_name']);
			}
			// Edit End LIXD-9 hungtn VNIT 20150622
		}
        //sort($list_tags, SORT_STRING); // Add LIXD-10 hungtn VNIT 20150918
        $result['list_tags'] = $list_tags;//Add - LIXD-10 - TrungVNIT - 2015/08/20
        $result['tags_unset'] = $tags_unset;
        $result['tags_array'] = $tags_array;
        
        $tmp = array();
        if(count($tags_array) > 0) {
            foreach($tags_array as $item) {
                $text = explode("<", $item);
                $text = explode(">", $text[1]);
                $text = $text[0];
                $tmp[] = $text;
            }
            
            $result['tags_array_new'] = $tmp;
        }
        
		return ACWView::json($result, 1);//Edit - LIXD-10 - TrungVNIT - 2015/08/20
	}
	
	/**
	 * シリーズ、アップロード
	 */
	public static function action_yoyakuupload()
	{
		$param = self::get_param(array(
			'series_file'
			,'lang_id'
			,'uptype'
			,'tmp_name'
			,'old_name'
			,'mei_id'
			,'yoyaku_flg'
			//Add Start - NBKD-785 - TinVNIT - 2014/12/03
			,'series_id'
			,'t_ctg_head_id'
			,'t_ctg_section_id'
			//Add End - NBKD-785 - TinVNIT - 2014/12/03
			// Add start - miyazaki U_SYS - 2015/01/14
			,'section_name'
			,'free_flg'
			// Add end - miyazaki U_SYS - 2015/01/14
			,'t_series_head_id' //Add LIXD-10 MinhVnit 2015/06/24
			,'sec_no' //Add LIXD-10 MinhVnit 2015/06/24
            ,'list_file_html'
            ,'data_array' //Add - LIXD-10 - TrungVNIT - 2015/08/20
            ,'seq'//LIXD-10 Add MinhVnit 2015/10/08
			));
          
        $list_tags = array();  
        $tags_unset = array();
        $tags_array = array();
        
		$err = false;
		if (self::get_validate_result() == false) {
			$err = true;
		}else if ($param['uptype'] == 'imgup' || $param['uptype'] == 'multiimgup') { // Add Start LIXD-9 hungtn VNIT 20150622
			/*
			 * 画像サムネイル
			 */
			 if($param['uptype'] == 'imgup') {
			 	$err = (YoyakuSeriesFile_lib::convert_image_file($param) == false) ? true : false;
			 } else {
			 	// multiimgup
			 	$err = (YoyakuSeriesFile_lib::convert_multiimate_file($param) == false) ? true : false;
			 }
			
		} else if ($param['uptype'] == 'hdrexcelup' || $param['uptype'] == 'excelup') {
			if ($param['uptype'] == 'hdrexcelup') {
				/*
				 * ヘッダーExcel
				 */
				$param['yoayku_flg'] = 1;
				if (self::_execute_header_excel($param) == false) {
					$err = true;
				}
			}
			if ($err == false) {
                
                //Add Start - LIXD-10 - TrungVNIT - 2015/08/20
                $tmppath = $param['tmppath'];
                $excel_path = $tmppath->get_full_path();
                //Edit start LIXD-284 Tin VNIT 10/29/2015
                //$tags_data = SeriesFile_lib::get_tags_excel($excel_path, $param); //Edit LIXD-10 hungtn VNIT 20150922
                try {
		            $excel = new ImportExport_lib();
		            if ($excel->load($excel_path) === false) {
		                ACWLog::debug_var('LIXD-10', 'Can not open excel file: '.$path);
		                return FALSE;
		            }
		        } catch (Exception $exc) {
		            ACWLog::debug_var('LIXD-10', $exc->getMessage());
		            return FALSE;
		        }
                $tags_data = SeriesFile_lib::get_tags_excel($excel_path, $param, $excel);//Edit  LIXD-10 hungtn VNIT 20150922
                //Edit end LIXD-284 Tin VNIT 10/29/2015
				$list_tags = $tags_data['tags'];
                $tags_unset = $tags_data['unset'];
                $tags_array = $tags_data['data_array'];
                //Add End - LIXD-10 - TrungVNIT - 2015/08/20
				/*
				 * エクセル変換
				 */
				$err = (YoyakuSeriesFile_lib::convert_excel_file($param) == false) ? true : false;
			}
		}

		if ($err){
			// 今アップロードしたファイルを削除
			//Add LIXD-9 hungtn VNIT 20150701
			if($param['uptype'] != 'multiimgup') {
				YoyakuSeriesFile_lib::delete_old_file($param['tmp_name'], $param['file_name']);
			}
			//Add LIXD-9 hungtn VNIT 20150701
			$result = array();
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			//Add Start LIXD-9 hungtn VNIT 20150622
			if($param['uptype'] != 'multiimgup') {
				$param['error_flg'] = null;
			}
			//Add Start LIXD-9 hungtn VNIT 20150622
            
            //Add Start - LIXD-10 - TrungVNIT - 2015/07/06
            if ($param['uptype'] == 'imgup') {
                if(isset($param['old_name']) && $param['old_name'] != NULL){
                    $list_file = (isset($param['list_file_html'])) ? $param['list_file_html'] : '';
                    HTMLDOMParser_lib::replace_image_uploaded($param['sec_no'], $list_file, $param['tmp_name'], $param['old_name'], $param['file_name'], 1);
                }
            }
            //Add End - LIXD-10 - TrungVNIT - 2015/07/06
            
			$result = ACWArray::filter($param
				,array(
					'file_name'
					,'org_file_name'
					,'org_header_file_name'
					,'header_file_name'
					,'error_flg' // Edit LIXD-9 hungtn VNIT 20150622
					)
				);

			$result['status'] = 'OK';
			
			// その他ページ用ファイル拡張子
			$file_lib = new File_lib();

			// Edit Start LIXD-9 hungtn VNIT 20150622
			if($param['uptype'] == 'multiimgup') {
				foreach($result['org_file_name'] as $item) {
					$result['file_ext'][] = $file_lib->GetExtensionName($item);
				}
				$result['error'] = ACWError::get_list();
			} else {
				$result['file_ext'] = $file_lib->GetExtensionName($result['org_file_name']);
			}
			// Edit End LIXD-9 hungtn VNIT 20150622
		}
        //Add Start - LIXD-10 - TrungVNIT - 2015/08/25
        $result['list_tags'] = $list_tags;//Add - LIXD-10 - TrungVNIT - 2015/08/20
        $result['tags_unset'] = $tags_unset;
        $result['tags_array'] = $tags_array;
        
        //Add Start LIXD-10 hungtn VNIT 20150922
        $tmp = array();
        if(count($tags_array) > 0) {
            foreach($tags_array as $item) {
                $text = explode("<", $item);
                $text = explode(">", $text[1]);
                $text = $text[0];
                $tmp[] = $text;
            }
            
            $result['tags_array_new'] = $tmp;
        }
        //Add End LIXD-10 hungtn VNIT 20150922
        
        //Add End - LIXD-10 - TrungVNIT - 2015/08/25
		return ACWView::json($result, 1);
	}

	/**
	 * 関連情報画像設定
	 */
	public static function action_relimg()
	{
		$param = self::get_param(array(
			'r_mei_id'
			,'tmp_name'
			,'old_name'
			,'yoyaku_flg'
			,'section_name' // Add NBKD-1045 hungtn VNIT 20150609
			,'m_lang_id' // Add NBKD-1045 hungtn VNIT 20150609
			,'t_series_mei_id' // Add LIXD-10 Phong VNIT 20150701
			,'section_id'      // Add LIXD-10 Phong VNIT 20150701
			,'sec_no'          // Add LIXD-10 Phong VNIT 20150701
			));
		$series_path= AKAGANE_SERIES_TMP_PATH ;// Add LIXD-10 Phong VNIT 20150701
		$db = new Itemfile_model();
		$t_series_head_id = $db->get_t_series_head_id($param['t_series_mei_id']);
		// コピー処理
		// Edit start - miyazaki U_SYS - 2014/11/28
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$result = self::_copy_yoyaku_reference_image($param);
			$series_path =  AKAGANE_YOYAKU_SERIES_TMP_PATH ; // Add LIXD-10 Phong VNIT 20150701
			$model = new YoyakuSeries_model();
			$row = $model->get_head_row_by_mei_id($param['t_series_mei_id']);
			$t_series_head_id = $row['t_yoyaku_series_lang_id'];	
		} else {
			$result = self::_copy_reference_image($param);
		}
		// Edit end - miyazaki U_SYS - 2014/11/28

		if (ACWError::count() > 0) {
			return ACWView::json(
				array(
					'status' => 'NG'
					,'error' => ACWError::get_list()
				));
		}else{// Add start LIXD-10 Phong VNIT 20150701
			$lib_path = new Path_lib($series_path);
			$lib_path->combine($param['tmp_name']);
			$new_path = $lib_path->get_full_path();
			$file_name = $result['file_name'];
			$file = new File_lib();
			
			$file_thum_name = $file->GetBaseName($file_name) .'_s.'. $file->GetExtensionName($file_name);			
			SeriesFile_lib::copy_new_file($new_path,$new_path.'\/'.$file_name,$new_path.'\/'.$file_thum_name,$param['section_id'],$t_series_head_id,$param['old_name'],$param['sec_no'],$param['yoyaku_flg']);	
		}// Add end LIXD-10 Phong VNIT 20150701

		$result['status'] = 'OK';
		return ACWView::json($result);
	}

	/**
	 * 関連情報画像設定内部処理
	 */
	private static function _copy_reference_image($param)
	{
		$db = new Itemfile_model();
		//Add Start LIXD-74 hungtn VNIT 20150610
        /* chat with yamagami 20150828
		$t_ctg_section_id_list = $db->get_reference_section_all($param['r_mei_id'], $param['section_name'], $param['m_lang_id']);
		if(count($t_ctg_section_id_list) > 0) {
			$t_series_head_id = $db->get_t_series_head_id($param['r_mei_id']);
			if(!empty($t_series_head_id)) {
				$sfile = new SeriesFile_lib($t_series_head_id, $param['r_mei_id']);
				// 画像情報獲得
				$img_data = null;
				foreach($t_ctg_section_id_list as $item) {
					$img_data = $sfile->get_section_image($item);
					if(!empty($img_data)) {
						if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
							continue;
						}
						break;
					}
				}
				if(empty($img_data)) {
					// don't have image => get image normal
					goto getimagenormal;
				}
				// have image
				if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
					goto getimagenormal;
				}
				// コピー実処理
				$sfile->copy_file_to_tmp($param['tmp_name'], $img_data['FileName'], $param['old_name']);

				return array(
					'org_file_name' => $img_data['OrgFileName']
					,'file_name' => $img_data['FileName']
				);
				
			}#end if empty($t_series_head_id
			
		} else {
			getimagenormal:
            */
			$sec_info = $db->get_reference_section($param['r_mei_id']);
			if (is_null($sec_info)) {
				ACWError::add('sec_id', '参照先が存在しませんでした。');
				return null;
			}
			$sfile = new SeriesFile_lib($sec_info['t_series_head_id'], $param['r_mei_id']);
			// 画像情報獲得
			$img_data = $sfile->get_section_image($sec_info['t_ctg_section_id']);
			if (is_null($img_data)) {
				ACWError::add('image', '参照先XMLエラー');
				return null;
			}

			if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
				ACWError::add('image', '参照先に画像が設定されていませんでした。');
				return null;
			}

			// コピー実処理
			$sfile->copy_file_to_tmp($param['tmp_name'], $img_data['FileName'], $param['old_name']);

			return array(
				'org_file_name' => $img_data['OrgFileName']
				,'file_name' => $img_data['FileName']
			);
		//} #end if count $t_ctg_section_id_list
		//Add End LIXD-74 hungtn VNIT 20150610

	}
	
	/**
	 * 関連情報画像設定内部処理
	 * Add - miyazaki U_SYS - 2014/12/01
	 */
	private static function _copy_yoyaku_reference_image($param)
	{
		$db = new Itemfile_model();
		//Add Start LIXD-74 hungtn VNIT 20150610
        /* chat with yamagami 20150828
		$t_ctg_section_id_list = $db->get_reference_section_all($param['r_mei_id'], $param['section_name'], $param['m_lang_id']);
		if(count($t_ctg_section_id_list) > 0) {
			$t_series_head_id = $db->get_t_series_head_id($param['r_mei_id']);
			if(!empty($t_series_head_id)) {
				$sfile = new SeriesFile_lib($t_series_head_id, $param['r_mei_id']);
				// 画像情報獲得
				$img_data = null;
				foreach($t_ctg_section_id_list as $item) {
					$img_data = $sfile->get_section_image($item);
					if(!empty($img_data)) {
						if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
							continue;
						}
						break;
					}
				}
				if(empty($img_data)) {
					// don't have image => get image normal
					goto getimagenormal;
				}
				// have image
				if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
					goto getimagenormal;
				}
				
				$file_lib = new File_lib();
				
				// 既存の画像ファイルを削除
				$tmp_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
				$tmp_lib->combine($param['tmp_name']);
				$tmp_lib->combine($param['old_name']);
				if ($file_lib->FileExists($tmp_lib->get_full_path()) == true) {
					$file_lib->DeleteFile($tmp_lib->get_full_path());
					
					// 既存のサムネイルも削除
					$pinfo = $tmp_lib->get_pathinfo();
					$tmp_lib->move_parent();
					if ($file_lib->FileExists($tmp_lib->get_full_path($pinfo['filename'] . '_s.' . $pinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT)) == true) {
						$file_lib->DeleteFile($tmp_lib->get_full_path($pinfo['filename'] . '_s.' . $pinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT));
					}
				} else {
					if ($param['old_name'] != '') {
						$tmp_lib->move_parent();
					}
				}
				
				// コピーを実行
				$src_lib = new Path_lib($sfile->get_full_path());
				$src_lib->combine($img_data['FileName']);
				$sinfo = $src_lib->get_pathinfo();
				if ($file_lib->FileExists($src_lib->get_full_path()) == true) {
					$file_lib->CopyFile($src_lib->get_full_path(), $tmp_lib->get_full_path($sinfo['basename']));

					// サムネイルのコピーを実行
					$src_lib->move_parent();
					$s_name = $sinfo['filename'] . '_s.' . $sinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT;
					if ($file_lib->FileExists($src_lib->get_full_path($s_name)) == true) {
						$file_lib->CopyFile($src_lib->get_full_path($s_name), $tmp_lib->get_full_path($s_name));
					}
				}

				return array(
					'org_file_name' => $img_data['OrgFileName']
					,'file_name' => $img_data['FileName']
				);
				
			}#end if empty($t_series_head_id
			
		} else {
			getimagenormal:
            */
			$sec_info = $db->get_reference_section($param['r_mei_id']);
			if (is_null($sec_info)) {
				ACWError::add('sec_id', '参照先が存在しませんでした。');
				return null;
			}
			$sfile = new SeriesFile_lib($sec_info['t_series_head_id'], $param['r_mei_id']);
			// 画像情報獲得
			$img_data = $sfile->get_section_image($sec_info['t_ctg_section_id']);
			if (is_null($img_data)) {
				ACWError::add('image', '参照先XMLエラー');
				return null;
			}

			if (isset($img_data['OrgFileName']) == false || isset($img_data['FileName']) == false ) {
				ACWError::add('image', '参照先に画像が設定されていませんでした。');
				return null;
			}

			// コピー実処理
			//$sfile->copy_file_to_tmp($param['tmp_name'], $img_data['FileName'], $param['old_name']);
			
			$file_lib = new File_lib();
			
			// 既存の画像ファイルを削除
			$tmp_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			$tmp_lib->combine($param['tmp_name']);
			$tmp_lib->combine($param['old_name']);
			if ($file_lib->FileExists($tmp_lib->get_full_path()) == true) {
				$file_lib->DeleteFile($tmp_lib->get_full_path());
				
				// 既存のサムネイルも削除
				$pinfo = $tmp_lib->get_pathinfo();
				$tmp_lib->move_parent();
				if ($file_lib->FileExists($tmp_lib->get_full_path($pinfo['filename'] . '_s.' . $pinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT)) == true) {
					$file_lib->DeleteFile($tmp_lib->get_full_path($pinfo['filename'] . '_s.' . $pinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT));
				}
			} else {
				if ($param['old_name'] != '') {
					$tmp_lib->move_parent();
				}
			}
			
			// コピーを実行
			$src_lib = new Path_lib($sfile->get_full_path());
			$src_lib->combine($img_data['FileName']);
			$sinfo = $src_lib->get_pathinfo();
			if ($file_lib->FileExists($src_lib->get_full_path()) == true) {
				$file_lib->CopyFile($src_lib->get_full_path(), $tmp_lib->get_full_path($sinfo['basename']));

				// サムネイルのコピーを実行
				$src_lib->move_parent();
				$s_name = $sinfo['filename'] . '_s.' . $sinfo['extension'] . '.' . THUMBNAIL_IMAGE_FORMATT;
				if ($file_lib->FileExists($src_lib->get_full_path($s_name)) == true) {
					$file_lib->CopyFile($src_lib->get_full_path($s_name), $tmp_lib->get_full_path($s_name));
				}
			}

			return array(
				'org_file_name' => $img_data['OrgFileName']
				,'file_name' => $img_data['FileName']
			);
		
		//}//Add Start LIXD-74 hungtn VNIT 20150610
	}

	/*
	 * ヘッダーエクセルを変換する
	 */
	private static function _execute_header_excel(&$param)
	{
		$src_file = $param['tmppath']->get_full_path();
		/*
		 * ファイル情報
		 */
		$org_pi = Path_lib::info($param['org_header_file_name']);
		$src_pi = pathinfo($src_file);

		$tmpdir = new Path_lib($src_file);
		$tmpdir->move_parent();	// ディレクトリ

		$auto_excel_name =  $src_pi['filename']  . '_auto.xlsx';

		/*
		 * 変換用情報
		 */
		$cvt_table = ItemNo_common_model::get_convert_table_all($param['lang_id']);
		// ヘッダーEXCELの場合
		$hexl = new HeaderExcel_lib();
		// Add start - miyazaki U_SYS - 2015/01/14
		// NBKD-757　項目名取得
		if ($param['free_flg'] == '1') {
			$section_name = $param['section_name'];
		} else {
			$model = new Itemfile_model();
			$section_name = $model->get_section_name($param);
		}
		// Add end - miyazaki U_SYS - 2015/01/14
		/*
		 * アップロードされたファイルから読み込み
		 */
		if ($hexl->make_header_data($src_file, $cvt_table, $section_name) == false) { // Edit - miyazaki U_SYS - 2015/01/14
			return false;	// 失敗
		}
		/*
		 * SQL実行
		 */
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			ItemNo_common_model::exec_header_sql($hexl->get_data(), $param['lang_id'] ,true);
		} else {
			ItemNo_common_model::exec_header_sql($hexl->get_data(), $param['lang_id']);
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		/*
		 * 作成したEXCELを保存
		 */
		$hexl->save($tmpdir->get_full_path($auto_excel_name), $param);//Edit - NBKD-73 - TrungVNIT - 2014/12/03
		/*
		 * HTML変換
		 */
		$execl = new Excel_lib();
		$execl->to_html($tmpdir->get_full_path($auto_excel_name), $tmpdir->get_full_path($src_pi['filename'] . '_auto.html'));
		$param['org_file_name'] = $org_pi['filename'] . '_auto.xlsx';
		$param['file_name'] = $auto_excel_name;

        $execl->free(); //Edit LIXD-321 hungtn VNIT 20151111
		return true;
	}

	/**
	 * 商品品番情報、アップロード
	 */
	public static function action_uploadno()
	{
		$param = self::get_param(array(
			'item_no_file',
			'm_lang_id',
			't_series_head_id',
			't_series_mei_id',
			'yoyaku_flg'	// Add - miyazaki U_SYS - 2014/11/19
		));
		$result = array();
		$file = new File_lib();
		
		if (self::get_validate_result() == false) {
			$result['status'] = 'NG';
			
			foreach (ACWError::get_list() as $error) {
				$result['error'][] = $error['info'];
			}
		} else {
			$db = new Itemfile_model();
			$db->begin_transaction();
			
			// ロックチェック
			$lock_res = $db->lock_series_mei($param);
			if ($lock_res !== true) {
				$result['status'] = 'NG';
				$result['error'][] = $lock_res;
				$db->commit();
			} else {
				$import_result = ItemNo_common_model::set_import_data($param);

				if ($import_result == null) {
					// 正常終了
					$result['status'] = 'OK';

					// 品番アップロードが正常終了して、再読み込みも行われない場合、文字列を返さない
					$res = self::_upload_success_after($param);
					if (empty($res['res']) == false) {
						$result['error'] = $res['res'];
					}

					if ($res['rollback']) {
						$db->rollback();
						$result['status'] = 'NG';
					} else {
						$db->commit();
						
						// Edit start - miyazaki U_SYS - 2014/11/19
						if ((isset($param['yoyaku_flg']) == false) || ($param['yoyaku_flg'] == '')) {
							//Edit start - miyazaki U_SYS - 2014/10/17
							// 他言語の処理を実行
							$res_other = $db->upload_no_other_lang($param);
							$result['other_error'] = $res_other;
							//Edit end - miyazaki U_SYS - 2014/10/17
						}
						// Edit end - miyazaki U_SYS - 2014/11/19
                        //Edit start - NBKD-1069 - TrungVNIT - 2015/03/20
                        else{
                            $param['yoyaku_flg'] = 1;
                            $res_other = $db->upload_no_other_lang($param);
                            $result['other_error'] = $res_other;
                        }
                        //Edit end - NBKD-1069 - TrungVNIT - 2015/03/20
					}
				} else if (strpos($import_result, 'WARN:') !== false) {
					// 警告（正常終了だが、メッセージ表示をしたい）
					$result['status'] = 'OK';
					$result['error']['warn'] = substr($import_result, 5);

					// 品番アップロード警告終了した場合は、結合して返す
					$res = self::_upload_success_after($param);
					$result['error'] = ACWArray::merge($result['error'], $res['res']);

					if ($res['rollback']) {
						$db->rollback();
						$result['status'] = 'NG';
					} else {
						$db->commit();
					}
				} else {
					// 取込エラー
					$result['status'] = 'NG';
					$result['error'] = array($import_result);
				}
			}
			//Move - miyazaki U_SYS - 2014/10/17
			$db->unlock_series_mei($param);
		}

		// 一時フォルダ削除
		if (isset($param['tmp_folder_path'])) {
			$file->DeleteFolder($param['tmp_folder_path']);
		}
			
		return ACWView::json($result);
	}

	
	/*
	 * 正常終了後動作（アップロードファイルのコピー、ヘッダーフォーマット再読み込み）
	 */
	private static function _upload_success_after($params)
	{
		// アップロードファイルのコピー
		$file = new File_lib();
		$file->CopyFile($params['item_no_path'], $params['copy_no_path']);
		
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($params['yoyaku_flg']) == true) && ($params['yoyaku_flg'] != '')) {
			$model = new YoyakuSeries_model();
			$series_lang_rows = $model->get_series_lang_rows($params['t_series_head_id'], $params['m_lang_id']);
			$series_file = new YoyakuSeriesFile_lib($params["t_series_head_id"], $series_lang_rows[0]['t_series_mei_id']);

			// テンポラリフォルダを作成してコピーする
			$tmp_name = YoyakuSeriesFile_lib::make_tmp($series_lang_rows[0]['t_series_mei_id']);
			$series_file->copy_to_tmp($tmp_name);
			
			$tmp_path = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		} else {
			$model = new Series_model();
			$series_lang_rows = $model->get_series_lang_rows($params['t_series_head_id'], $params['m_lang_id']);
			$series_file = new SeriesFile_lib($params["t_series_head_id"], $series_lang_rows[0]['t_series_mei_id']);

			// テンポラリフォルダを作成してコピーする
			$tmp_name = SeriesFile_lib::make_tmp($series_lang_rows[0]['t_series_mei_id']);
			$series_file->copy_to_tmp($tmp_name);
			
			$tmp_path = new Path_lib(AKAGANE_SERIES_TMP_PATH);
		}
		// Edit end - miyazaki U_SYS - 2014/11/19

		
		$rtn_msg = array();
		$rtn_msg['res'] = array();
		$rtn_msg['rollback'] = true;
		
		// 更新日時のチェック
//		if ($series_file->check_update_time($params['xml_update_time']) == false) {
//			$rtn_msg['res']['message'] = '品番アップロード中に他のユーザによって変更されたため、処理を終了しました';
//			return $rtn_msg;
//		} else {
//			$params['xml_update_time'] = $series_file->set_update_time();
//		}

		
		// シリーズXMLの読み込み
		$sec_xml = $series_file->get_xml_section();
		if (is_null($sec_xml)) {
			$rtn_msg['res']['message'] = 'XMLの読み込みに失敗しました';
			return $rtn_msg;
		}
		$free_xml = $series_file->get_xml_free();
		if (is_null($free_xml)) {
			$rtn_msg['res']['message'] = 'XMLの読み込みに失敗しました';
			return $rtn_msg;
		}
		
		// tmpディレクトリパス作成
		$tmp_path->combine($tmp_name);		
		$xml_path = $tmp_path->get_full_path() . '/' . AKAGANE_SERIES_XML_NAME;
		
		$res_count = 0;
		$res_count += self::_reload_excel_header($sec_xml, $tmp_name, $params['m_lang_id'], false, $xml_path, $params['yoyaku_flg']);
		$res_count += self::_reload_excel_header($free_xml, $tmp_name, $params['m_lang_id'], true, $xml_path, $params['yoyaku_flg']);
		
//		// 更新日時のチェック
//		if ($series_file->check_update_time($params['xml_update_time']) == false) {
//			$rtn_msg['res']['message'] = '品番アップロード中に他のユーザによって変更されたため、処理を終了しました';
//			return $rtn_msg;
//		}
		
		// テンポラリフォルダから戻す(テンポラリフォルダは削除される)
		$series_file->move_tmp_dir($tmp_name);
		$rtn_msg['rollback'] = false;
		
		// ヘッダー再読み込みが行われた場合、メッセージを表示
		if ($res_count > 0) {
			$rtn_msg['res']['message'] = 'ヘッダーフォーマットエクセルの再作成を実行しました';
		}		
		
		return $rtn_msg;
	}
	
	private static function _reload_excel_header($xml, $tmp_name, $lang_id, $free_flg, $xml_path, $yoayku_flg = '')		// Edit end - miyazaki U_SYS - 2014/11/19
	{
		// ヘッダー読み込みパラメータ
		$head_param = array(
			 'tmppath' => ''
			,'org_header_file_name' => ''
			,'lang_id' => $lang_id
			,'yoyaku_flg' => $yoayku_flg// Add - miyazaki U_SYS - 2014/11/20
		);
		
		$file = new File_lib();
		
		$count = 0;
		
		// XMLの読み込み
		$doc = new DOMDocument('1.0', 'UTF-8');
		$doc->preserveWhiteSpace = false;	// これを書かないと崩れる
		$doc->formatOutput = true;			// 整形
		$doc->load($xml_path);
		
		$xpath = new DOMXPath($doc);
		
		if ($free_flg) {
			$query_path = '/XML/Free/FreeSection/Data[@DataId="%s"]/%s';
		} else {
			$query_path = '/XML/SeriesHead/SeriesHeadSection/SectionInfo/Data[@DataId="%s"]/%s';
		}
		
		// セクションループ
		foreach ($xml as $x) {
			// データタグをループ
			foreach ($x['Data'] as $data) {
				// データ区分が"6"（ヘッダーエクセル）のDataの場合、処理を続行
				if (strcmp($data['DataKbn'], AKAGANE_SECTION_KBN_HEADER_EXCEL) == 0) {
					// ヘッダーファイルパス
					// Edit start - miyazaki U_SYS - 2014/11/19
					if ($yoayku_flg != '') {
						$head_param['tmppath'] = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
					} else {
						$head_param['tmppath'] = new Path_lib(AKAGANE_SERIES_TMP_PATH);
					}
					// Edit end - miyazaki U_SYS - 2014/11/19
					
					$head_param['tmppath']->combine($tmp_name);
					if ($free_flg) {
						// フリーは1つ階層が深いため、idをパスに結合
						$head_param['tmppath']->combine($x['FreeItemId']);
					}
					$head_param['tmppath']->combine($data['HeaderFileName']);
					$head_param['org_header_file_name'] = $data['OrgHeaderFileName'];
					
					if ($file->FileExists($head_param['tmppath']->get_full_path())) {
						// エラー初期化
						ACWError::clear();
						 // Add start - miyazaki U_SYS - 2015/01/14
						if ($free_flg == true) {
							$head_param['t_ctg_section_id'] = $x['FreeItemId'];
						} else {
							$head_param['t_ctg_section_id'] = $x['SectionId'];
						}
						$head_param['free_flg'] = $free_flg;
						$head_param['section_name'] = $x['SectionName'];
						 // Add end - miyazaki U_SYS - 2015/01/14
						
						// ヘッダー読み込み
						self::_execute_header_excel($head_param);
						$count++;
						
						if (ACWError::count() > 0) {
							// エラー発生
							ACWLog::debug_var('HEADER_EXCEL_AUTO', $head_param['tmppath']->get_full_path());
							ACWLog::debug_var('HEADER_EXCEL_AUTO', 'データID「' . $data['DataId'] . '」でのヘッダーフォーマット取込時にエラーが発生しました。');
							ACWLog::debug_var('HEADER_EXCEL_AUTO', ACWError::get_list());
							
							// 以前のデータを削除する（xlsx）
							$head_param['tmppath']->move_parent();
							$head_param['tmppath']->combine($data['FileName']);
							
							if ($file->FileExists($head_param['tmppath']->get_full_path())) {
								$file->DeleteFile($head_param['tmppath']->get_full_path());
							}
							
							// 以前のデータを削除する（html）
							$html_path = str_replace('.xlsx', '.html', $head_param['tmppath']->get_full_path());
							
							if ($file->FileExists($html_path)) {
								$file->DeleteFile($html_path);
							}
						} else {
							// 正常終了 → ファイル名を更新
							$oid = uniqid('', true);
							$ext = $file->GetExtensionName($data['HeaderFileName']);
							
							// 保存フォルダ
							$parnet_path = $head_param['tmppath']->move_parent()->get_full_path();
							
							// ヘッダーファイルの名前の変更(xlsx)
							$old_head_path = $parnet_path . '/' . $data['HeaderFileName'];
							$new_head_path = $parnet_path . '/' . $oid . '.' . $ext;
							rename($old_head_path, $new_head_path);
							
							// ヘッダーファイルの名前の変更(html)
							$old_head_html_path = str_replace('.xlsx', '.html', $old_head_path);
							$new_head_html_path = str_replace('.xlsx', '.html', $new_head_path);
							rename($old_head_html_path, $new_head_html_path);
							
							// xmlのHeaderFileNameの変更
							$data_head_path = sprintf($query_path, $data['DataId'], 'HeaderFileName');
							$data_head_node = $xpath->query($data_head_path)->item(0);					
							$data_head_node->nodeValue = $oid . '.' . $ext;
							
							// 出力ファイルの名前の変更(xlsx)
							$old_path = $parnet_path . '/' . $data['FileName'];
							$new_path = $parnet_path . '/' . $oid . '_auto.' . $ext;
							rename($old_path, $new_path);
							
							// 出力ファイルの名前の変更(html)
							$old_html_path = str_replace('.xlsx', '.html', $old_path);
							$new_html_path = str_replace('.xlsx', '.html', $new_path);
							rename($old_html_path, $new_html_path);
							
							// xmlのFileNameの変更
							$data_path = sprintf($query_path, $data['DataId'], 'FileName');
							$data_node = $xpath->query($data_path)->item(0);					
							$data_node->nodeValue = $oid . '_auto.' . $ext;
						}
					}
				}
			}
		}	
		
		$doc->save($xml_path);
		
		unset($xpath);
		unset($doc);
		
		return $count;
	}

	/**
	 * 入力チェック
	 */
	public static function validate($action, &$param)
	{
		if ($action == 'upload' || $action == 'yoyakuupload') {
			return self::_validate_upload($param);
		} else if ($action == 'uploadno') {
			return self::_validate_uploadno($param);
		} else if ($action == 'checkmultidl') {
			return self::_validate_checkmultidl($param);
		}
	}

	/*
	 * アップロード時のチェック
	 */
	private static function _validate_upload(&$param)
	{
		if (isset($param['series_file']) == false) {
			ACWError::add('series_file', 'アップロードファイルが指定されていません。');
			return false;
		}
		$model = new Itemfile_model();
		ACWLog::debug_var('UPLOAD_PARAM', $param);
		// Add Start LIXD-9 hungtn VNIT 20150622
		$arr_result = NULL;
		$tmp_parram = NULL;
		$result = false;
		if(isset($param['uptype']) && $param['uptype'] == "multiimgup") {
			if(count($param['series_file']['name']) > 1) {
				for($i = 0; $i < count($param['series_file']['name']); $i++ ) {
					$tmp_parram = $param;
					$tmp_parram['series_file']['error'] = $param['series_file']['error'][$i];
					$tmp_parram['series_file']['name'] = $param['series_file']['name'][$i];
					$tmp_parram['series_file']['size'] = $param['series_file']['size'][$i];
					$tmp_parram['series_file']['tmp_name'] = $param['series_file']['tmp_name'][$i];
					$tmp_parram['series_file']['type'] = $param['series_file']['type'][$i];
					$arr_result[] = $model->upload_validate_detail($tmp_parram);
					$param['upload'][] = $tmp_parram['upload'];
					$param['tmppath'][] = $tmp_parram['tmppath'];
					$param['org_file_name'][] = $tmp_parram['org_file_name'];
					$param['file_name'][] = $tmp_parram['file_name'];
					$param['org_header_file_name'][] = $tmp_parram['org_header_file_name'];
					$param['header_file_name'][] = $tmp_parram['header_file_name'];
				}
				
				if(count($arr_result) > 0) {
					foreach($arr_result as $item) {
						if($item == true) $result = true;	
					}
					// All false -> return false
				}
			} else {
				$tmp_parram = $param;
				$tmp_parram['series_file']['error'] = $param['series_file']['error'][0];
				$tmp_parram['series_file']['name'] = $param['series_file']['name'][0];
				$tmp_parram['series_file']['size'] = $param['series_file']['size'][0];
				$tmp_parram['series_file']['tmp_name'] = $param['series_file']['tmp_name'][0];
				$tmp_parram['series_file']['type'] = $param['series_file']['type'][0];
				$result = $model->upload_validate_detail($tmp_parram);
				$param['upload'][] = $tmp_parram['upload'];
				$param['tmppath'][] = $tmp_parram['tmppath'];
				$param['org_file_name'][] = $tmp_parram['org_file_name'];
				$param['file_name'][] = $tmp_parram['file_name'];
				$param['org_header_file_name'][] = $tmp_parram['org_header_file_name'];
				$param['header_file_name'][] = $tmp_parram['header_file_name'];
			}
			
			return $result;
		} else {
			return $model->upload_validate_detail($param);
		}
		// Add End LIXD-9 hungtn VNIT 20150622
	}

	// Add Start LIXD-9 hungtn VNIT 20150622
	private function upload_validate_detail(&$param) {
		$upl = new Upload_lib($param['series_file']);
		if ($upl->get_ext() == '') {
			ACWError::add('series_file', '拡張子がないファイルはアップロードできません。');
			return false;
		}
		if ($upl->get_error() != 0) {
			ACWError::add('series_file', 'アップロードに失敗しました。');
			return false;
		}
		// テンポラリ
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$tmppath = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		} else {
			$tmppath = new Path_lib(AKAGANE_SERIES_TMP_PATH);
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		$tmppath->combine($param['tmp_name']);
		// ランダムなファイル名を付ける
		$rndfilename = $tmppath->add_random_filename('', $upl->get_ext(),TRUE);//edit LIXD-9 Phong VNIT-20150624		
		// 移動
		if ($upl->move_to($tmppath->get_full_path()) == false) {
			ACWError::add('series_file', 'アップロードファイルの移動に失敗しました。');
			return false;
		}
		$param['upload'] = $upl;
		$param['tmppath'] = $tmppath;
		if ($param['uptype'] == 'hdrexcelup') {
			/*
			 * ヘッダーアップロード
			 */
			$param['org_file_name'] = '';
			$param['file_name'] = '';
			$param['org_header_file_name'] = $upl->get_name();
			$param['header_file_name'] = $rndfilename;
		} else {
			$param['org_file_name'] = $upl->get_name();
			$param['file_name'] = $rndfilename;
			$param['org_header_file_name'] = '';
			$param['header_file_name'] = '';
		}
		return true;
	}
	// Add End LIXD-9 hungtn VNIT 20150622
	
	/*
	 * アップロード時のチェック
	*/
	private static function _validate_uploadno(&$param)
	{
		if (isset($param['item_no_file']) == false) {
			ACWError::add('message', 'アップロードファイルが指定されていません。');
			return false;
		}

		$upl = new Upload_lib($param['item_no_file']);
		if ($upl->get_ext() == '') {
			ACWError::add('message', '拡張子がないファイルはアップロードできません。');
			return false;
		}
		if ($upl->get_error() != 0) {
			ACWError::add('message', 'アップロードに失敗しました。');
			return false;
		}
		
		// Edit start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$model = new YoyakuSeries_model();
			$series_lang_rows = $model->get_series_lang_rows($param['t_series_head_id'], $param['m_lang_id']);
			
			// 成功時の移動用パスを作成する
			$series_file = new YoyakuSeriesFile_lib($param['t_series_head_id'], $series_lang_rows[0]['t_series_mei_id']);
			
			$tmp_name = YoyakuSeriesFile_lib::make_tmp($series_lang_rows[0]['t_series_mei_id']);
			$tmp_path = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			
			$t_series_lang_id = $series_lang_rows[0]['t_yoyaku_series_lang_id'];
		} else {
			$model = new Series_model();
			$series_lang_rows = $model->get_series_lang_rows($param['t_series_head_id'], $param['m_lang_id']);
			
			// 成功時の移動用パスを作成する
			$series_file = new SeriesFile_lib($param['t_series_head_id'], $series_lang_rows[0]['t_series_mei_id']);
			
			$tmp_name = SeriesFile_lib::make_tmp($series_lang_rows[0]['t_series_mei_id']);
			$tmp_path = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			
			$t_series_lang_id = $series_lang_rows[0]['t_series_lang_id'];
		}
		// Edit end - miyazaki U_SYS - 2014/11/19
		
		$item_no_path = new Path_lib($series_file->get_full_path());
		$item_no_path->combine('item_no.xlsx');
		
		// 更新日時を取得する
		$param['xml_update_time'] = $series_file->get_update_time();
		
		// アップロード用の一時フォルダを作成する
		$tmp_path->combine($tmp_name);
		$param['tmp_folder_path'] = $tmp_path->get_full_path();
		$tmp_path->combine('item_no.xlsx');

		// 移動
		if ($upl->move_to($tmp_path->get_full_path()) == false) {
			ACWError::add('message', 'アップロードファイルの移動に失敗しました。');
			return false;
		}

		$param['copy_no_path'] = $item_no_path->get_full_path();
		$param['item_no_path'] = $tmp_path->get_full_path();
		$param['t_series_lang_id'] = $t_series_lang_id;
                
		return true;
	}

	/**
	 * ダウンロードファイル情報取得
	 */
    public function get_typeset_files($mei_id, $m_media_type_id)
    {
        // Edit Start LIXD-18 hungtn VNIT 20150813
        $sql = "
            SELECT
                 zip_file_path
                ,pdf_file_path
                ,log_file_path
                ,t_req_id
                ,object_storage_flg -- Add NBKD-1033 TinVNIT 3/6/2015
                ,template_id
                ,major_ver
                ,minor_ver
            FROM
                t_typeset_files
            LEFT JOIN m_template_head on (t_typeset_files.m_template_id = m_template_head.m_template_id)
            WHERE
                t_series_ver_id IN (SELECT t_series_ver_id FROM t_series_mei WHERE t_series_mei_id = :mei_id AND del_flg = 0)
                AND t_typeset_files.m_media_type_id = :m_media_type_id
        ";
        return $this->query($sql, array('mei_id' => $mei_id, 'm_media_type_id' => intval($m_media_type_id)));
        // Edit End LIXD-18 hungtn VNIT 20150813
    }
	/**
	 * ダウンロードファイル情報取得
	 * Add - miyazaki U_SYS - 2014/11/20
	 */
	public function get_yoyaku_typeset_files($mei_id, $m_media_type_id) // Edit Start LIXD-18 hungtn VNIT 20150813
	{
		$sql = "
			SELECT
				 zip_file_path
				,pdf_file_path
				,log_file_path
				,t_req_id
				,object_storage_flg -- Add NBKD-1033 TinVNIT 3/6/2015
				,template_id
                ,major_ver
                ,minor_ver
			FROM
				t_typeset_files
			LEFT JOIN m_template_head on (t_typeset_files.m_template_id = m_template_head.m_template_id)
			WHERE
				t_series_ver_id IN (SELECT t_yoyaku_series_ver_id FROM t_yoyaku_series_mei WHERE t_yoyaku_series_mei_id = :mei_id)
				AND t_typeset_files.m_media_type_id = :m_media_type_id
		";
		return $this->query($sql, array('mei_id' => $mei_id, 'm_media_type_id' => intval($m_media_type_id)));
	}// Edit End LIXD-18 hungtn VNIT 20150813

	/**
	 * 関連情報参照先のt_ctg_section_idを取る
	 */
	public function get_reference_section($mei_id)
	{
		$sql = "
			SELECT
				csec.t_ctg_section_id
				,shead.t_series_head_id
			FROM
				t_ctg_section csec
			INNER JOIN
				t_series_head shead ON shead.t_ctg_head_id = csec.t_ctg_head_id AND shead.del_flg = 0
			INNER JOIN
				t_series_lang slang ON slang.t_series_head_id = shead.t_series_head_id AND slang.del_flg = 0
			INNER JOIN
				t_series_ver sver ON sver.t_series_lang_id = slang.t_series_lang_id AND sver.del_flg = 0
			INNER JOIN
				t_series_mei smei ON smei.t_series_ver_id = sver.t_series_ver_id AND smei.del_flg = 0 AND smei.t_series_mei_id = :mei_id
			WHERE
				csec.reference_flg = 1
		";

		$result = $this->query($sql, array('mei_id' => $mei_id));
		if (count($result) == 0) {
			return null;
		}

		return $result[0];
	}
	
	// Add Start LIXD-74 hungtn VNIT 20150609
    	public function get_t_series_head_id ($imei) {
		$sql = "
			SELECT shead.t_series_head_id FROM
			t_series_head shead
			INNER JOIN
				t_series_lang slang ON slang.t_series_head_id = shead.t_series_head_id AND slang.del_flg = 0
			INNER JOIN
				t_series_ver sver ON sver.t_series_lang_id = slang.t_series_lang_id AND sver.del_flg = 0
			INNER JOIN
				t_series_mei smei ON smei.t_series_ver_id = sver.t_series_ver_id AND smei.del_flg = 0 AND smei.t_series_mei_id = :mei_id";
		$result = $this->query($sql, array('mei_id' => $imei));
		if(count($result) == 0) {
			return null;
		}
		return $result[0]['t_series_head_id'];
	}
    /* chat with yamagami 20150828
	public function get_reference_section_all($mei_id, $section_name, $m_lang_id)
	{
		$sql = "
			SELECT m_section_id,t_ctg_section_id from 
			(
			select m_section_id,t_ctg_section_id FROM t_section_trans
			 WHERE t_section_trans.m_lang_id = :m_lang_id AND t_section_trans.section_name = :section_name AND t_section_trans.m_section_id in (
			SELECT
				csec.m_section_id
				--,shead.t_series_head_id
			FROM
				t_ctg_section csec
			INNER JOIN
				t_series_head shead ON shead.t_ctg_head_id = csec.t_ctg_head_id AND shead.del_flg = 0
			INNER JOIN
				t_series_lang slang ON slang.t_series_head_id = shead.t_series_head_id AND slang.del_flg = 0
			INNER JOIN
				t_series_ver sver ON sver.t_series_lang_id = slang.t_series_lang_id AND sver.del_flg = 0
			INNER JOIN
				t_series_mei smei ON smei.t_series_ver_id = sver.t_series_ver_id AND smei.del_flg = 0 AND smei.t_series_mei_id = :mei_id
			)
			UNION
			select m_section_id,t_ctg_section_id FROM t_section_trans 
			WHERE t_section_trans.m_lang_id = :m_lang_id AND t_section_trans.section_name = :section_name AND t_section_trans.t_ctg_section_id in (
			SELECT
				csec.t_ctg_section_id
				--,shead.t_series_head_id
			FROM
				t_ctg_section csec
			INNER JOIN
				t_series_head shead ON shead.t_ctg_head_id = csec.t_ctg_head_id AND shead.del_flg = 0
			INNER JOIN
				t_series_lang slang ON slang.t_series_head_id = shead.t_series_head_id AND slang.del_flg = 0
			INNER JOIN
				t_series_ver sver ON sver.t_series_lang_id = slang.t_series_lang_id AND sver.del_flg = 0
			INNER JOIN
				t_series_mei smei ON smei.t_series_ver_id = sver.t_series_ver_id AND smei.del_flg = 0 AND smei.t_series_mei_id = :mei_id
			)
			) t
		";
		
		$result = $this->query($sql, array('mei_id' => $mei_id, 'section_name' => $section_name, 'm_lang_id' => $m_lang_id));
		
		
		$ctg_section_id_list = null;
		if (count($result) == 0) {
			return null;
		} else {
			// get t_ctg_section_id section_kbn = 2
			foreach ($result as $item) {
				if(empty($item['m_section_id'])) {
					// get_t_ctg_section_id and check section_kbn is image type
					$tmp = $this->get_t_ctg_section_id($item['t_ctg_section_id'], FALSE); // m_section_id is empty
					if(!empty($tmp)) {
						$ctg_section_id_list[] = $tmp;	
					}
					
				} else {
					// get_t_ctg_section_id and check section_kbn is image type
					$tmp = $this->get_t_ctg_section_id($item['m_section_id'], TRUE, $mei_id); // had m_section_id // Edit LIXD-74 hungtn VNIT 20150702
					if(!empty($tmp)) {
						$ctg_section_id_list[] = $tmp;	
					}
				}
			}#end foreach
		}
		
		if (count($ctg_section_id_list) == 0) {
			return null;
		}

		return $ctg_section_id_list;
	}
	

	
	private function get_t_ctg_section_id ($value, $m_sectio_id_flg = TRUE, $mei_id = null) {// Edit Start LIXD-74 hungtn VNIT 20150702
		if($m_sectio_id_flg === true) {
			
			$sql = "select t_ctg_section_id FROM t_ctg_section WHERE section_kbn = 2 AND m_section_id = :m_section_id 
			AND t_ctg_head_id IN(
			SELECT t_ctg_head_id FROM t_series_mei sm
			JOIN t_series_ver sv on (sm.t_series_ver_id = sv.t_series_ver_id)
			JOIN t_series_lang sl ON (sv.t_series_lang_id = sl.t_series_lang_id)
			JOIN t_series_head sh ON (sl.t_series_head_id = sh.t_series_head_id)
			WHERE sm.t_series_mei_id = :mei_id
			)
			";
			
			$param = array('m_section_id' => $value, 'mei_id' => $mei_id);// Edit End LIXD-74 hungtn VNIT 20150702
			$result = $this->query($sql, $param);
			if(count($result) > 0) {
				return $result[0]['t_ctg_section_id'];
			} else {
				return null;
			}	
		} else {
			$sql = "select t_ctg_section_id FROM t_ctg_section WHERE section_kbn = 2 AND t_ctg_section_id = :t_ctg_section_id";
			$param = array('t_ctg_section_id' => $value);
			$result = $this->query($sql, $param);
			if(count($result) > 0) {
				return $result[0]['t_ctg_section_id'];
			} else {
				return null;
			}
		}
		
	}
    */
	// Add End LIXD-74 hungtn VNIT 20150609
	
	/**
	 * 素材の一括DL選択画面表示
	 */
	public static function action_viewdl()
	{
		// Edit start - miyazaki U_SYS - 2014/11/19
		$param = self::get_param(
			array('t_series_head_id', 't_series_mei_id', 'm_lang_id', 't_ctg_head_id', 'data_kbn', 'yoyaku_flg','object_storage_flg')//add NBKD-1033 Phong VNIT 2015/03/10
		);
				
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			// 一時フォルダを作成
			$tmp_name = YoyakuSeriesFile_lib::make_tmp($param['t_series_mei_id']);

			// 一時フォルダへ退避
			$ser_lib = new YoyakuSeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);			
			//edit start NBKD-1033 Phong VNIT 2015/03/10						
			$ser_model = new YoyakuSeries_model();
			$row = $ser_model->get_head_row_by_mei_id($param['t_series_mei_id']);
			$param['t_ctg_head_id'] = $row['t_ctg_head_id'];
			
			$model = new Itemfile_model();
			$xml_data = $model->get_xml_data($param, $ser_lib);
			$free_data = $xml_data['free_data'];
			$list_file = $model->getlist_filename($xml_data['section_data'],$free_data);
			if (isset($param['object_storage_flg'])==true && $param['object_storage_flg']=='1'){
				foreach($free_data as $row_free){
					YoyakuSeriesFile_lib::make_tmp_free($tmp_name,$row_free['id']);					
				}
				$ser_lib->copy_obts_to_tmp($tmp_name,$param['m_lang_id'],$list_file);			
			}else{
				$ser_lib->copy_to_tmp($tmp_name);				
			}
			//edit end NBKD-1033 Phong VNIT 2015/03/10
		} else {
			// 一時フォルダを作成
			$tmp_name = SeriesFile_lib::make_tmp($param['t_series_mei_id']);

			// 一時フォルダへ退避
			$ser_lib = new SeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);			
			//edit start NBKD-1033 Phong VNIT 2015/03/10
			$model = new Itemfile_model();
			$xml_data = $model->get_xml_data($param, $ser_lib);
			$free_data = $xml_data['free_data'];
			$list_file = $model->getlist_filename($xml_data['section_data'],$free_data);
			
			if (isset($param['object_storage_flg'])==true && $param['object_storage_flg']=='1'){
				foreach($free_data as $row_free){
					SeriesFile_lib::make_tmp_free($tmp_name,$row_free['id']);					
				}
				$ser_lib->copy_obts_to_tmp($tmp_name,$param['m_lang_id'],$list_file);	
			}else{
				$ser_lib->copy_to_tmp($tmp_name);				
			}//edit end NBKD-1033 Phong VNIT 2015/03/10
		}
		// Edit end - miyazaki U_SYS - 2014/11/19

		// XMLデータ取得
		/*$model = new Itemfile_model();
		$xml_data = $model->get_xml_data($param, $ser_lib);*/
		$res_param = ACWArray::merge($param, $xml_data);
		$res_param['tmp_name'] = $tmp_name;
		
		//Add start - miyazaki U_SYS - 2014/10/14
		$sozai_count = $model->get_sozai_count($res_param['section_data']);
		$free_sozai_count = $model->get_sozai_count($res_param['free_data']);
		$res_param['sozai_count'] = $sozai_count + $free_sozai_count;
		//Add end - miyazaki U_SYS - 2014/10/14
		
		// Add start - miyazaki U_SYS - 2014/11/19
		if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
			$res_param['yoyaku_flg'] = 1;
		}
		// Add end - miyazaki U_SYS - 2014/11/19
		//add start  LIXD-354 Phong VNIT-20151118
		$series = new Series_model();
		$list_tag = $series->get_list_tag($param['t_series_mei_id'], $param['m_lang_id'], $param['t_series_head_id']);
		foreach ($list_tag as $section_tag){
	       	foreach ($section_tag as $tag){	       		
	       		if($tag['excel_flg']=='YES'){
	       			if($tag['free_flg']=='YES'){
						Itemfile_model::excel_to_html($tmp_name,$tag['image_tmp_file'],$tag['t_ctg_section_id'],$res_param['yoyaku_flg']);
					}else{
						Itemfile_model::excel_to_html($tmp_name,$tag['image_tmp_file'],NULL,$res_param['yoyaku_flg']);
					}
					
				}
	       	}	
	    }	
        $array_tag = array('t_series_head_id' => $param['t_series_head_id'], 't_series_mei_id' => $param['t_series_mei_id']);
        if(isset($res_param['section_data'])){
            $res_param['section_data'] = $series->merge_list_tag($res_param['section_data'], $list_tag, $array_tag);
        }        
        if(isset($res_param['free_data'])){
            $res_param['free_data'] = $series->merge_list_tag($res_param['free_data'], $list_tag, $array_tag, TRUE);
        }
        //add end LIXD-354 Phong VNIT-20151118
		return ACWView::template('series/view_multidl.html', $res_param);
	}
	
    //Add Start LIXD-357 hungtn VNIT 20151123
    public static function action_viewdlexcel()
	{
        $data = self::filter_data_ex($_POST);
        
        
		return ACWView::template('series/view_multidl_excel.html', array('data' => $data));
	}
    public static function filter_data_ex($data) {
        
        $tmp_folder = '';
        if(isset($data['tmp_folder'])) {
            $tmp_folder = $data['tmp_folder'];
            unset($data['tmp_folder']);
        }
        
        $return_arr = array();
        if(count($data) > 0) {
            foreach($data as $key => $value) {
                $tmp = explode("_lable_img_", $key);
                $return_arr[$tmp[1]][$tmp[0]] = $value;
                if($tmp[0] == 'data_org') {
                    $ext = pathinfo($value, PATHINFO_EXTENSION);
                    $return_arr[$tmp[1]]['ext'] = $ext;
                }
            }
        }
       
        if(count($return_arr) > 0) {
			$path = AKAGANE_SERIES_TMP_PATH . '/' . $tmp_folder;
			$list = SeriesFile_lib::get_full_image($path);
			$list_image = isset($list['image']) ? $list['image'] :  array();

			$fileLib = new File_lib();
            
			$return = array();
            foreach($return_arr as $key => $item) {
				if($key == 'parent'){
					$return[$key] = $item;
				}else{
                    //Edit Start LIXD-393 hungtn VNIT 20151216
                    if($item['ext'] == 'xlsx' || $item['ext'] == 'xls') {
                        $return[$key] = $item;
                        continue;
                    }
                    
                    $data_file = $item['data_org']; //Edit LIXD-393 hungtn VNIT 20151218
                    /*
                    $data_url = $item['data_url'];
                    $data_url_path = explode("/", $data_url);
                    
                    if(count($data_url_path) > 0) {
                        $item_file_flg = 0;
                        foreach($data_url_path as $path_tmp) {
                            if($path_tmp == 'itemfile') {
                                $item_file_flg = 1;
                            }
                            if($item_file_flg == 3) {
                                $data_file = $path_tmp;
                            }
                            
                            if($item_file_flg > 0) {
                                $item_file_flg++;
                            }
                        }
                    }
                    */
                    if(!empty($data_file)) {
                        if(Itemfile_model::check_file_exit($data_file, $path, $fileLib) == TRUE) {
                            $return[$key] = $item;
                        }
                        
                    }
				}
                //Edit End LIXD-393 hungtn VNIT 20151216
            }
			$return_arr = $return;
        }
        return $return_arr;
    }
    //Add Start LIXD-393 hungtn VNIT 20151216
    public static function check_file_exit($file_name, $path, $file_lib) {
        $file_list = $file_lib->FileFolderList($path);
        if(count($file_list) > 0) {
            foreach($file_list as $key => $file) {
               if(is_numeric($key)) {
                   if($file == $file_name) {
                       return TRUE;
                   }
               } else {
                   if(Itemfile_model::check_file_exit($file_name, $path.$key, $file_lib) == false) {
                       return FALSE;
                   } else {
                       return true;
                   }
               }
            }
        } else {
            return false;
        }
        
    }//Add End LIXD-393 hungtn VNIT 20151216

    //Add End LIXD-357 hungtn VNIT 20151123
	/**
	 * Add - miyazaki U_SYS - 2014/10/14
	 * 素材数取得
	 */
	private function get_sozai_count($section)
	{
		$count = 0;
		foreach ($section as $sec) {
			if (isset($sec['info']) == false) {
				continue;
			}
			
			foreach ($sec['info'] as $info) {
				if (($info['type'] == AKAGANE_SECTION_KBN_IMAGE || $info['type'] == AKAGANE_SECTION_KBN_IMAGE_MASTER) && (isset($info['file_name']) == true)) { // Add Start LIXD-9 hungtn VNIT 20150622
					$count++;
				} else if (($info['type'] == AKAGANE_SECTION_KBN_HEADER_EXCEL) && ((isset($info['header_file_name']) == true) || (isset($info['file_name']) == true))) {
					$count++;
				} else if (($info['type'] == AKAGANE_SECTION_KBN_EXCEL) && (isset($info['file_name']) == true)) {
					$count++;
				}
			}
			//add start LIXD-354 Phong VNIT-20151117
			if(isset($sec['child_section']) && !empty($sec['child_section'])){
				foreach ($sec['child_section'] as $child) {
					if (isset($child['info']) == false) {
						continue;
					}
					foreach ($child['info'] as $info) {
						if (($info['type'] == AKAGANE_SECTION_KBN_IMAGE || $info['type'] == AKAGANE_SECTION_KBN_IMAGE_MASTER) && (isset($info['file_name']) == true)) { // Add Start LIXD-9 hungtn VNIT 20150622
							$count++;
						} else if (($info['type'] == AKAGANE_SECTION_KBN_HEADER_EXCEL) && ((isset($info['header_file_name']) == true) || (isset($info['file_name']) == true))) {
							$count++;
						} else if (($info['type'] == AKAGANE_SECTION_KBN_EXCEL) && (isset($info['file_name']) == true)) {
							$count++;
						}
					}
				}
			}//add end LIXD-354 Phong VNIT-20151117
		}
		
		return $count;
	}
	
	/**
	 * XML展開、値取得
	 */
	public function get_xml_data($param, $ser_lib)
	{
		$res_param = array();
		
		$sec_xml = $ser_lib->get_xml_section();
		$section_data = Section_common_model::make_section_from_xml($param['t_ctg_head_id'], $param['data_kbn'], $param['m_lang_id'], $sec_xml);
		$res_param['section_data'] = $section_data;

		// フリー
		$free_xml = $ser_lib->get_xml_free();
		$free_data = Section_common_model::make_section_from_free($param['m_lang_id'], $free_xml);
		$res_param['free_data'] = $free_data;
		
		return $res_param;
	}
	
	/**
	 * 一時ファイル表示（フリー用）
	 */
	public static function action_viewtmpfree()
	{
		$param = self::get_param(array('acw_url'));
		$free_id = $param['acw_url'][3];
		unset($param['acw_url'][3]);
		return self::_viewtmp($param['acw_url'], $free_id);
	}
	
	/**
	 * 一括DL処理のチェック
	 */
	public static function action_checkmultidl()
	{
		$param = self::get_param(
			array(
				  'chk_multidl_select'
				, 'tmp_name'
				, 't_ctg_head_id'
				, 't_series_head_id'
				, 't_series_mei_id'
				, 'm_lang_id'
				, 'data_kbn'
				, 'yoyaku_flg'
			)
		);
		
		$result = array();
		if (self::get_validate_result() == true) {
			// OK
			$result['status'] = 'OK';
			
			$file_lib = new File_lib();
			
			// ダウンロード作業用フォルダを準備（あれば一旦削除）
			// Edit start - miyazaki U_SYS - 2014/11/19
			if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
				$path_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			} else {
				$path_lib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			}
			// Edit end - miyazaki U_SYS - 2014/11/19
			
			$path_lib->combine($param['tmp_name']);
			$tmp_path = $path_lib->get_full_path();
			$path_lib->combine('Download');
			if ($file_lib->FolderExists($path_lib->get_full_path()) === true) {
				$file_lib->DeleteFolder($path_lib->get_full_path());
			}
			$file_lib->CreateFolder($path_lib->get_full_path());
			
			// シリーズIDのフォルダ
			$model = new Itemfile_model();
			// Edit start - miyazaki U_SYS - 2014/11/19
			if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
				$ver_info = $model->get_yoyaku_series_ver($param);
			} else {
				$ver_info = $model->get_series_ver($param);
			}
			// Edit end - miyazaki U_SYS - 2014/11/19
			
			$trg_model = New Trigger_model();
			// ファイル、フォルダに使えない文字をアンダーバーに変更
			$series_id = $trg_model->_replace_under_ber($ver_info['series_id']);
			$dl_name = $series_id . '_' . $ver_info['major_ver'] . '_' . $ver_info['minor_ver'] . '_' . date('YmdGis');
			// 格納フォルダを用意
			$dl_name= str_replace(array("#","&","%"),"_",$dl_name);//Add LIXD-37 Phong VNIT-20150723
			$dl_wrk_path = $path_lib->get_full_path($dl_name);						
			$file_lib->CreateFolder($dl_wrk_path);

			// XMLデータ取得（後の処理をやりやすいように変換）
			// Edit start - miyazaki U_SYS - 2014/11/19
			if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
				$ser_lib = new YoyakuSeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
			} else {
				$ser_lib = new SeriesFile_lib($param['t_series_head_id'], $param['t_series_mei_id']);
			}
			// Edit end - miyazaki U_SYS - 2014/11/19
			
			$xml_data = $model->get_xml_data($param, $ser_lib);
			$data_list = array();
			foreach ($xml_data['free_data'] as $free) {
				foreach ($free['info'] as $f_info) {
					$data_list[$f_info['data_id']] = $f_info;
					$data_list[$f_info['data_id']]['free_id'] = $free['id'];
				}
			}
			foreach ($xml_data['section_data'] as $sec) {
				foreach ($sec['info'] as $s_info) {
					$data_list[$s_info['data_id']] = $s_info;
					$data_list[$s_info['data_id']]['free_id'] = null;
				}
                //Add Start LIXD-287 download material hungtn VNIT 20151105
                if(isset($sec['child_section'])) {
                    if(count($sec['child_section']) > 0) {
                        foreach ($sec['child_section'] as $child_section ) {
                            foreach ($child_section['info'] as $s_info) {
            					$data_list[$s_info['data_id']] = $s_info;
            					$data_list[$s_info['data_id']]['free_id'] = null;
            				}
                        }
                    }
                }
                //Add End LIXD-287 download material hungtn VNIT 20151105
			}
			//add start  LIXD-354 Phong VNIT-20151118
			$series = new Series_model();
			$list_tag = $series->get_list_tag($param['t_series_mei_id'], $param['m_lang_id'], $param['t_series_head_id']);
	        foreach ($list_tag as $section_tag){
	        	foreach ($section_tag as $tag){
	        		if(empty($tag['image_tmp_file'])==FALSE){
		        		$key = $tag['t_ctg_section_id'].'_'.$tag['t_multi_data_id'].'_'.str_replace(array("<",">"),"",$tag['tag_name']);
		        		$data_list[$key]['file_name']= $tag['image_tmp_file'];
						$data_list[$key]['org_file_name']=$tag['image_name'];					
						$data_list[$key]['type'] = 0 ;  // # AKAGANE_SECTION_KBN_HEADER_EXCEL;
						if($tag['free_flg']=='YES'){
							if($tag['excel_flg']=='YES'){
							$data_list[$key]['free_id']= $tag['t_ctg_section_id'].'/excel';	
							}else{
								$data_list[$key]['free_id']= $tag['t_ctg_section_id'];
							}
						}else{
							if($tag['excel_flg']=='YES'){
							$data_list[$key]['free_id']= 'excel';	
							}else{
								$data_list[$key]['free_id']= null;
							}	
						}						
					}
	        	}	
	        }	        
	        //add end LIXD-354 Phong VNIT-20151118
			// コピー
			$exist_obj = array();
			$file_cnt = 0;
			$array_check= array();
			if(count($param['chk_multidl_select']) > 1){
				foreach ($param['chk_multidl_select'] as $key =>$item) {
					if (array_key_exists($item, $data_list) !== false){
						if(in_array($data_list[$item]['file_name'],$array_check)){
							unset($param['chk_multidl_select'][$key]);
						}else{
							$array_check[]= $data_list[$item]['file_name'];	
						}
					}
				}
			}
			foreach ($param['chk_multidl_select'] as $target) {
				if (array_key_exists($target, $data_list) !== false) {
					// ファイルコピー
					$res1 = $model->copy_dl_file(
						  $file_lib
						, $tmp_path
						, $dl_wrk_path
						, $exist_obj
						, $data_list[$target]['file_name']
						, $data_list[$target]['org_file_name']
						, $data_list[$target]['free_id']
					);
					if ($res1 !== false) {
						$exist_obj[$data_list[$target]['org_file_name']] = $res1;
						$file_cnt++;
					}

					if ($data_list[$target]['type'] == AKAGANE_SECTION_KBN_HEADER_EXCEL) {
						// ヘッダーEXCEL
						$res2 = $model->copy_dl_file(
							  $file_lib
							, $tmp_path
							, $dl_wrk_path
							, $exist_obj
							, $data_list[$target]['header_file_name']
							, $data_list[$target]['org_header_file_name']
							, $data_list[$target]['free_id']
						);
						if ($res2 !== false) {
							$exist_obj[$data_list[$target]['org_header_file_name']] = $res2;
							$file_cnt++;
						}
					}				
				}
			}
			
			// ダウンロード用ファイルの準備
			// 日本語のファイル名を使用すると、ダウンロード時にエラーになるため、変換
			if ($file_cnt <= 1) {
				// 単品の場合、そのファイル自体をダウンロード
				$file_list = $file_lib->FileList($dl_wrk_path);
				$ext = $file_lib->GetExtensionName($file_list[0]);
				$file_name = uniqid() . '.' . $ext;
				$org_file_name = $file_list[0];
				$file_lib->CopyFile($dl_wrk_path . '/' . $file_list[0], $path_lib->get_full_path($file_name));
			} else {
				// 複数の場合、圧縮ファイルでダウンロード
				$file_name = uniqid() . '.zip';
				$org_file_name = $dl_name . '.zip';
				if ($model->create_zip($dl_wrk_path, $path_lib->get_full_path($file_name)) == false) {
					// 圧縮失敗
					$result['status'] = 'NG';
					$list = ACWError::get_list();
					$result['msg'] = $list[0]['info'];
					return ACWView::json($result);
				}
			}
			
			$result['file_name'] = $file_name;
			$result['org_file_name'] = $org_file_name;
		} else {
			// NG
			$result['status'] = 'NG';
			$list = ACWError::get_list();
			$result['msg'] = $list[0]['info'];
		}
		
		return ACWView::json($result);
	}
	
	/**
	 * 素材一括DL実行
	 */
	public static function action_multidl()
	{
		$url_param = self::get_param(array('acw_url'));
		$param = $url_param['acw_url'];

		// Edit start - miyazaki U_SYS - 2014/11/19
		if (isset($param) && (count($param) == 3 || count($param) == 4)) {
			if (count($param) == 4) {
				$path_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			} else {
				$path_lib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			}
		// Edit end - miyazaki U_SYS - 2014/11/19
			$path_lib->combine($param[2]);
			$path_lib->combine('Download');
			$path_lib->combine($param[0]);

			return ACWView::download($param[1], file_get_contents($path_lib->get_full_path()));
		}

		return ACWView::OK;
	}
	
	/**
	 * 素材一括DL後処理
	 */
	public static function action_multidlafter()
	{
		$param = self::get_param(array('tmp_name', 'yoyaku_flg'));
		
		if ((isset($param['tmp_name']) == true) && ($param['tmp_name'] != '')) {
			// Edit start - miyazaki U_SYS - 2014/11/19
			if ((isset($param['yoyaku_flg']) == true) && ($param['yoyaku_flg'] != '')) {
				$path_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
			} else {
				$path_lib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
			}
			// Edit end - miyazaki U_SYS - 2014/11/19
			
			$path_lib->combine($param['tmp_name']);
			
			$file_lib = new File_lib();
			if ($file_lib->FolderExists($path_lib->get_full_path()) === true) {
				$file_lib->DeleteFolder($path_lib->get_full_path());
			}
		}

		$result = array('status'=>'OK');
		return ACWView::json($result);
	}		
	
	/*
	 * 一括DL処理のチェック
	 */
	private static function _validate_checkmultidl(&$param)
	{
		if (isset($param['chk_multidl_select']) == false) {
			ACWError::add('target_empty', '素材が選択されていません。');
			return false;
		}
		
		if (is_null($param['chk_multidl_select']) == true) {
			ACWError::add('target_empty', '素材が選択されていません。');
			return false;
		}
		
		return true;
	}
	
	/**
	 * ファイルのコピー（重複チェックなど）
	 */
	private function copy_dl_file($file_lib, $tmp_path, $dl_wrk_path, $exist_obj, $file_name, $org_file_name, $free_id)
	{
		if (is_null($file_name) == false) {
			if (is_null($free_id) == true) {
				$src_path = $tmp_path . '/' . $file_name;
			} else {
				$src_path = $tmp_path . '/' . $free_id . '/' . $file_name;
			}
			$dst_path = $dl_wrk_path . '/' . $org_file_name;

			// 既に同じファイル名がいないかチェック
			if (array_key_exists($org_file_name, $exist_obj) !== false) {
				// 同じファイルがある場合は、"_1"と付加していく

				// 拡張子など
				$file_ext = $file_lib->GetExtensionName($dst_path);
				$file_name = $file_lib->GetBaseName($dst_path);							

				// 同じファイルの数
				$file_value = $exist_obj[$org_file_name];

				// 新しいパス
				$new_path = $dl_wrk_path . '/' . $file_name . '_' . $file_value . '.' . $file_ext;
				$file_lib->CopyFile($src_path, $new_path);

				return ++$file_value;
			} else {
				// 同じファイルがなければ通常通りコピー
				$file_lib->CopyFile($src_path, $dst_path);

				return 1;
			}
		}
		return false;
	}	
	
	/**
	 * zipファイルを作成
	 */
	private function create_zip($wrk_dir_path, $zip_file_name_path)
	{
		$file_lib = new File_lib();
		
		if ($file_lib->FileExists($zip_file_name_path)) {
			$file_lib->DeleteFile($zip_file_name_path);
		}

		try {
			//edit start LIXD-354 Phong VNIT-20151117
			//$res = $this->set_zip_list_exe($zip_file_name_path, $wrk_dir_path);			
			$res = $file_lib->sevenzip_compress($zip_file_name_path, $wrk_dir_path);
			if ($res == FALSE) {
			//edit end LIXD-354 Phong VNIT-20151117
				ACWError::add('zip_error', 'zipフォルダの作成に失敗しました。');
				return false;
			}		
		} catch (Exception $exc) {
			ACWError::add('zip_error', 'zipフォルダの作成に失敗しました。' . $exc->getMessage());
			return false;
		}
		
		return true;
	}
	
	/**
	 * 圧縮処理（exe起動）
	 */
	private function set_zip_list_exe($zipname, $dirname)
	{
		// コマンド
		$comm = sprintf(AKAGANE_WEB_PATH . '/7za.exe a "%s" "%s"', $zipname, $dirname);  // edit LIXD-37 Phong VNIT 20150721
		$comm_sjis = mb_convert_encoding($comm, 'sjis-win', 'UTF-8');
		//Edit start LIXD-69 TIN-VNIT 7/30/2015
		$result = $this->uft8_exec($comm, $output, $return_var);			
		//$result = exec($comm, $output, $return_var);	
        //Edit end LIXD-69 TIN-VNIT 7/30/2015	
		ACWLog::debug_var('MULTI_DL', '圧縮処理ログ');
		ACWLog::debug_var('MULTI_DL', $result);
		ACWLog::debug_var('MULTI_DL', $output);
		ACWLog::debug_var('MULTI_DL', $return_var);
		
		return $return_var;
	}
    //Edit start LIXD-353 TIN-VNIT 7/30/2015
	public function uft8_exec($cmd,&$output=null,&$return=null)
	{
        try {
            //get current work directory
    	    $cd = getcwd();
    	
    	    // on multilines commands the line should be ended with "\r\n"
    	    // otherwise if unicode text is there, parsing errors may occur
    	   /* $cmd = "@echo off
    	    @chcp 65001 > nul
    	    @cd \"$cd\"
    	    ".$cmd;*/
    	    $cmd = "chcp 65001 > nul	    
    	    ".$cmd;


    	    //create a temporary cmd-batch-file
    	    //need to be extended with unique generic tempnames
    	    $rand_txt = "". rand(100000, 999999) . uniqid() . rand(1000000, 9999999);
    	    $tempfile = ACW_TMP_DIR."/".'php_exec'.$rand_txt.'.bat';
    	    file_put_contents($tempfile,$cmd);
    	    //execute the batch
    	    exec("start /b ".$tempfile,$output,$return);

    	    // get rid of the last two lin of the output: an empty and a prompt
    	    array_pop($output);
    	    array_pop($output);

    	    //if only one line output, return only the extracted value
    	    if(count($output) == 1)
    	    {
    	        $output = $output[0];
    	    }

    	    //delete the batch-tempfile
            $file_lib = new File_lib();
            if($file_lib->FileExists($tempfile)) {
                if(!$file_lib->DeleteFile($tempfile)) {
                    ACWError::add('uft8_exec_error', 'Delete fail: ' . $tempfile);
                }
                
            }
    	    
    	
    	    return $output;
        } catch (Exception $exc) {
			ACWError::add('uft8_exec_error', 'uft8_exec_error: ' . $exc->getMessage());
			return false;
		}
	    

	}
    //Edit end LIXD-353 TIN-VNIT 7/30/2015
	private function get_series_ver($param)
	{
		$sql = "
			SELECT
				shead.series_id
			,	sver.major_ver
			,	sver.minor_ver
			FROM
				t_series_mei smei
			INNER JOIN
				t_series_ver sver
				ON	smei.t_series_ver_id = sver.t_series_ver_id --EDIT NBKD-1184/LIXD-63 MinhVnit 2015/07/28
				AND sver.del_flg = 0			
			INNER JOIN
				t_series_lang slang
				ON	sver.t_series_lang_id = slang.t_series_lang_id
				AND	slang.del_flg = 0
				AND	slang.m_lang_id = :m_lang_id
			INNER JOIN
				t_series_head shead
				ON	shead.t_series_head_id = slang.t_series_head_id
				--AND	shead.del_flg = 0 --Remove NBKD-40,52 Minh Vnit 2014/11/13
				AND	shead.t_series_head_id = :t_series_head_id
			WHERE
				smei.t_series_mei_id = :t_series_mei_id
			AND	smei.del_flg = 0
			
		";
		$filter = ACWArray::filter($param, array('m_lang_id', 't_series_mei_id', 't_series_head_id'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	
	/*
	 * Add - miyazaki U_SYS - 2014/11/19 
	 */
	private function get_yoyaku_series_ver($param)
	{
		$sql = "
			SELECT
				shead.series_id
			,	yver.major_ver
			,	yver.minor_ver
			FROM
				t_yoyaku_series_mei ymei
			INNER JOIN
				t_yoyaku_series_ver yver
				ON	ymei.t_yoyaku_series_ver_id = yver.t_yoyaku_series_ver_id --EDIT NBKD-1184/LIXD-63 MinhVnit 2015/07/28
				AND yver.del_flg = 0			
			INNER JOIN
				t_yoyaku_series_lang ylang
				ON	yver.t_yoyaku_series_lang_id = ylang.t_yoyaku_series_lang_id
				AND	ylang.t_yoyaku_series_lang_id = :t_series_head_id
				AND	ylang.comp_flg = 0
			INNER JOIN
				t_series_head shead
				ON	shead.t_series_head_id = ylang.t_series_head_id
				--AND	shead.del_flg = 0 --Remove NBKD-40,52 Minh Vnit 2014/11/13
			INNER JOIN
				t_series_lang slang
				ON	slang.t_series_lang_id = ylang.t_series_lang_id
				AND	slang.m_lang_id = :m_lang_id
			WHERE
				ymei.t_yoyaku_series_mei_id = :t_series_mei_id
			AND	ymei.del_flg = 0
			
		";
		$filter = ACWArray::filter($param, array('m_lang_id', 't_series_mei_id', 't_series_head_id'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	
	// 項目置き換えリスト 
	// Add - miyazaki U_SYS - 2014/10/17
	private $_replace_colmun = array(
		',tanka,'=>',0,',
		',tani,'=>',NULL,',
		',min_num_1,'=>',1,',
		',max_num_1,'=>',9999999,',
		',slide_price_1,'=>',0,',
		',cad_url,'=>',cad_url || :cad_url_cnct_str,'
	);
	
	/**
	 * 品番の他言語反映
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	public function upload_no_other_lang($param)
	{
		// 登録中の言語が日本語でなければ戻る
		if ($param['m_lang_id'] != '1') {
			return array();
		}
		
        //Add Start - NBKD-1069 - TrungVNIT - 2015/03/20
		$yoyaku_flg = '';
		if(isset($param['yoyaku_flg']) && $param['yoyaku_flg'] == 1){
                    $yoyaku_flg = 1;
		}
        //Add End - NBKD-1069 - TrungVNIT - 2015/03/20
		
		// 品番テーブルカラム一覧
		$col_rows = $this->get_item_info_colmun($yoyaku_flg);
		
		// sqlの形に変換
		$sql_col = '';
		foreach ($col_rows as $col) {
			$sql_col .= ',' . $col['column_name'];
		}
		// insertのカラム用文字列
                if($yoyaku_flg == ''){
                    $ins_str = str_replace(',t_item_info_id,', '', $sql_col);
                }else{
                    $ins_str = str_replace(',t_yoyaku_item_info_id,', '', $sql_col);
                }
		// selectのカラム用文字列
		$sel_str = str_replace('m_lang_id', '', $ins_str);
		
		// 項目の置き換え
		foreach ($this->_replace_colmun as $src => $dst) {
			$sel_str = str_replace($src, $dst, $sel_str);
		}
		
		// 登録中の言語以外の言語一覧
		$lang_rows = $this->get_lang_rows_not_main_lang($param);
		
		// シリーズIDを取得
		$model = new ItemNo_common_model();
		if (isset($param['series_id']) == false) {
			//Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
			if ($yoyaku_flg == '') {
				$series_id = $model->get_series_id($param['t_series_head_id']);
				$param['series_id'] = $series_id;
			}else{
				$series_id = $model->get_yoyaku_series_id($param['t_series_head_id']);
				$param['series_id'] = $series_id;
			}
			//Edit End - NBKD-1069 - TrungVNIT - 2015/03/20
		}
		
		// ヘッダIDを取得
		$head = $this->get_t_series_head_id_by_series_id($param);
		if (isset($param['t_series_head_id']) == false) {
			$param['t_series_head_id'] = $head['t_series_head_id'];
		}
		$param['t_ctg_head_id'] = $head['t_ctg_head_id'];
		
        //Add Start - NBKD-1069 - TrungVNIT - 2015/03/20 //Edit Start LIXD-28 hungtn VNIT 20150904
        $y_ser_model = NULL;
		if(isset($param['yoyaku_flg']) && $param['yoyaku_flg'] == 1){
            $y_ser_model = new YoyakuSeries_model();
			$param['t_series_head_id'] = $head['t_series_head_id'];
		}
		//Add End - NBKD-1069 - TrungVNIT - 2015/03/20 //Edit End LIXD-28 hungtn VNIT 20150904
                
		$ser_model = new Series_model();
		
		$login_info = ACWSession::get('user_info');
		$param['m_user_id'] = $login_info['m_user_id'];
		
		// 他言語へ登録
		$rtn = array();
		foreach ($lang_rows as $lang) {
			$error = false;
			$message = array();
			
			$param['other_lang_id'] = $lang['m_lang_id'];
	
			$db = new Itemfile_model();

			// 明細ID取得、存在チェック
			//Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
			if($yoyaku_flg == 1){
				$series_lang_rows = $db->get_yoyaku_series_lang_rows($param['t_series_head_id'], $param['other_lang_id']);
				$param['t_series_lang_id'] = isset($series_lang_rows[0]['t_yoyaku_series_lang_id']) ? $series_lang_rows[0]['t_yoyaku_series_lang_id'] : '';
			}else{
				$series_lang_rows = $ser_model->get_series_lang_rows($param['t_series_head_id'], $param['other_lang_id']);
			}
			//Edit End - NBKD-1069 - TrungVNIT - 2015/03/20
                        
			if ((empty($series_lang_rows) == true) || ($series_lang_rows[0]['t_series_mei_id'] == '-1')) {
				// 明細データがない⇒品番反映のみ実行
				$db->begin_transaction();
				
				// 既存の品番を全て削除
				$db->delete_no_other_lang($param, $yoyaku_flg);//Edit - NBKD-1069 - TrungVNIT - 2015/03/20

				// 品番を追加
				$db->insert_no_other_lang($param, $ins_str, $sel_str, $yoyaku_flg);//Edit - NBKD-1069 - TrungVNIT - 2015/03/20
				
				$db->commit();
			} else {
				// 明細データがある→品番反映後、ヘッダーの再作成を実行
				$param['t_series_mei_id'] = $series_lang_rows[0]['t_series_mei_id'];

				// 作業言語のロック
				$lock_res = $db->lock_series_mei($param);
				if ($lock_res !== true) {
					// 明細ロック失敗
					$error = true;
					$message[] = $lang['lang_name'] . 'は' . $lock_res;
				} else {
					$db->begin_transaction();
					
					// 既存の品番を全て削除
					$db->delete_no_other_lang($param, $yoyaku_flg);//Edit - NBKD-1069 - TrungVNIT - 2015/03/20

					// 品番を追加
					$db->insert_no_other_lang($param, $ins_str, $sel_str, $yoyaku_flg);//Edit - NBKD-1069 - TrungVNIT - 2015/03/20
					
					$head_param = $param;
					$head_param['m_lang_id'] = $param['other_lang_id'];
					$head_param['lang_name'] = $lang['lang_name'];

					// 承認状況更新
                    //Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
					if($yoyaku_flg == 1){
						$status_row = $y_ser_model->get_status_row($param['t_series_mei_id'], $param['m_user_id']);
						$head_param['yoyaku_flg'] = $param['yoyaku_flg'];
					}else{
						$status_row = $ser_model->get_status_row($param['t_series_mei_id'], $param['m_user_id']);	
					}
                    //Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
					
					$head_param['approval_status'] = $status_row['approval_status'];
					$res_stat = $db->update_status_other_lang($head_param);
					if ($res_stat != '') {
						$message[] = $res_stat;
					}
                                       
					// ヘッダーファイルの自動再作成
					$res_head = Itemfile_model::_upload_success_after2($head_param);

					if ($res_head['error'] == true) {
						// エラーあり
						$error = true;
						$db->rollback();
					} else {
						// エラーなし
						$db->commit();
					}

					// 再作成実行orエラーメッセージ
					if ($res_head['message'] != '') {
						$message[] = $res_head['message'];
					}
					
					$db->unlock_series_mei($param);
				}
			}
			// 結果文字列作成
			if ($error == true) {
				$rtn[] = $lang['lang_name'] . 'の品番反映に失敗しました。';
			} else {
				$rtn[] = $lang['lang_name'] . 'の品番反映が終了しました。';
			}
			if (empty($message) == false) {
				foreach ($message as $msg) {
					$rtn[] = $msg;
				}
			}
		}
		
		return $rtn;
	}
	
	//Add Start - NBKD-1069 - TrungVNIT - 2015/03/20
	private function get_yoyaku_series_lang_rows($t_series_head_id, $other_lang_id){
		$sql = "
			SELECT
				COALESCE(yver.t_series_mei_id, -1) t_series_mei_id
			, COALESCE(yver.translate_base_major_ver, -1) key_lang_major_ver
			,	COALESCE(ylang.t_yoyaku_series_lang_id, -1) t_yoyaku_series_lang_id 
			,	yver.t_yoyaku_series_ver_id
			,	yver.approval_status
			, 	sm.series_name
			,	head.series_id
			FROM
				t_yoyaku_series_lang ylang
			LEFT JOIN
				t_yoyaku_series_ver yver
				ON yver.t_yoyaku_series_ver_id = ylang.t_yoyaku_series_ver_id
			LEFT JOIN
				t_yoyaku_series_mei sm
					ON (yver.t_yoyaku_series_ver_id = sm.t_yoyaku_series_ver_id)
			LEFT JOIN
				t_series_head head
				ON head.t_series_head_id = ylang.t_series_head_id
			WHERE
				ylang.t_series_lang_id = (
					SELECT 
						t_series_lang_id
					FROM 
						t_series_lang
					WHERE 
						t_series_head_id = :t_series_head_id AND m_lang_id = :m_lang_id 
				) AND ylang.t_series_head_id = :t_series_head_id
			AND ylang.comp_flg = 0";
		return $this->query($sql, array('t_series_head_id'=> $t_series_head_id, 'm_lang_id'=>$other_lang_id));
	}
	//Add End - NBKD-1069 - TrungVNIT - 2015/03/20
	
	/**
	 * 品番テーブルカラム一覧
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function get_item_info_colmun($yoyaku_flg = '')
	{
            if($yoyaku_flg == ''){
                $sql_col = "
			SELECT 
				column_name
			FROM 
				information_schema.columns 
			WHERE 
				table_name='t_item_info' 
			ORDER BY
				ordinal_position
		";   
            }else{
                $sql_col = "
			SELECT 
				column_name
			FROM 
				information_schema.columns 
			WHERE 
				table_name='t_yoyaku_item_info' 
			ORDER BY
				ordinal_position
		";
            }
		$rows = $this->query($sql_col);
		return $rows;
	}
	
	/**
	 * 登録されている言語の一覧(自分自身以外)
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function get_lang_rows_not_main_lang($param)
	{
		$lang_sql = "
			SELECT
				m1.m_lang_id
			,	m1.lang_name
			FROM
				m_lang m1
			WHERE
				m_lang_id <> :m_lang_id
			AND
				m1.del_flg = 0
			ORDER BY
				m1.disp_seq
		";

		return $this->query($lang_sql, array('m_lang_id'=>$param['m_lang_id']));
	}

	/**
	 * 登録されている別言語の品番を削除
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function delete_no_other_lang($param, $yoyaku_flg = '')
	{
		//Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
		if($yoyaku_flg == 1){
			$del_sql = "
				DELETE FROM
					t_yoyaku_item_info
				WHERE
					m_lang_id = :other_lang_id
				AND
					series_item_no = :series_id
			";
		}else{
			$del_sql = "
				DELETE FROM
					t_item_info
				WHERE
					m_lang_id = :other_lang_id
				AND
					series_item_no = :series_id
			";
		}
		//Edit End - NBKD-1069 - TrungVNIT - 2015/03/20
		$filter = ACWArray::filter($param, array('other_lang_id', 'series_id'));
		$this->execute($del_sql, $filter);
	}
	
	/**
	 * 登録された品番を他言語へ反映
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function insert_no_other_lang($param, $ins_str, $sel_str, $yoyaku_flg = '')
	{
		//Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
		if($yoyaku_flg == 1){
			$sql = "
				INSERT INTO t_yoyaku_item_info (
					";
			$sql .= $ins_str;
			$sql .= "
				) SELECT
					:other_lang_id";
			$sql .= $sel_str;
			$sql .= "
				FROM
					t_yoyaku_item_info
				WHERE
					m_lang_id = :m_lang_id
				AND series_item_no = :series_id
				ORDER BY
					t_yoyaku_item_info_id 
			";
		}else{
			$sql = "
				INSERT INTO t_item_info (
					";
			$sql .= $ins_str;
			$sql .= "
				) SELECT
					:other_lang_id";
			$sql .= $sel_str;
			$sql .= "
				FROM
					t_item_info
				WHERE
					m_lang_id = :m_lang_id
				AND series_item_no = :series_id
				ORDER BY
					t_item_info_id 
			";
		}
		//Edit End - NBKD-1069 - TrungVNIT - 2015/03/20
		
		$filter = ACWArray::filter($param, array('other_lang_id', 'series_id', 'm_lang_id'));
		// CAD_URLの値の末尾につける文字列
		if ($param['other_lang_id'] == '2') {
			$filter['cad_url_cnct_str'] = '&language=GB';
		} else if ($param['other_lang_id'] == '3') {
			$filter['cad_url_cnct_str'] = '&language=CN';
		} else {
			$filter['cad_url_cnct_str'] = '';
		}
		$this->execute($sql, $filter);
	}
	
	/**
	 * シリーズIDからヘッダIDを取得
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function get_t_series_head_id_by_series_id($param)
	{
		$sql = "
			SELECT
				head.t_series_head_id
			,	head.t_ctg_head_id
			FROM
				t_series_head head
			WHERE
				head.del_flg = 0
			AND	head.series_id = :series_id
		";
		
		$filter = ACWArray::filter($param, array('series_id'));
		$rows = $this->query($sql, $filter);
		
		if (count($rows) == 0) {
			return null;
		}
		return $rows[0];
	}

        /**
	 * 他言語品番反映後のヘッダーフォーマット再読み込み
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	public static function _upload_success_after2($params)
	{
                //Edit Start - NBKD-1069 - TrungVNIT - 2015/03/20
		if(isset($params['yoyaku_flg']) && $params['yoyaku_flg'] == 1){
			$series_file = new YoyakuSeriesFile_lib($params['t_series_lang_id'], $params['t_series_mei_id']);
			$series_tmp_path = AKAGANE_YOYAKU_SERIES_TMP_PATH;
			$tmp_name = YoyakuSeriesFile_lib::make_tmp($params['t_series_mei_id']);
		}else{
			$series_file = new SeriesFile_lib($params["t_series_head_id"], $params['t_series_mei_id']);
			$series_tmp_path = AKAGANE_SERIES_TMP_PATH;
			$tmp_name = SeriesFile_lib::make_tmp($params['t_series_mei_id']);
		}
		//Edit End - NBKD-1069 - TrungVNIT - 2015/03/20
		$rtn_msg = array();
		$rtn_msg['message'] = '';
		$rtn_msg['error'] = true;
		
		// テンポラリフォルダを作成してコピーする
		//$tmp_name = SeriesFile_lib::make_tmp($params['t_series_mei_id']);	
		$series_file->copy_to_tmp($tmp_name);
  
		// 他言語反映の場合に、他言語の品番エクセルを削除する
                if(isset($params['yoyaku_flg']) && $params['yoyaku_flg'] == 1){
                    $path_lib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
                }else{
                    $path_lib = new Path_lib(AKAGANE_SERIES_TMP_PATH);
                }
		$path_lib->combine($tmp_name);
		$path_lib->combine('item_no.xlsx');
		$file_lib = new File_lib();
		if ($file_lib->FileExists($path_lib->get_full_path()) === true) {
                    $file_lib->DeleteFile($path_lib->get_full_path());
		}
		
		// シリーズXMLの読み込み
		$sec_xml = $series_file->get_xml_section();
		if (is_null($sec_xml)) {
			$rtn_msg['message'] = $params['lang_name'] . 'の' . 'XMLの読み込みに失敗しました';
			return $rtn_msg;
		}
		$free_xml = $series_file->get_xml_free();
		if (is_null($free_xml)) {
			$rtn_msg['message'] = $params['lang_name'] . 'の' . 'XMLの読み込みに失敗しました';
			return $rtn_msg;
		}
		
		// tmpディレクトリパス作成
		//$tmp_path = new Path_lib($series_tmp_path);
		if(isset($params['yoyaku_flg']) && $params['yoyaku_flg'] == 1){
                    $tmp_path = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
                }else{
                    $tmp_path = new Path_lib(AKAGANE_SERIES_TMP_PATH);
                }
		$tmp_path->combine($tmp_name);		
		$xml_path = $tmp_path->get_full_path() . '/' . AKAGANE_SERIES_XML_NAME;
		
		$res_count = 0;
		$res_count += self::_reload_excel_header($sec_xml, $tmp_name, $params['m_lang_id'], false, $xml_path, $params['yoyaku_flg']);
		$res_count += self::_reload_excel_header($free_xml, $tmp_name, $params['m_lang_id'], true, $xml_path, $params['yoyaku_flg']);
		
		// テンポラリフォルダから戻す(テンポラリフォルダは削除される)
		$series_file->move_tmp_dir($tmp_name);
		$rtn_msg['error'] = false;
		// ヘッダー再読み込みが行われた場合、メッセージを表示
		if ($res_count > 0) {
			$rtn_msg['message'] = $params['lang_name'] . 'の' . 'ヘッダーフォーマットエクセルの再作成を実行しました';
		}		
		return $rtn_msg;
	}
	
	/**
	 * 他言語品番反映後の承認状況を更新
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function update_status_other_lang(&$param) // Edit - miyazaki U_SYS - 2015/01/06
	{
		$rtn = '';
		if (($param['approval_status'] == AKAGANE_APPROVAL_STATUS_KEY_PROO_NOW) 
		 || ($param['approval_status'] == AKAGANE_APPROVAL_STATUS_KEY_PROO_END)) {
			// 承認中、確認中⇒修正中
                        //Edit Start - NBKD-1069 - TrungVNIT - 2015/04/15
                        if(isset($param['yoyaku_flg']) && $param['yoyaku_flg'] == 1){
                            $rtn = $this->yoyaku_update_status_edit_irai($param);
                        }else{
                            $rtn = $this->update_status_edit_irai($param);
                        }
                        //Edit End - NBKD-1069 - TrungVNIT - 2015/04/15
		} else if ($param['approval_status'] == AKAGANE_APPROVAL_STATUS_KEY_COMP) {
			// 承認済み⇒ロック解除
                        //Edit Start - NBKD-1069 - TrungVNIT - 2015/03/25
                        if(isset($param['yoyaku_flg']) && $param['yoyaku_flg'] == 1){
                            $rtn = $this->yoyaku_update_status_lock_release($param);
                        }else{
                            $rtn = $this->update_status_lock_release($param);
                        }
                        //Edit End - NBKD-1069 - TrungVNIT - 2015/03/25
		}
		
		return $rtn;
	}
	
        //Add Start - NBKD-1069 - TrungVNIT - 2015/04/15
        private function yoyaku_update_status_edit_irai(&$params){
            $old_t_series_mei_id = $params['t_series_mei_id'];
            $new_t_series_mei_id = $params['t_series_mei_id'];
            $approval_status = 1; // 修正中
            
            $sql = "
                    INSERT INTO
                            t_yoyaku_series_mei
                    (
                              t_yoyaku_series_ver_id
                            , series_name
                            , kou_no
                            , upd_fld_kbn
                            , upd_fld_name
                            , upd_kbn
                            , del_flg
                            , add_user_id
                            , add_datetime
                            , upd_user_id
                            , upd_datetime
                    )
                    SELECT
                              t_yoyaku_series_ver_id
                            , series_name
                            , kou_no
                            , upd_fld_kbn
                            , upd_fld_name
                            , upd_kbn 
                            , del_flg
                            , add_user_id
                            , add_datetime
                            , upd_user_id
                            , upd_datetime
                    FROM
                            t_yoyaku_series_mei
                    WHERE
                            t_yoyaku_series_mei_id = :t_yoyaku_series_mei_id
            ";
            $sql_params = array(
                    't_yoyaku_series_mei_id' => $old_t_series_mei_id
            );
           
            $this->execute($sql, $sql_params);
            
            // シリーズ明細ID取得
            $rows = $this->query("SELECT LASTVAL() AS t_yoyaku_series_mei_id");
            $params['t_series_mei_id'] = $rows[0]['t_yoyaku_series_mei_id'];
            $new_t_series_mei_id = $params['t_series_mei_id'];
            
            $this->lock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id, 'yoyaku_flg'=>1));
            
            $series_file = new YoyakuSeriesFile_lib($params['t_series_lang_id'], $old_t_series_mei_id);
            $series_file->make_clone($params['t_series_lang_id'], $new_t_series_mei_id);
            
            $sql = "
                        INSERT INTO
                                t_yoyaku_series_approval
                        (
                                  t_yoyaku_series_mei_id
                                , approval_user_kbn
                                , approval_user_id
                                , approval_status
                                , del_flg
                                , add_user_id
                                , add_datetime
                                , upd_user_id
                                , upd_datetime
                        )
                        SELECT
                                  :t_series_mei_id
                                , approval_user_kbn
                                , approval_user_id
                                , :approval_status
                                , :del_flg
                                , :add_user_id
                                , NOW()
                                , :upd_user_id
                                , NOW()
                        FROM
                                t_yoyaku_series_approval
                        WHERE
                                t_yoyaku_series_mei_id = :old_t_series_mei_id
                ";
                $sql_params = array(
                        't_series_mei_id' => $new_t_series_mei_id,
                        'old_t_series_mei_id' => $old_t_series_mei_id,
                        'approval_status' => null, // 校正承認状況 修正中
                        'del_flg' => 0,
                        'add_user_id' => $params['m_user_id'],
                        'upd_user_id' => $params['m_user_id']
                );
                $this->execute($sql, $sql_params);
                
                $sql = "
                        UPDATE
                                t_yoyaku_series_ver
                        SET
                                approval_status = :approval_status -- 校正承認ステータス 校正済 or 修正中
                        ,
                                t_series_mei_id = :new_t_series_mei_id
                        WHERE
                                t_series_mei_id = :old_t_series_mei_id
                ";
                $sql_params = array(
                        'old_t_series_mei_id' => $old_t_series_mei_id,
                        'new_t_series_mei_id' => $new_t_series_mei_id,
                        'approval_status' => $approval_status
                );
                $this->execute($sql, $sql_params);

                $this->unlock_series_mei(array('t_series_mei_id'=>$old_t_series_mei_id, 'yoyaku_flg'=>1));
                return $params['lang_name'] . 'の校を更新して、ステータスを修正中に変更しました。';
        }
        //Add End - NBKD-1069 - TrungVNIT - 2015/04/15

        /**
	 * 承認中、確認中の場合、修正中に戻す
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function update_status_edit_irai(&$params) // Edit - miyazaki U_SYS - 2015/01/06
	{
		$old_t_series_mei_id = $params['t_series_mei_id'];
		$new_t_series_mei_id = $params['t_series_mei_id'];
		
		$approval_status = 1; // 修正中
		// シリーズ明細
		$sql = "
			INSERT INTO
				t_series_mei
			(
				  t_series_ver_id
				, series_name
				, kou_no
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  t_series_ver_id
				, series_name
				, kou_no
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			FROM
				t_series_mei
			WHERE
				t_series_mei_id = :t_series_mei_id
		";
		$sql_params = array(
			't_series_mei_id' => $old_t_series_mei_id
		);
		$this->execute($sql, $sql_params);

		// シリーズ明細ID取得
		$rows = $this->query("SELECT LASTVAL() AS t_series_mei_id");
		$params['t_series_mei_id'] = $rows[0]['t_series_mei_id'];
		$new_t_series_mei_id = $params['t_series_mei_id'];
		
		// 新しい明細もロック
		$this->lock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id));

		// 旧シリーズ明細フォルダを新フォルダへコピー
		$series_file = new SeriesFile_lib($params['t_series_head_id'], $old_t_series_mei_id);
		$series_file->make_clone($params['t_series_head_id'], $new_t_series_mei_id);

		// シリーズ承認
		$sql = "
			INSERT INTO
				t_series_approval
			(
				  t_series_mei_id
				, approval_user_kbn
				, approval_user_id
				, approval_status
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  :t_series_mei_id
				, CASE m1.user_auth WHEN 2 THEN 0 ELSE 1 END -- approval_user_kbn
				, t1.m_user_id -- approval_user_id
				, :approval_status
				, :del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_ctg_user t1
			JOIN
				m_user m1
					ON (t1.m_user_id = m1.m_user_id)
			JOIN
				t_user_lang t2
					ON	(
						t2.m_user_id = m1.m_user_id
					AND	
						t2.m_lang_id = :m_lang_id
					)
			WHERE
				t1.t_ctg_head_id = :t_ctg_head_id
			AND
				m1.user_auth in (2, 3)
			AND
				m1.del_flg = 0
		";
		$sql_params = array(
			't_ctg_head_id' => $params['t_ctg_head_id'],
			't_series_mei_id' => $new_t_series_mei_id,
			'approval_status' => null, // 校正承認状況 修正中
			'del_flg' => 0,
			'add_user_id' => $params['m_user_id'],
			'upd_user_id' => $params['m_user_id'],
			'm_lang_id' => $params['m_lang_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ版
		$sql = "
			UPDATE
				t_series_ver
			SET
				approval_status = :approval_status -- 校正承認ステータス 校正済 or 修正中
			,
				t_series_mei_id = :new_t_series_mei_id
			WHERE
				t_series_mei_id = :old_t_series_mei_id
		";
		$sql_params = array(
			'old_t_series_mei_id' => $old_t_series_mei_id,
			'new_t_series_mei_id' => $new_t_series_mei_id,
			'approval_status' => $approval_status
		);
		$this->execute($sql, $sql_params);
		
		$this->unlock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id));
		
		return $params['lang_name'] . 'の校を更新して、ステータスを修正中に変更しました。';
	}

        private function yoyaku_update_status_lock_release(&$params){
            $t_yoyaku_series_ver_id = Sequence_common_model::nextval('t_series_ver_t_series_ver_id_seq');
            $login_info = ACWSession::get('user_info');
		
            $old_t_series_mei_id = null;
            $new_t_series_mei_id = null;
            // 予約時は常に、マイナーをインクリメント
            // シリーズ版
            $sql = "
                    INSERT INTO
                            t_yoyaku_series_ver
                    (
                              t_yoyaku_series_ver_id
                            , t_yoyaku_series_lang_id
                            , major_ver
                            , minor_ver
                            , t_series_mei_id
                            , translate_status
                            , approval_status
                            , translate_base_lang_id
                            , translate_base_major_ver
                            , translate_base_minor_ver
                            , image_flg
                            , spec_flg
                            , draw_flg
                            , del_flg
                            , add_user_id
                            , add_datetime
                            , upd_user_id
                            , upd_datetime
                    )
                    SELECT
                              :t_yoyaku_series_ver_id
                            , t_yoyaku_series_lang_id
                            , major_ver
                            , minor_ver + 1
                            , t_series_mei_id
                            , null -- translate_status 翻訳ステータス
                            , 1 -- approval_status 校正承認ステータス、修正中
                            , translate_base_lang_id -- 翻訳元言語
                            , translate_base_major_ver -- 翻訳元メジャー版
                            , translate_base_minor_ver -- 翻訳元マイナー版
                            , 0 -- image_flg 画像フラグ
                            , 0 -- spec_flg
                            , 0 -- draw_flg
                            , 0 -- del_flg
                            , :add_user_id
                            , NOW()
                            , :upd_user_id
                            , NOW()
                    FROM
                            t_yoyaku_series_ver
                    WHERE
                            t_series_mei_id = :t_series_mei_id
            ";
            $sql_params = array(
                    't_series_mei_id' => $params['t_series_mei_id'],
                    'add_user_id' => $login_info['m_user_id'],
                    'upd_user_id' => $login_info['m_user_id'],
                    't_yoyaku_series_ver_id' => $t_yoyaku_series_ver_id
            );
            $this->execute($sql, $sql_params);

            $params['t_series_ver_id'] = $t_yoyaku_series_ver_id;

            // 予約シリーズ言語.予約シリーズ版ID更新
            $sql = "
                    UPDATE
                            t_yoyaku_series_lang
                    SET
                            t_yoyaku_series_ver_id = :t_series_ver_id
                    WHERE
                            t_yoyaku_series_lang_id = (
                                    SELECT
                                            MAX(t_yoyaku_series_lang_id)
                                    FROM
                                            t_yoyaku_series_ver
                                    WHERE
                                            t_series_mei_id = :t_series_mei_id
                            )
                    AND	comp_flg = 0
            ";
            $sql_params = array(
                    't_series_ver_id' => $params['t_series_ver_id'],
                    't_series_mei_id' => $params['t_series_mei_id']
            );
            $this->execute($sql, $sql_params);

            // シーケンスはシリーズ明細IDを使用
            $t_yoyaku_series_mei_id = Sequence_common_model::nextval('t_series_mei_t_series_mei_id_seq');

            // シリーズ明細
            $sql = "
                    INSERT INTO
                            t_yoyaku_series_mei
                    (
                              t_yoyaku_series_mei_id
                            , t_yoyaku_series_ver_id
                            , series_name
                            , kou_no
                            , upd_fld_kbn
                            , upd_fld_name
                            , upd_kbn
                            , del_flg
                            , add_user_id
                            , add_datetime
                            , upd_user_id
                            , upd_datetime
                    )
                    SELECT
                              :t_yoyaku_series_mei_id
                            , :t_series_ver_id
                            , series_name
                            , 0 -- kou_no
                            , 1 -- upd_fld_kbn
                            , null -- upd_fld_name
                            , 5 -- upd_kbn 更新区分 5：版改訂
                            , 0 -- del_flg
                            , :add_user_id
                            , NOW()
                            , :upd_user_id
                            , NOW()
                    FROM
                            t_yoyaku_series_mei
                    WHERE
                            t_yoyaku_series_mei_id = :t_series_mei_id
            ";
            $sql_params = array(
                    't_series_ver_id' => $params['t_series_ver_id'],
                    't_series_mei_id' => $params['t_series_mei_id'],
                    'add_user_id' => $login_info['m_user_id'],
                    'upd_user_id' => $login_info['m_user_id'],
                    't_yoyaku_series_mei_id' => $t_yoyaku_series_mei_id
            );
            $this->execute($sql, $sql_params);

            // シリーズ明細ID取得
            $old_t_series_mei_id = $params['t_series_mei_id'];
            $params['t_series_mei_id'] = $t_yoyaku_series_mei_id;
            $new_t_series_mei_id = $params['t_series_mei_id'];

            // 新しい明細もロック
            $this->lock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id, 'yoyaku_flg'=>1));

            // 旧シリーズ明細フォルダを新フォルダへコピー
            $series_file = new YoyakuSeriesFile_lib($params['t_series_lang_id'], $old_t_series_mei_id);

            $series_file->make_clone($params['t_series_lang_id'], $new_t_series_mei_id);

            // Tシリーズ版.シリーズ明細ID更新
            $sql = "
                    UPDATE
                            t_yoyaku_series_ver
                    SET
                            t_series_mei_id = :t_series_mei_id
                    WHERE
                            t_yoyaku_series_ver_id = :t_series_ver_id
            ";
            $sql_params = array(
                    't_series_mei_id' => $new_t_series_mei_id,
                    't_series_ver_id' => $params['t_series_ver_id']
            );
            $this->execute($sql, $sql_params);

            // シリーズ明細履歴t_series_mei_his

            // シリーズ承認
            $sql = "
                    INSERT INTO
                            t_yoyaku_series_approval
                    (
                              t_yoyaku_series_mei_id
                            , approval_user_kbn
                            , approval_user_id
                            , approval_status
                            , del_flg
                            , add_user_id
                            , add_datetime
                            , upd_user_id
                            , upd_datetime
                    )
                    SELECT
                              :t_series_mei_id
                            , CASE m1.user_auth WHEN 2 THEN 0 ELSE 1 END -- approval_user_kbn
                            , t1.m_user_id -- approval_user_id
                            , :approval_status
                            , :del_flg
                            , :add_user_id
                            , NOW()
                            , :upd_user_id
                            , NOW()
                    FROM
                            t_yoyaku_ctg yctg
                    JOIN
                            t_ctg_user t1
                                    ON (yctg.org_t_ctg_head_id = t1.t_ctg_head_id)
                    JOIN
                            m_user m1
                                    ON (t1.m_user_id = m1.m_user_id)
                    JOIN
                            t_user_lang t2
                                    ON	(
                                            t2.m_user_id = m1.m_user_id
                                    AND	
                                            t2.m_lang_id = :m_lang_id
                                    )						
                    WHERE
                            yctg.t_yoyaku_ctg_id = (
                                                        SELECT
                                                                MAX(t_yoyaku_ctg_id)
                                                        FROM
                                                                t_yoyaku_ctg
                                                        WHERE
                                                                org_t_ctg_head_id = :t_ctg_head_id
                                                    )
                    AND
                            m1.user_auth IN (2, 3)
                    AND
                            m1.del_flg = 0
            ";
            $sql_params = array(
                    't_ctg_head_id' => $params['t_ctg_head_id'],
                    't_series_mei_id' => $params['t_series_mei_id'],
                    'approval_status' => null,
                    'del_flg' => 0,
                    'add_user_id' => $login_info['m_user_id'],
                    'upd_user_id' => $login_info['m_user_id'],
                    'm_lang_id' => $params['m_lang_id']
            );

            $this->execute($sql, $sql_params);
           
            // 新規明細のロックを解除
            if (is_null($new_t_series_mei_id) == false) {
                    $this->unlock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id, 'yoyaku_flg'=>1));
            }
            // 旧明細のロックを解除
            if (is_null($old_t_series_mei_id) == false) {
                    $this->unlock_series_mei(array('t_series_mei_id'=>$old_t_series_mei_id, 'yoyaku_flg'=>1));
            } else {
                    $this->unlock_series_mei($params);
            }
        }


        /**
	 * 確認済みの場合、ロックを解除する
	 * Add - miyazaki U_SYS - 2014/10/17
	 */
	private function update_status_lock_release(&$params) // Edit - miyazaki U_SYS - 2015/01/06
	{		  
		// シリーズ版言語翻訳対象 取得
		$sql = '
			SELECT
				  ver.major_ver
				, ver.minor_ver
				, lang.key_lang_flg
			FROM
				t_series_ver ver
			JOIN
				t_series_lang lang
				ON	ver.t_series_lang_id = lang.t_series_lang_id
				AND	lang.del_flg = 0
			WHERE
				ver.t_series_mei_id = :t_series_mei_id
		';
		$sql_params = array(
			't_series_mei_id' => $params['t_series_mei_id']
		);
		$ver_rows = $this->query($sql, $sql_params);

		// 他言語反映時は常にマイナーをインクリメント
		$major_ver = $ver_rows[0]['major_ver'];
		$minor_ver = $ver_rows[0]['minor_ver'] + 1;
		$translate_base_lang_id = null;
		$translate_base_major_ver = null;
		$translate_base_minor_ver = null;

		if ($ver_rows[0]['key_lang_flg'] == 0) {
			// キー言語以外の翻訳対象の場合

			// シリーズ版言語翻訳対象 取得
			$sql = '
				SELECT
					  lang_trans.key_lang_major_ver -- キー言語メジャー版NO
					, lang_trans.translate_base_lang_id -- 翻訳元言語ID
					, lang_trans.translate_base_major_ver -- 翻訳元メジャー版NO
					, lang_trans.translate_base_minor_ver -- 翻訳元マイナー版NO
				FROM
					t_series_lang_trans lang_trans
				WHERE
					lang_trans.t_series_head_id = :t_series_head_id
				AND
					lang_trans.key_lang_major_ver = (
						SELECT
							MAX(key_lang_major_ver) key_lang_major_ver
						FROM
							t_series_lang_trans
						WHERE
							t_series_head_id = :t_series_head_id
						AND
							translate_lang_id = :translate_lang_id
					)
				AND
					lang_trans.translate_lang_id = :translate_lang_id
			';
			$sql_params = array(
				't_series_head_id' => $params['t_series_head_id'],
				'translate_lang_id' => $params['m_lang_id']
			);
			$rows = $this->query($sql, $sql_params);
			if (count($rows) > 0) {
				$translate_base_lang_id = $rows[0]['translate_base_lang_id'];
				$translate_base_major_ver = $rows[0]['translate_base_major_ver'];
				$translate_base_minor_ver = $rows[0]['translate_base_minor_ver'];
			}
			// シリーズ版言語翻訳対象 削除
			// ※版は無視して、対象翻訳言語全ての版を削除
			$sql = "
				DELETE FROM
					t_series_lang_trans
				WHERE
					t_series_head_id = :t_series_head_id
				AND
					translate_lang_id = :translate_lang_id
			";
			$sql_params = array(
				't_series_head_id' => $params['t_series_head_id'],
				'translate_lang_id' => $params['m_lang_id']
			);
			$this->execute($sql, $sql_params);
		}

		// シリーズ版
		$sql = "
			INSERT INTO
				t_series_ver
			(
				  t_series_lang_id
				, major_ver
				, minor_ver
				, t_series_mei_id
				, translate_status
				, approval_status
				, translate_base_lang_id
				, translate_base_major_ver
				, translate_base_minor_ver
				, image_flg
				, spec_flg
				, draw_flg
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  t_series_lang_id
				, :major_ver
				, :minor_ver
				, t_series_mei_id
				, null -- translate_status 翻訳ステータス
				, 1 -- approval_status 校正承認ステータス、修正中
				, COALESCE(:translate_base_lang_id, translate_base_lang_id) -- 翻訳元言語
				, COALESCE(:translate_base_major_ver, translate_base_major_ver) -- 翻訳元メジャー版
				, COALESCE(:translate_base_minor_ver, translate_base_minor_ver) -- 翻訳元マイナー版
				, 0 -- image_flg 画像フラグ
				, 0 -- spec_flg
				, 0 -- draw_flg
				, 0 -- del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_series_ver
			WHERE
				t_series_mei_id = :t_series_mei_id
		";
		$sql_params = array(
			't_series_mei_id' => $params['t_series_mei_id'],
			'major_ver' => $major_ver,
			'minor_ver' => $minor_ver,
			'translate_base_lang_id' => $translate_base_lang_id,
			'translate_base_major_ver' => $translate_base_major_ver,
			'translate_base_minor_ver' => $translate_base_minor_ver,
			'add_user_id' => $params['m_user_id'],
			'upd_user_id' => $params['m_user_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ版ID取得
		$rows = $this->query("SELECT LASTVAL() AS t_series_ver_id");
		$params['t_series_ver_id'] = $rows[0]['t_series_ver_id'];

		// Tシリーズ言語.シリーズ版ID更新
		$sql = "
			UPDATE
				t_series_lang
			SET
				t_series_ver_id = :t_series_ver_id
			WHERE
				t_series_lang_id = (
					SELECT
						MAX(t_series_lang_id)
					FROM
						t_series_ver
					WHERE
						t_series_mei_id = :t_series_mei_id
				)
		";
		$sql_params = array(
			't_series_ver_id' => $params['t_series_ver_id'],
			't_series_mei_id' => $params['t_series_mei_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ明細
		$sql = "
			INSERT INTO
				t_series_mei
			(
				  t_series_ver_id
				, series_name
				, kou_no
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  :t_series_ver_id
				, series_name
				, 0 -- kou_no
				, 1 -- upd_fld_kbn
				, null -- upd_fld_name
				, 5 -- upd_kbn 更新区分 5：版改訂
				, 0 -- del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_series_mei
			WHERE
				t_series_mei_id = :t_series_mei_id
		";
		$sql_params = array(
			't_series_ver_id' => $params['t_series_ver_id'],
			't_series_mei_id' => $params['t_series_mei_id'],
			'add_user_id' => $params['m_user_id'],
			'upd_user_id' => $params['m_user_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ明細ID取得
		$rows = $this->query("SELECT LASTVAL() AS t_series_mei_id");
		$old_t_series_mei_id = $params['t_series_mei_id'];
		$params['t_series_mei_id'] = $rows[0]['t_series_mei_id'];
		$new_t_series_mei_id = $params['t_series_mei_id'];

		// 新しい明細もロック
		$this->lock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id));

		// 旧シリーズ明細フォルダを新フォルダへコピー
		$series_file = new SeriesFile_lib($params['t_series_head_id'], $old_t_series_mei_id);
		$series_file->make_clone($params['t_series_head_id'], $new_t_series_mei_id);
		/*$head_folder = AKAGANE_STRAGE_PATH . 'series/head_' . sprintf('%010d', $params['t_series_head_id']);
		$old_folder = $head_folder . '/mei_' . sprintf('%010d', $old_t_series_mei_id);
		$new_folder = $head_folder . '/mei_' . sprintf('%010d', $new_t_series_mei_id);
		Series_model::copy_all($old_folder, $new_folder);*/

		// Tシリーズ版.シリーズ明細ID更新
		$sql = "
			UPDATE
				t_series_ver
			SET
				t_series_mei_id = :t_series_mei_id
			WHERE
				t_series_ver_id = :t_series_ver_id
		";
		$sql_params = array(
			't_series_mei_id' => $new_t_series_mei_id,
			't_series_ver_id' => $params['t_series_ver_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ明細履歴
		$sql = "
			INSERT INTO
				t_series_mei_his
			(
				  t_series_mei_id
				, t_series_ver_id
				, series_id
				, series_name
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  t_series_mei_id
				, t_series_ver_id
				, (SELECT series_id FROM t_series_head WHERE t_series_head_id = :t_series_head_id) -- シリーズID
				, series_name
				, upd_fld_kbn
				, upd_fld_name
				, upd_kbn
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			FROM
				t_series_mei
			WHERE
				t_series_mei_id = :t_series_mei_id
		";
		$sql_params = array(
			't_series_mei_id' => $new_t_series_mei_id,
			't_series_head_id' => $params['t_series_head_id']
		);
		$this->execute($sql, $sql_params);

		// シリーズ承認
		$sql = "
			INSERT INTO
				t_series_approval
			(
				  t_series_mei_id
				, approval_user_kbn
				, approval_user_id
				, approval_status
				, del_flg
				, add_user_id
				, add_datetime
				, upd_user_id
				, upd_datetime
			)
			SELECT
				  :t_series_mei_id
				, CASE m1.user_auth WHEN 2 THEN 0 ELSE 1 END -- approval_user_kbn
				, t1.m_user_id -- approval_user_id
				, :approval_status
				, :del_flg
				, :add_user_id
				, NOW()
				, :upd_user_id
				, NOW()
			FROM
				t_ctg_user t1
			JOIN
				m_user m1
					ON (t1.m_user_id = m1.m_user_id)
			JOIN
				t_user_lang t2
					ON	(
						t2.m_user_id = m1.m_user_id
					AND	
						t2.m_lang_id = :m_lang_id
					)						
			WHERE
				t1.t_ctg_head_id = :t_ctg_head_id
			AND
				m1.user_auth IN (2, 3)
			AND
				m1.del_flg = 0
		";
		$sql_params = array(
			't_ctg_head_id' => $params['t_ctg_head_id'],
			't_series_mei_id' => $params['t_series_mei_id'],
			'approval_status' => null,
			'del_flg' => 0,
			'add_user_id' => $params['m_user_id'],
			'upd_user_id' => $params['m_user_id'],
			'm_lang_id' => $params['m_lang_id']
		);
		$this->execute($sql, $sql_params);
		
		$this->unlock_series_mei(array('t_series_mei_id'=>$new_t_series_mei_id));
		
		return $params['lang_name'] . 'の版を更新して、ステータスを修正中に変更しました。';
	}
	
	/**
	 * 項目名取得
	 * Add - miyazaki U_SYS - 2015/01/14
	 */
	private function get_section_name($param)
	{
		$sql = "
			SELECT
				section_name
			FROM
				t_ctg_section
			WHERE
				t_ctg_section_id = :t_ctg_section_id
			AND enable_flg = 1
		";
		$sql_param = ACWArray::filter($param, array('t_ctg_section_id'));
		$rows = $this->query($sql, $sql_param);
		if (count($rows) <= 0) {
			return null;
		}
		
		return $rows[0]['section_name'];
	}
	//add start NBKD-1033 Phong VNIT 2015/03/19
	public function getlist_filename($section_data,$section_free_data)
	{
		$list_file = array();		
			foreach($section_data as $row)
			{
				foreach($row['info'] as $row_info){					
					if(isset($row_info['file_name'])==true && $row_info['file_name'] !='' )
					{
						$list_file[]=  $row_info['file_name'];
						$list_file[]=  self::convert_file($row_info['file_name']);
					}
					if(isset($row_info['header_file_name'])==true && $row_info['header_file_name'] !='' )
					{
						$list_file[] = $row_info['header_file_name'];
						$list_file[] = self::convert_file($row_info['header_file_name']);
					}
                    
				}
                if(isset($row['child_section'])) { //Add Start LIXD-287 download material hungtn VNIT 20151105
                    if(count($row['child_section']) > 0) {
                        foreach($row['child_section'] as $child_section) {
                            if(isset($child_section['file_name'])==true && $child_section['file_name'] !='' )
        					{
        						$list_file[]=  $child_section['file_name'];
        						$list_file[]=  self::convert_file($child_section['file_name']);
        					}
        					if(isset($child_section['header_file_name'])==true && $child_section['header_file_name'] !='' )
        					{
        						$list_file[] = $child_section['header_file_name'];
        						$list_file[] = self::convert_file($child_section['header_file_name']);
        					}
                        }//Add End LIXD-287 download material hungtn VNIT 20151105
                    }
                }
			}
			foreach($section_free_data as $row)
			{
				foreach($row['info'] as $row_info){					
					if(isset($row_info['file_name'])==true && $row_info['file_name'] !='' )
					{
						$list_file[]=$row['id'].'/'.  $row_info['file_name'];
						$list_file[]=$row['id'].'/'.  self::convert_file($row_info['file_name']);
					}
					if(isset($row_info['header_file_name'])==true && $row_info['header_file_name'] !='' )
					{
						$list_file[] =$row['id'].'/'. $row_info['header_file_name'];
						$list_file[] =$row['id'].'/'. self::convert_file($row_info['header_file_name']);
					}
				}
			}		
				
		return $list_file;
		
	}	
	public static function convert_file($filename)
	{
		$new_file=$filename;
		$ps = pathinfo($filename);		
		$ext = $ps['extension'];
		if ($ext=='xls' || $ext=='xlsx'){
			$new_file = $ps['filename'] . '.html';
		}else{
			$new_file = $ps['filename'] .'_s.' . $ext . '.' . THUMBNAIL_IMAGE_FORMATT;
		}				
		return $new_file;
	}
	// add end NBKD-1033 Phong VNIT 2015/03/19
	//add start LIXD-287 Phong VNIT 20151104 Edit start LIXD-287 hung VNIT 20151104
	public static function SaveAsExcelFile($file_name,$replace_flg = false, $new_file_name='' )
	{	
        try{ //Edit Start LIXD-321 hungtn VNIT 20151111
            $excel = new Excel_lib();
            $isOneSheet = $excel->isOneSheet($file_name);
            if($isOneSheet === FALSE) {
                $file_name = str_replace("//", "/", $file_name);
        		$file_name = str_replace("/", "\\", $file_name);
        		$excel = new COM("Excel.Application") or $this->error("error");
                $excel->DisplayAlerts = 0;
        		$workbook = $excel->Workbooks->Open($file_name);
                 
        		$count_sheet = $workbook->Worksheets->Count();
                if($count_sheet > 1) {
                    for($i = 2; $i<=$count_sheet; $i++)
            		{
            			$workbook->Worksheets(2)->Delete();
            		}	
                }
        			
        		if($new_file_name == ''){
        			$new_file_name = self::Get_Excel_NewName($file_name, FALSE);
        		}
        		if($replace_flg == true) {
                    $workbook->Save();
                } else {
                    $workbook->SaveAs($new_file_name);
                }
        		$excel->application->ActiveWorkbook->Close("False");
                $excel->Quit();
        		unset($workbook);
        		unset($excel);
            } else {
                if($new_file_name == ''){
        			$new_file_name = self::Get_Excel_NewName($file_name, FALSE);
        		}
                $file_lib = new FileWindows_lib();
                if($file_lib->CopyFile($file_name, $new_file_name) == FALSE) {
                    ACWLog::debug_var("LIXD-287", "file copy error: ".$file_name);
                }
            }
            return $new_file_name;
        } catch (Exception $exc) {
            ACWLog::debug_var('EXCEL_APPLICATION_ERROR', $exc->getMessage());
        }
        //Edit End LIXD-321 hungtn VNIT 20151111
		//Edit End LIXD-287 hung VNIT 20151104
	}
	public static function Get_Excel_NewName($file_name, $creating = TRUE)
	{
		$file_name = str_replace("//", "/", $file_name);
		$file_name = str_replace("/", "\\", $file_name);
		$file = new File_lib();
		$new_name = dirname($file_name).'\\'. $file->GetBaseName($file_name).'_sheet_1.'.$file->GetExtensionName($file_name);	
		if($file->FileExists($new_name)== FALSE && $creating == TRUE)
		{
			return self::SaveAsExcelFile($file_name,false,$new_name);
		}
		return $new_name;
	}
	//add end LIXD-287 Phong VNIT 20151104
    
    //Add Start LIXD-321 hungtn VNIT 20151109
    public static function action_copyimagetoexcel() {
        // coppy data_org, remove img old
        $param = self::get_param(array(
            'img_old',
            'data_org',
            'tmp_name',
            'mei_id',
            'series_id',
            't_ctg_head_id',
            't_series_head_id',
            'data_id_org',
            'data_free'
            ));
        
       $tmppath = new Path_lib(AKAGANE_SERIES_TMP_PATH);
       $tmppath->combine($param['tmp_name']);
       $series_tmppath = new Path_lib(AKAGANE_SERIES_TMP_PATH);
       $series_tmppath->combine("tmp_".$param['tmp_name']);
       $file_lib = new File_lib();
        
       // delete data old
       $file_old =  $tmppath->get_full_path()."/".$param['img_old'];
       
       $file_old_ext = pathinfo($param['img_old'], PATHINFO_EXTENSION);      
       $file_old_thumb_name = pathinfo($param['img_old'], PATHINFO_FILENAME)."_s.".$file_old_ext.".jpg";
       $file_old_thumb = $tmppath->get_full_path()."/".$file_old_thumb_name;
       
       if($file_lib->FileExists($file_old)) {
           /*   if(!$file_lib->DeleteFile($file_old)) {
               ACWLog::debug_var("LIXD-10", "Delete old image free fail");
           }
           if(!$file_lib->DeleteFile($file_old_thumb)) {
               ACWLog::debug_var("LIXD-10", "Delete old image thumb free fail");
           }*/
           
       }
               
       //copy data org
       $file_org = '';
       $file_org_thumb = '';
       if(empty($param['data_id_org'])) {
           $file_org = $series_tmppath->get_full_path()."/".$param['data_free']."/".$param['data_org'];
           $file_org_thumb = $series_tmppath->get_full_path()."/".$param['data_free']."/".pathinfo($param['data_org'],PATHINFO_FILENAME)."_s.".pathinfo($param['data_org'],PATHINFO_EXTENSION).".jpg";
       } else {
           $file_org = $series_tmppath->get_full_path()."/".$param['data_id_org']."_".$param['data_org'];
           $file_org_thumb = $series_tmppath->get_full_path()."/".$param['data_id_org']."_".pathinfo($param['data_org'],PATHINFO_FILENAME)."_s.".pathinfo($param['data_org'],PATHINFO_EXTENSION).".jpg";
       }
       $file_new = $tmppath->get_full_path()."/".$param['data_org'];

       $file_ext = pathinfo($param['data_org'], PATHINFO_EXTENSION);
       $file_name_thumb = pathinfo($param['data_org'], PATHINFO_FILENAME) . "_s." . $file_ext.".jpg";

       if($file_lib->FileExists($file_org)) {
           if(!$file_lib->CopyFile($file_org, $file_new)) {
               ACWLog::debug_var("LIXD-10", "copy image free fail");
           }
           $file_new_thumb = str_replace($param['data_org'], $file_name_thumb, $file_new);
           if(!$file_lib->CopyFile($file_org_thumb, $file_new_thumb)) {
               ACWLog::debug_var("LIXD-10", "copy image thumb free fail");
           }
       } else {
           ACWLog::debug_var("LIXD-10", "copy image free fail");
       }
       
       $result['status'] = 'OK';
       return ACWView::json($result);
    }
    //Add Start LIXD-321 hungtn VNIT 20151109
    //add start LIXD-354 Phong VNIT 20151118
    public static function excel_to_html($tmp_name,$excel_name,$free_id = null, $yoyaku_flg = 0)
	{
		if($yoyaku_flg == 1){
			$path = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		}else{
			$path = new Path_lib(AKAGANE_SERIES_TMP_PATH);
		}
		$path->combine($tmp_name);
		if($free_id != null){
			$path->combine($free_id);
		}
		$path->combine('excel');
		$pathinfo = pathinfo($excel_name);
		$rndfull  = $path->get_full_path($excel_name);
		$srcname  = $pathinfo['filename'];	// 拡張子抜きで		
		
		$html_name = $path->get_full_path($srcname . '.html');		
		$execl = new Excel_lib();
		$rndfull= Itemfile_model::Get_Excel_NewName($rndfull);
		
		if ($execl->to_html($rndfull, $html_name) == false) {
			ACWError::add('series_file', 'EXCEL→HTML変換でエラーが発生しました。' . $execl->get_error());
			return false;
		}
      
        return $html_name;
    }
    public static function action_viewchildexcel()
	{
		$param = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
		$free_id = $url_param[3];				
		$sw = $url_param[2];	
		if(isset($url_param[4]) && $url_param[4]=='yoyaku'){
			$pathlib = new Path_lib(AKAGANE_YOYAKU_SERIES_TMP_PATH);
		}else{
			$pathlib = new Path_lib(AKAGANE_SERIES_TMP_PATH);	
		}
		$pathlib->combine($url_param[1]);
		if($free_id !='0'){
			$pathlib->combine($free_id);
		}
		$pathlib->combine('excel');		
		SeriesFile_lib::view($pathlib, $url_param[0], $sw, null);
		return ACWView::OK;
	}	
    //add start LIXD-354 Phong VNIT 20151118
    
    //Add Start LIXD-357 hungtn VNIT 20151123
    public static function action_exdldata() {
        $param = self::get_param(array('chk_multidl_select', 'series_id', 'ctg_id'));
        $result['status'] = 'NG';
        $result['msg'] = '';
        try{
            if(count($param['chk_multidl_select']) > 0) {
                $param['chk_multidl_select'] = array_unique($param['chk_multidl_select']);
                $file_url = self::create_file_download($param);
                $result['folder'] = urlencode($file_url);
                $result['status'] = 'OK';
                $result['filezip'] = $param['folder_zip'];
                if($param['status'] == FALSE) {
                    $result['status'] = 'NG';
                }
            }
        } catch (Exception $exc) {
            ACWLog::debug_var('LIXD-357', $exc->getMessage());
        }
        
        return ACWView::json($result);
    }
    
    public static function create_file_download(&$param) {
        $result = '';
        $file_lib = new File_lib();
        $param['folder_zip'] = '';
        $param['status'] = true;
        if(count($param['chk_multidl_select']) > 1) {
            
            $folder_zip = date('Ymd_His')."". rand(100000, 999999).uniqid();
            $return_folder = $param['ctg_id']."_".$param['series_id'].$folder_zip;
            //Add Start LIXD-418 hungtn VNIT 20160107
            $arr_find = array(
			    "/",
			    "\\",
			    ":",
			    "*",
			    '"',
			    "<",
			    ">",
			    "?",
			    "|"
			);
			$return_folder = str_replace($arr_find, "_", $return_folder);
            //Add End LIXD-418 hungtn VNIT 20160107
            $folder_download = AKAGANE_SERIES_TMP_PATH."/".$return_folder;
            
            $param['folder_zip'] = AKAGANE_SERIES_TMP_PATH."/".$folder_zip.'.zip';
            
            $file_lib->CreateFolder($folder_download);
            
            foreach($param['chk_multidl_select'] as $link) {
                self::copy_from_link($link, $folder_download, FALSE, $param);
            }
            
            $file_lib->sevenzip_compress($param['folder_zip'],$folder_download);
            $result = $return_folder.".zip";
        } else if(count($param['chk_multidl_select']) == 1) {
            
            $return_folder = date('Ymd_His')."". rand(100000, 999999).uniqid();
            $folder_download = AKAGANE_SERIES_TMP_PATH."/".$return_folder;
            $file_lib->CreateFolder($folder_download);
            $status = self::copy_from_link($param['chk_multidl_select'][0], $folder_download, true, $param);
            if($status == FALSE) {
                $param['status'] = false;
            }
            $tmp_link = explode("/", $param['chk_multidl_select'][0]);
            $result = $return_folder."/".$tmp_link[0];
        }
        
        return $result;
    }
    
    public static function copy_from_link($link, $tofolder, $one_file = false, &$param) {
        $tmp_link = explode("/", $link);
        if(count($tmp_link) > 0) {
            $file_lib = new File_lib();
            $get_data_flg = 0;
            $org_file_name = $tmp_link[0];
            $tmp_folder = '';
            $file_name = '';
            $free_id = '';
            $type = '';
            $source_path = '';
            
            foreach($tmp_link as $path) {
                if($path == 'itemfile') {
                    $get_data_flg = 1;
                }
                
                if($get_data_flg > 2) {
                   if($get_data_flg == 3) {
                       $file_name = $path;
                   } else if($get_data_flg == 4) {
                       $tmp_folder = $path;
                   } else if($get_data_flg == 5) {
                       $type = $path;
                   } else if($get_data_flg == 6) {
                       $free_id = $path;
                   }
                }
                if($get_data_flg > 0) {
                    $get_data_flg++;
                }
            }
            
            if($tmp_folder != '') {
                
                $source_path = AKAGANE_SERIES_TMP_PATH."/".$tmp_folder."/".$file_name;
                
                $has_file_flg = false;
                
                if($file_lib->FileExists($source_path) == FALSE) {
                    $source_path = AKAGANE_SERIES_TMP_PATH."/".$tmp_folder."/excel/".$file_name;
                    if($file_lib->FileExists($source_path) == FALSE) {
                        if(!is_numeric($free_id)) {
                            $source_path = AKAGANE_SERIES_TMP_PATH."/".$tmp_folder."/".$free_id."/".$file_name;
                            if($file_lib->FileExists($source_path) == FALSE) {
                                ACWLog::debug_var("LIXD-357", "File $org_file_name of Excel not found!");
                            } else {
                                $has_file_flg = true;
                            }
                        } else {
                            ACWLog::debug_var("LIXD-357", "File $org_file_name of Excel not found!");
                        }
                    }else {
                        $has_file_flg = true;
                    }
                } else {
                    $has_file_flg = true;
                }
                if($has_file_flg == true) {
                    
                    if($one_file == true) {
                        $extention = pathinfo($org_file_name, PATHINFO_EXTENSION);
                        $org_file_name = uniqid().'.'.$extention;
                        $param['folder_zip'] = $tofolder."/".$org_file_name;
                    }

                    self::copy_ex_auto($file_lib, $source_path, $tofolder, $org_file_name, $org_file_name);
                } else {
                    return false;
                }
                return true;
                /*
                if($file_lib->CopyFile($source_path, $tofolder."/".$org_file_name) == false) {
                    ACWLog::debug_var("LIXD-357", "File $org_file_name of Excel copy error");
                } else {
                    return true;
                }
                */
            }
        }
        return false;
    }
    
    public static function copy_ex_auto($file_lib, $source_path, $tofolder, $filename, $file_org) {
        if($file_lib->FileExists($tofolder."/".$filename)) {
            //gennew name
            $filename_tmp = pathinfo($filename, PATHINFO_FILENAME);
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            $file_tam = explode(".".$ext, $file_org);
            array_pop($file_tam);
            $file_org_tmp = implode("",$file_tam);
            $index = explode("_", $filename_tmp);
            $count_tmp = count($index);
            
            if($count_tmp > 1 && isset($index[$count_tmp-1])) {
                if(is_numeric($index[$count_tmp-1])) {
                    $file_new = $file_org_tmp."_".($index[$count_tmp-1]+1).".".$ext;
                    self::copy_ex_auto($file_lib, $source_path, $tofolder, $file_new, $file_org);
                } else {
                    self::copy_ex_auto($file_lib, $source_path, $tofolder, $file_org_tmp."_1.".$ext, $file_org);
                }
            } else {
                self::copy_ex_auto($file_lib, $source_path, $tofolder, $file_org_tmp."_1.".$ext, $file_org);
                return true;
            }
            
        } else {
            $file_lib->CopyFile($source_path, $tofolder."/".$filename);
            return true;
        }
    }
    
    public static function action_multidlex() {
        $param = self::get_param(array('folder', 'filezip', 'tmp'));
        $file_lib =  new File_lib();
        $param['folder'] = urldecode($param['folder']);
        
        $ext = pathinfo($param['folder'], PATHINFO_EXTENSION);
        if($ext == 'zip') {
            ACWView::download_file($param['folder'], $param['filezip']);
            self::multidlexafter($param['filezip'], $param['tmp'], $param['folder']);
        } else {
            $file_path = AKAGANE_SERIES_TMP_PATH."/".$param['folder'];
            
            if($file_lib->FileExists($param['filezip'])) {
                
                $file_name_tmp = explode("/", $file_path);
                $file_name_tmp = array_pop($file_name_tmp);
                
                try{
                   ACWView::download_file($file_name_tmp, $param['filezip']);
                   self::multidlexafter($param['filezip'], $param['tmp']);
                } catch (Exception $exc) {
                    ACWLog::debug_var('LIXD-357', $exc->getMessage());
                }
            }
        }
        
        return ACWView::OK;
    }
    
    public static function multidlexafter($file_path, $tmp_folder, $folder_before_zip = '') {
        
        $file_lib =  new File_lib();
        if($file_lib->FileExists($file_path)) {
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            if($ext == 'zip') {
                $file_lib->DeleteFile($file_path);
                $dir = pathinfo($file_path, PATHINFO_DIRNAME);
                $folder_delete = $dir."/".$folder_before_zip;
                $file_name_tmp = explode(".zip", $folder_delete);
                $folder_delete = $file_name_tmp[0];
                if($file_lib->FolderExists($folder_delete)) {
                    if($file_lib->DeleteFolder($folder_delete) == FALSE) {
                        ACWLog::debug_var("LIXD-357", "$file_path Delete File Download Error!");
                    } 
                    
                    //$file_lib->DeleteFolderCommandLine($folder_delete);
                }
                
            } else {
                $folder_delete = pathinfo($file_path, PATHINFO_DIRNAME);
                if($file_lib->FolderExists($folder_delete)) {
                    $file_lib->DeleteFolderCommandLine($folder_delete);
                }
            }
            
        } else {
            ACWLog::debug_var("LIXD-357", "$file_path Download Error!");
        }
        
        // delete file html 
        $file_list_rec = $file_lib->FileFolderList(AKAGANE_SERIES_TMP_PATH."/".$tmp_folder."/excel");
        if(count($file_list_rec) > 0) {
            foreach($file_list_rec as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                if($ext == 'html') {
                    $file_lib->DeleteFile(AKAGANE_SERIES_TMP_PATH."/".$tmp_folder."/excel/".$file);
                }
            }
        }
        
        return ACWView::OK;
    }
    //Add End LIXD-357 hungtn VNIT 20151123
}
/* ファイルの終わり */
