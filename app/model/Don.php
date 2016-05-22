<?php
/**
 * ユーザ管理
 *
*/
class Don_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}
	
	public static function validate($action, &$param)
	{
		switch ($action) {
            case 'update':
			return self::_validate_update($param);
                case 'index':
                    if($param['search_donvi_name'] != ''){
                        $s_donvi_name = $param['search_donvi_name'];
                        $param['s_donvi_name'] = strtolower($s_donvi_name);
                    }
                break;  
        }
		return true;
	}

	/**
	* 初期表示
	*/
	public static function action_index()
	{
		/*$param = self::get_param(array(
			'search_donvi_name'
		));*/
		$model = new Don_model();
		$rows = $model->get_kiemtra_rows(null);		
		return ACWView::template('don.html', array(
			'data_rows' => $rows,			
			//'search_donvi_name'=>$param['search_donvi_name'],
            'mode'=>'main',
            'menu'=>'#menu_kt'
		));
	}
    public static function action_history()
	{
		$param = self::get_param(array(
			'loaidon',
            'tu_ngay',
            'den_ngay',
            'maso_duyet',
            'trangthai',
            'don_no',
            'file_name',
            'default_flg'
		));
		$model = new Don_model();
        if(!isset($param['default_flg']) || $param['default_flg']!='1'){
            $date = new DateTime();
            $date->add(date_interval_create_from_date_string('-5 days'));
            $param['tu_ngay']=$date->format('d/m/Y');
            $param['default_flg'] = 1;
        }
		$rows = $model->get_don_rows($param);	
        	
		return ACWView::template('don.html', array(
			'data_rows' => $rows,			
			'search_data'=>$param,
            'mode'=>'history',
            'menu'=>'#menu_lsyc'
		));
	}
    public static function action_xoadon()
	{
		$param = self::get_param(array(
			'loaidon',
            'tu_ngay',
            'den_ngay',
            'maso_duyet',
            'trangthai',
            'don_no',
            'file_name',
            'default_flg'
		));
		$model = new Don_model();
        if(!isset($param['default_flg']) || $param['default_flg']!='1'){
            $date = new DateTime();
            $date->add(date_interval_create_from_date_string('-5 days'));
            $param['tu_ngay']=$date->format('d/m/Y');
            $param['default_flg'] = 1;
        }
		$rows = $model->get_don_rows($param);		
		return ACWView::template('xoadon.html', array(
			'data_rows' => $rows,			
			'search_data'=>$param,
            'mode'=>'history',
            'menu'=>'#menu_lsyc'
		));
	}
    public static function action_muonblank()
	{
		$param = self::get_param(array(			
            'file_name'
		));
		$model = new Don_model();
        $param['srch_muon']=1;
		//$rows = $model->get_don_rows($param);		
		return ACWView::template('muon.html', array(
			'data_rows' => array(),			
			'search_data'=>$param,
            'mode'=>'history'
		));
	}
    public static function action_muon()
	{
		$param = self::get_param(array(			
            'file_name'
		));
		$model = new Don_model();
        $param['srch_muon']=1;
		$rows = $model->get_don_rows($param);		
		return ACWView::template('muon.html', array(
			'data_rows' => $rows,			
			'search_data'=>$param,
            'mode'=>'history'
		));
	}
	/**
	 * 編集
	 * @return string
	 */
	public static function action_edit()
	{
		$params = self::get_param(array(
			'don_id'
		));
		$url_cmd = $_REQUEST['acw_url_cmd'];
        $url_info=explode('/',$url_cmd);
        $don_id = null;
        $loaidon = "";  // don moi
       // $don_cn = null;
        $quyen_tao_moi='1';
        $tieude_doncn = '';
        $noidung ='';
        $don_id_old ='';
        $don_no ='';
        $usr_kt='';
        $usr_duyet='';
        $usr_ttql='';
        $flg_muon ='0';
        $version =0;
        $flg_capnhat='0';
        $filelist = array();
        $data_row = array();
        $model = new Don_model();
        $file = new File_model();
        if(count($url_info)>=3){
            if($url_info[2] !='cn'){
               $don_id = $url_info[2]; 
            }else{
                $loaidon = "1"; // don cap nhat 
                $don_id_old = $url_info[3];
                $data_old = $model->get_don_row($don_id_old);
                $version = $data_old['version']+1;
                $tieude_doncn =preg_replace('/\[.+\]/','[Cập nhật:'.$version.']',$data_old['tieude']);
                $noidung= $data_old['noidung'];
                $don_no = $data_old['don_no'];
                $usr_kt=$data_old['user_kt'];;
                $usr_duyet=$data_old['user_duyet'];;
                $usr_ttql=$data_old['user_ttql'];;
                $filelist = $file->get_file_don($data_old['don_id'],'notpdf',DON_STATUS_DUYET_CN);// lấy những file được cho phép cập nhật
                if(count($filelist)== 0){
                    $filelist=$file->get_file_don($data_old['don_id'],'notpdf');
                    $flg_muon='0';
                }else{
                    $flg_muon='1';
                }
            }
        }
		
		
		
		$user_info = ACWSession::get('user_info');
		$usr = new  User_model();
		$kiemtra_list=$usr->get_user_bylevel(array('kiemtra'=>1));
		$duyet_list=$usr->get_user_bylevel(array('duyet'=>1));
		$ttql_list=$usr->get_user_bylevel(array('trungtam_quanly'=>1));
		$mode ="";
        
		if ($don_id == null) {			
			$mode ="new";
			$data_row['folder_tmp']=uniqid();
			$data_row['don_id'] = $don_id_old;
            $data_row['ver_id'] = null;
            $data_row['version'] = $version;
            $data_row['don_no'] = $don_no;
			$data_row['don_name'] = null;
			$data_row['user_name_disp'] = $user_info['user_name_disp'];
			$data_row['phongban_name'] = $user_info['phongban_name'];	
            $data_row['loaidon']= $loaidon;
            $data_row['tieude']= $tieude_doncn;
            $data_row['noidung']= $noidung;
            $data_row['add_datetime']= null;	
            $data_row['maso_duyet']= null;	
            $data_row['user_kt']= $usr_kt;		
            $data_row['user_duyet']= $usr_duyet;	
            $data_row['user_ttql']= $usr_ttql;	
            $data_row['ngay_kt']= null;
            $data_row['ngay_duyet']= null;	
            $data_row['ngay_ttql']= null;
            $data_row['ngay_duyet_cn']= null;
            $data_row['trangthai']=null;
            //$data_row['flg_capnhat']=$flg_capnhat;
            $data_row['tieude_doncn']=$tieude_doncn;
            if($user_info["upload"] == 0){
                $quyen_tao_moi='0';
            }
		} else {
			$data_row = $model->get_don_row($don_id);
            $data_row['folder_tmp']=uniqid();
			$mode ="";	
            $status = $data_row['trangthai'];
            $new_flg ='';
            $file_status ='';
            //$usr = new User_model();
            $login_info = ACWSession::get('user_info');            
            $level = $model->get_user_don($don_id,$user_info['user_id']);
            if(($status == "-1"||$status == DON_STATUS_TRA_KT||$status == DON_STATUS_TRA_DUYET||$status == DON_STATUS_TRA_TTQL) && $level["upload"] > 0 ){
                $mode ="upload";
                if($status == DON_STATUS_TRA_KT||$status == DON_STATUS_TRA_DUYET||$status == DON_STATUS_TRA_TTQL){
                    $flg_capnhat='1';
                }
                
            }
            else if($status == "0" && $level["kiemtra"]> 0 ){
                $mode ="kiemtra";
                $new_flg ="'1','2'";
            }else if($status == DON_STATUS_KT && $level["duyet"]> 0)
            {
                $mode="duyet";
                $new_flg ="'1','2'";
            }else if($status == DON_STATUS_DUYET && $level["ttql"]> 0)
            {
                $mode="ttql";
                $new_flg ="'1','2'";
            }else if($status == DON_STATUS_TTQL && $user_info["upload"] > 0 ){// user nao cung co quyen
                $mode ="xin_cn";
            }else if($status == DON_STATUS_DUYET_CN  && $user_info["upload"] > 0 ){ // user nao cung co quyen
                //if($model->check_don_capnhat_exist($don_id)==FALSE){
                    $mode ="duyet_cn";  // cho phep tao don cap nhat    
                //}
            }else if($status ==  DON_STATUS_XIN_CN && $level["ttql"] > 0 ){
                $mode ="cho_duyet_cn";  // cho duyet don xin cap nhat
                $file_status = DON_STATUS_XIN_CN;
            }	
           
            $filelist =$file->get_file_don($don_id,'notdwg',$file_status,$new_flg);
            $list_dl = ACWSession::get('file_download');
            $model->join_dl_file($filelist,$list_dl);
		}
        $bv = new Banve_model();
		return ACWView::template('upload.html', array(
			'data_row' => $data_row	
			,'kiemtra_list' =>$kiemtra_list
			,'duyet_list' =>$duyet_list
			,'ttql_list' =>$ttql_list
			,'mode'=>$mode
            ,'filelist'=>$filelist
            ,'loaidon'=>$loaidon
            ,'flg_muon'=>$flg_muon
            ,'flg_capnhat'=>$flg_capnhat
            ,'quyen_tao_moi'=>$quyen_tao_moi
            ,'dm_banve' =>$bv->get_banve_tong('1')
        ));
	}
	private function join_dl_file(&$filelist, $dl_list){
        if(count($dl_list)==0){
            return ;
        }
        foreach($filelist as &$item){            
            foreach($dl_list as $row){
                if($item['file_id']==$row){
                    $item['dl_flg']='1';
                }
            }
        }
    }
	public static function _validate_update(&$param)
	{
	    /**
	     * 
	     * */	    
		$validate = new Validate_lib();
		
		/*$param['donvi_name'] = $validate->trim_ext($param['donvi_name']);
		if ($validate->type_str('donvi_name', 'Tên đơn vị', $param['donvi_name'], true) == false) {
			return false;
		}        
        if($param['donvi_name'] != ''){
            if(strlen($param['donvi_name']) > 100){
                ACWError::add('lelng', 'Tên đơn vị không được quá 100 ký tự');
                return false;
            }
        }*/
		return true;
	}
    /*public static function action_checkmaxlenght() {
        
        $result['error'] = array();        
        return ACWView::json($result);    
    }*/
   
	
	/**
	* 更新
	*/
	public static function action_update()
	{
		$params = self::get_param(array(	
            'don_id',
            'ver_id',
            'don_no',		
			'loaidon',
			'tieude',
            'noidung',
            'version',
            'usr_kiemtra',
            'usr_duyet',
            'usr_ttql',
            'filelist',
            'folder_tmp',
            'trangthai',
            'mode',
            'ghichu'
            ,'don_cn'
            ,'flg_capnhat' // cap nhat lai file bi tra duyet
            ,'flg_muon'
		));		
		if (self::get_validate_result() === true) {
            $model = new Don_model();
            $fmodel= new File_model();
            if(($params['mode']=="new" && !isset($params['don_id'])) ){                
    			$file_list =explode('|', $params['filelist']);
                /*if(isset($params['don_cn']) && !empty($params['don_cn'])){
                    //$params['don_id_cu']=$params['don_cn'];
                    $params['tieude']='[Cập nhật] '.$params['tieude'];
                }*/ 
                $file_don_id=0;
                /*if($params['loaidon']=='1' && $params['flg_muon']=='1'){// mượn file
                    $file_don_id = $params['don_id'];
                }*/ 
                $check_don_no=TRUE; 
                /*if($file_don_id =='0')
                {
                    if( $model->check_don_no_exist($params['don_no'])===FALSE ){
                        ACWError::add('don_no', 'Mã bản vẽ: "'.$params['don_no'].'" đã có, vui lòng nhập mã khác!');
                        $check_don_no=FALSE;
                    } 
                }*/       
                if($fmodel->check_filename($file_list,$file_don_id) && $check_don_no){
        			$model->_insert($params);
        			$don_id = $params['don_id'];
                    //$ver_id = $params['ver_id'];
                    $result['don_id']=$don_id;
                   // $result['ver_id']=$ver_id;
        			$file = new File_lib();
                    /*if( $params['version'] > 0 ){
                        //ACWLog::debug_var('----sql---','copy');
                        $model->copy_file_up_version($don_id,$params['version']);
                    }*/
        			for($i=0 ;$i< count($file_list);$i++)
                    {
                        if(!empty($file_list[$i])){
                            $par_ins['file_name'] =$file_list[$i];
                            $par_ins['file_type'] =$file->GetExtensionName($file_list[$i]);
                            $par_ins['file_id'] = null;
                            $par_ins['don_id'] = $don_id;
                            //$par_ins['ver_id'] = $params['ver_id'];
                            $par_ins['flg_muon'] = $params['flg_muon'];
                            $par_ins['flg_capnhat'] = $params['flg_capnhat'];
                            $fmodel->update($par_ins); 
                        }
                    }
                    
                    $src_path = ACW_TMP_DIR_IMG.'/'.$params['folder_tmp'];
                    $desti_path =self::get_folder_data_name($don_id);
                    //ACWLog::debug_var('----file----','src: '.$src_path);
                    //ACWLog::debug_var('----file----','desti: '.$desti_path);
                    
                    $file->MoveFolder($src_path,$desti_path);   
                                  
                    //$model->send_mail($don_id,0);
    			}
            }else{
                $result['don_id']=$params['don_id'];
                $id_don_cn=0;
                $flg_upd_file=TRUE;
                if($params['flg_capnhat']=='1'){
                    $id_don_cn = $params['don_id'];
                    $params['trangthai']=-1;
                    $flg_upd_file=FALSE;
                }
                //ACWLog::debug_var('---file----','1');
                if(isset($params['filelist']) && !empty($params['filelist'])){
                    $file_list =explode('|', $params['filelist']);     
                    //ACWLog::debug_var('---file----','2');               
                    if($fmodel->check_filename($file_list,$id_don_cn)){ 
                        //ACWLog::debug_var('---file----','count list: '.count($file_list));                   
                        $file = new File_lib();
                        $tmp_path = ACW_TMP_DIR_IMG.'/'.$params['folder_tmp'];
                        $desti_path =self::get_folder_data_name($params['don_id']);
                        //them file
            			for($i=0 ;$i< count($file_list);$i++)
                        {
                            $par_ins['file_name'] =$file_list[$i];
                            $par_ins['file_type'] =$file->GetExtensionName($file_list[$i]);
                            $par_ins['file_id'] = null;
                            $par_ins['don_id'] = $params['don_id'];
                            //$par_ins['ver_id'] = $params['ver_id'];
                            $par_ins['flg_capnhat'] = $params['flg_capnhat'];
                            $fmodel->update($par_ins);
                            $src_file = $tmp_path.'/'.$file_list[$i];  
                            $desti_file =$desti_path.'/'.$file_list[$i]; 
                            //ACWLog::debug_var('---file----','src_file: '.$src_file);  
                            //ACWLog::debug_var('---file----','desti_file: '.$desti_file); 
                            $file->DeleteFile($desti_file);    
                            $res=$file->MoveFile($src_file,$desti_file);
                            //ACWLog::debug_var('---file----','res: '.$res); 
                        }
                    }    
                }
                
                if($model->_update($params,$flg_upd_file)){
                    if($params['mode']=="ttql" && $params['trangthai'] == DON_STATUS_TTQL){
                        //$desti_path =self::get_folder_data_name($params['don_id']);
                        //$model->move_folder_main($desti_path); 
                        $main_folder = $model->update_folder_main($params['don_id']); 
                        $filelist = $fmodel->get_file_don($params['don_id'],'dwg','',1);
                        $model->insert_file_pdf($main_folder,$params['don_id'],$filelist); 
                        $model->convert_dwg_to_pdf($main_folder,$filelist) ;                    
                    }
                    $model->send_mail($params['don_id'],$params['trangthai']);
                }
                
            }
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
            $result['msg'] =Message_model::get_msg('SYS001');// "Cập nhật thành công !";
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}    
    public static function action_muonbanve()
	{
		$params = self::get_param(array(	
            'don_id',
           // 'ver_id',
            'lydo_muon',		
			'file_muon',
            'ngay_hen_tra',
            'mode'
		));	
        try{
            $model= new Don_model();
            $f = new File_model();
            $param_upd['status']= DON_STATUS_XIN_CN ;  //7: muon file
            if(isset($params['file_muon']) && !empty($params['file_muon']))
            {
                $list_muon="";
                foreach($params['file_muon'] as $item){
                    $info = $f->get_file_row($item);
                    $list_muon .= "\n".$info['file_name']; 
                    $param_upd['file_id']=$item;
                    $f->update_file($param_upd);
                }
            }
            $info = $model->get_don_row($params['don_id']);
            $params['noidung']= $info['noidung']."\n--------------\nNgày hẹn trả: ".$params['ngay_hen_tra']   ; 
            $params['noidung'] .= "\nLý do mượn: ".$params['lydo_muon']   ; 
            $params['noidung'] .= "\nFile mượn :";
            $params['noidung'] .= $list_muon;
            $params['trangthai']=DON_STATUS_XIN_CN;            
            if($model->_update($params,FALSE)){	
                $model->send_mail($params['don_id'],DON_STATUS_XIN_CN);
            }
        }catch (Exception $exc) {
            $result['status'] = 'NG';
			$result['error'] = $exc->getMessage();
        }
        if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
            $result['msg'] =Message_model::get_msg('SYS001');//"Cập nhật thành công !";
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
    }
    public static function action_trayeucau()
	{
		$params = self::get_param(array(	
            'don_id',
            'ver_id',
            'ghichu',		
			'file_tra',
            'trangthai',
            'mode'
		));	
        try{
            $model= new Don_model();
            $f = new File_model();
            $param_upd['status']= $params['trangthai'];  //tra file
            if(isset($params['file_tra']) && !empty($params['file_tra']))
            {
                $list_del='';
                foreach($params['file_tra'] as $item){
                    //$param_upd['file_id']=$item;
                    //$f->update_file($param_upd);
                    $info = $f->get_file_row($item);
                    $list_del .= "\n".$info['file_name'];                    
                    $f->delete_file($item,$params['trangthai']);
                }
                //update duyet cho nhung don khong bi tra
                /*$par_duyet['don_id']=$params['don_id'];
                $par_duyet['ver_id']=$params['ver_id'];
                if($params['trangthai'] == DON_STATUS_TRA_KT){
                    $par_duyet['status_old'] = DON_STATUS_NEW;
                    $par_duyet['status'] = DON_STATUS_KT;
                    $f->update_file_don($par_duyet);
                }else if($params['trangthai'] == DON_STATUS_TRA_DUYET){
                    $par_duyet['status_old'] = DON_STATUS_KT;
                    $par_duyet['status'] = DON_STATUS_DUYET;
                    $f->update_file_don($par_duyet);
                }else if($params['trangthai'] == DON_STATUS_TRA_TTQL){
                    $par_duyet['status_old'] = DON_STATUS_DUYET;
                    $par_duyet['status'] = DON_STATUS_TTQL;
                    $f->update_file_don($par_duyet);
                }*/
            }
            //$params['trangthai']=DON_STATUS_XIN_CN; 
            $info = $model->get_don_row($params['don_id']);
            $login_info = ACWSession::get('user_info');
            $par_upd['don_id']= $params['don_id']  ;  
            $par_upd['noidung']= $info['noidung']."\n--------------\n".$login_info['user_name_disp'] .' trả'  ; 
            $par_upd['noidung'] .= "\nLý do trả: ".$params['ghichu']   ; 
            $par_upd['noidung'] .= "\nFile trả :";
            $par_upd['noidung'] .= $list_del;
              
            if($model->update_noidung($par_upd,FALSE)){
                if($f->count_file($params['don_id'])== '0' ){
                    $model->delete_don($params['don_id']);
                }
                $model->send_mail($params['don_id'],$params['trangthai'],$params['ghichu']);
            }
        }catch (Exception $exc) {
            $result['status'] = 'NG';
			$result['error'] = $exc->getMessage();
        }
        if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
            $result['msg'] =Message_model::get_msg('SYS001');//"Cập nhật thành công !";
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
    }
    public function update_noidung($param){
        $sql ="update don t set 
                t.noidung = :noidung
                ,t.upd_datetime=NOW() 
                ,t.upd_user_id =:upd_user_id
               where don_id = :don_id ";
        $login_info = ACWSession::get('user_info');
        $par_upd['don_id']= $param['don_id']  ;  
        $par_upd['noidung']= $param['noidung'];
        $par_upd['upd_user_id']= $login_info['user_id'];
        $this->execute($sql,$par_upd);
        return TRUE;
    }
    public static function action_delete()
	{
		$params = self::get_param(array(	
            'don_id'           
		));
        $db = new Don_model();
        if($db->delete_don($params['don_id'])){
            
            $file= new File_model();
            $file->delete_file_don($params['don_id']);
            //$db->delete_ver($params['ver_id']);
        }	
        if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
            $result['msg'] =Message_model::get_msg('SYS002');//"Xóa đơn thành công !";
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
    }
    public function check_ver_delete($ver_id){
        $sql="select trangthai,add_user_id from ver where ver_id= :ver_id";
        $result = $this->query($sql,array('ver_id'=>$ver_id));
        if(count($result) >0 ){
            if($result[0]['trangthai'] !='-1'){
                ACWError::add('ver', Message_model::get_msg('SYS003')); //'Đơn này đã gửi duyệt không thể xóa'
                return false;
            }
            $login_info = ACWSession::get('user_info');
            if($result[0]['add_user_id'] !=$login_info['user_id']){
                ACWError::add('ver',Message_model::get_msg('SYS003')); // 'Bạn không có quyền xóa đơn này,vì bạn không phải là người tạo !'
                return false;
            }
        }
        return TRUE;
    }
    public function delete_don($don_id){
        $sql="update don set del_flg=1 ,upd_datetime=now(),upd_user_id=:upd_user_id
                where don_id=:don_id";
        $login_info = ACWSession::get('user_info');
        $result = $this->execute($sql,array('don_id'=>$don_id,'upd_user_id'=>$login_info['user_id']));
        return TRUE;
    }
    public function delete_ver($ver_id){
        $sql="update ver set del_flg=1 ,upd_datetime=now(),upd_user_id=:upd_user_id
                where ver_id=:ver_id";
        $login_info = ACWSession::get('user_info');
        $result = $this->execute($sql,array('ver_id'=>$ver_id,'upd_user_id'=>$login_info['user_id']));
        return TRUE;
    }
	public function get_don_rows($param)
	{
		$sql = "select DISTINCT t.don_id,
              t.don_no,
              t.tieude,
              t.noidung,            
              t.loaidon,
              t.trangthai,
              t.user_kt,
              t.user_duyet,
              t.user_ttql,
              t.maso_duyet,
              t.bophan,
              t.ngay_kt,
              t.ngay_duyet,
              t.ngay_ttql,
              t.ngay_duyet_muon ngay_duyet_cn,
              t.add_user_id,
							u.user_name_disp ,
              ad.user_name_disp  add_user,
            	DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime    ,
                (case 
				  when t.trangthai  = 0 or t.trangthai  = 1 or t.trangthai  = 2 then 'Đang chờ'
				  when t.trangthai  = 3 then 'Đã duyệt'
				  when t.trangthai  = 4 or t.trangthai  = 5 or t.trangthai  = 6 then 'Không duyệt'
                  when t.trangthai  = 7 then 'Xin mượn bản vẽ'
                  when t.trangthai  = 8 then 'Trả yêu xin mượn bản vẽ'
                  when t.trangthai  = 9 then 'Đã duyệt xin mượn bản vẽ'
				  end)  as status_name
                from don t	
				    left join m_user u on u.user_id = t.upd_user_id
                    left join m_user ad on ad.user_id = t.add_user_id
				LEFT JOIN file as f on f.don_id = t.don_id  and f.file_type='dwg'
                where t.del_flg=0
                and t.trangthai <> -1 ";
		
        $sql_param = array();
		if (isset($param['loaidon']) ) {
			$sql_param['loaidon'] =$param['loaidon'];
			$sql .= " and t.loaidon = :loaidon ";
		}
        if (isset($param['don_no']) && empty($param['don_no'])==FALSE) {
			$sql_param['don_no'] =SQL_lib::escape_like($param['don_no']);
			$sql .= " and lower(t.don_no) like lower(:don_no) ";
		}
		if (isset($param['maso_duyet']) && empty($param['maso_duyet'])==FALSE) {
			$sql_param['maso_duyet'] =SQL_lib::escape_like($param['maso_duyet']);
			$sql .= " and lower(t.maso_duyet) like lower(:maso_duyet) ";
		}
		if (isset($param['file_name']) && empty($param['file_name'])==FALSE) {
			$sql_param['file_name'] = '%'.(SQL_lib::escape_like($param['file_name'])).'%';
			$sql .= " and lower(f.file_name) like lower(:file_name )";
		}
        if (isset($param['srch_muon']) && empty($param['srch_muon'])==FALSE) {
			$sql .= " and f.`status` <> 9";
		}
        if (isset($param['trangthai']) ) {
            //$sql .= " and t.trangthai = :trangthai ";
            if($param['trangthai'] =="0") // dang Cho
            {
                $sql .= " and t.trangthai in (".DON_STATUS_NEW.",".DON_STATUS_KT.",".DON_STATUS_DUYET.")";
            }else if($param['trangthai'] =="1") //da duyet
            {
                $sql .= " and t.trangthai = ".DON_STATUS_TTQL;
            }else if($param['trangthai'] =="2")// không duyệt
            {
                $sql .= " and t.trangthai in (".DON_STATUS_TRA_KT.",".DON_STATUS_TRA_DUYET.",".DON_STATUS_TRA_TTQL.")";
            }else if($param['trangthai'] =="3")// xin muon ban ve
            {
                $sql .= " and t.trangthai = ".DON_STATUS_XIN_CN;
            }else if($param['trangthai'] =="4")// tra yeu cau muon ban ve
            {
                $sql .= " and t.trangthai = ".DON_STATUS_TRA_CN;
            }else if($param['trangthai'] =="5")// duyet muon ban ve
            {
                $sql .= " and t.trangthai = ".DON_STATUS_DUYET_CN;
            }
            
			//$sql_param['trangthai'] =$param['trangthai'];
		}
        $sql_param['tu_ngay'] ='00/00/0000';
        //$sql_param['den_ngay'] ='SYSDATE()';
        if (isset($param['tu_ngay']) && empty($param['tu_ngay'])==FALSE) {
			$sql_param['tu_ngay'] = $param['tu_ngay'];
		}
        if (isset($param['den_ngay']) && empty($param['den_ngay'])==FALSE) {
			$sql_param['den_ngay'] = $param['den_ngay'].' 23:59';
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y') and STR_TO_DATE(:den_ngay,'%d/%m/%Y %H:%i')";
		}else{
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and SYSDATE()";
        }
        //$sql .= "and t.add_datetime between :tu_ngay and :den_ngay";
        $login_info = ACWSession::get('user_info');
        $usr = new User_model();
        if($usr->check_only_upload($login_info['user_id'])){
            $sql .= " and t.add_user_id = ". $login_info['user_id'];
        }
		$sql .= "
			ORDER BY
				t.don_id
		";		
		return $this->query($sql, $sql_param);
	}
	public function get_kiemtra_rows($param,$kiemtra=FALSE)
	{
		$sql = "select DISTINCT t.don_id,
              t.don_no,
              t.tieude,
              t.noidung,            
              t.loaidon,
              t.trangthai,
              t.user_kt,
              t.user_duyet,
              t.user_ttql,
              t.maso_duyet,
              t.bophan,
              t.ngay_kt,
              t.ngay_duyet,
              t.ngay_ttql,
              t.ngay_duyet_muon ngay_duyet_cn,
              t.add_user_id,
							u.user_name_disp ,
              ad.user_name_disp  add_user,
            	DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime    ,
                (case 
				  when t.trangthai  = 0 or t.trangthai  = 1 or t.trangthai  = 2 then 'Đang chờ'
				  when t.trangthai  = 3 then 'Đã duyệt'
				  when t.trangthai  = 4 or t.trangthai  = 5 or t.trangthai  = 6 then 'Không duyệt'
                  when t.trangthai  = 7 then 'Xin mượn bản vẽ'
                  when t.trangthai  = 8 then 'Trả yêu xin mượn bản vẽ'
                  when t.trangthai  = 9 then 'Đã duyệt xin mượn bản vẽ'
				  end)  as status_name
                from don t	
					left join m_user u on u.user_id = t.upd_user_id
                    left join m_user ad on ad.user_id = t.add_user_id
				LEFT JOIN file as f on f.don_id = t.don_id  and f.file_type='dwg'
                where t.del_flg=0
                ";
        $login_info = ACWSession::get('user_info');
        $count = 0;
        $sql .=" and (";
        if($login_info['upload'] > 0){
            $sql .=" (t.trangthai in (-1,0,1,2) and t.add_user_id=" .$login_info['user_id'].")";
            $count++;
        }
        if($login_info['trungtam_quanly'] > 0){
            if($count>0 ){
                $sql .=" or (t.trangthai in (0,1,2,7) and t.user_ttql=" .$login_info['user_id'].")";
            }else{
                $sql .=" (t.trangthai in (0,1,2,7) and t.user_ttql=" .$login_info['user_id'].")";
            }
            $count++;
        }
        if($login_info['upload'] > 0){
            if($count>0 ){
                $sql .=" or (t.trangthai in (-1,0,1,2) and t.add_user_id=" .$login_info['user_id'].")";
            }else{
                $sql .=" (t.trangthai in (-1,0,1,2) and t.add_user_id=" .$login_info['user_id'].")";    
            }
            $count++;
        }
        if($login_info['kiemtra'] > 0){
            if($count>0 ){
                $sql .=" or (t.trangthai in (0,1,2) and t.user_kt=" .$login_info['user_id'].")";
            }else{
                $sql .=" (t.trangthai in (0,1,2) and t.user_kt=" .$login_info['user_id'].")";
            }
            $count++;
        }
        if($login_info['duyet'] > 0){
            if($count>0 ){
                $sql .=" or (t.trangthai in (0,1,2) and t.user_duyet=" .$login_info['user_id'].")";
            }else{
                $sql .=" (t.trangthai in (0,1,2) and t.user_duyet=" .$login_info['user_id'].")";
            }
        }     
        $sql .=" )";            
		return $this->query($sql);
	}	
	public function _insert(&$params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		//$maxno = $this->get_donno_max()+1;
       // $don_no	= str_pad($maxno, 7, "0", STR_PAD_LEFT); 	
		$sql_params = array(
            'don_no'=>$params['don_no'],
            'tieude'=>$params['tieude'],
            'noidung'=>$params['noidung'],
            //'don_id_cu'=>$params['don_cn'],
            'loaidon'=>$params['loaidon'],
            //'trangthai'=>$params['trangthai'],
            'user_kt'=>$params['usr_kiemtra'],
            'user_duyet'=>$params['usr_duyet'],
            'user_ttql'=>$params['usr_ttql'],
            //'maso_duyet'=>$params[''],
            'bophan'=>$login_info['phong_ban'],
            //'ngay_kt'=>$params[''],
            //'ngay_duyet'=>$params[''],
            //'ngay_ttql'=>$params[''],
            'add_user_id'=>$login_info['user_id'],
            'upd_user_id'=>$login_info['user_id']
		);		
		//ACWLog::debug_var('---sql---',$sql_params);
	    //if($params['loaidon']=='0'){
            $sql = "INSERT INTO	don
				(					
                    don_no,
                    tieude,
                    noidung,
                --    don_id_cu,
                    loaidon,
                   trangthai,
                    user_kt,
                    user_duyet,
                    user_ttql,                   
                    bophan,
                    add_user_id,
                    add_datetime,
                    upd_user_id,
                    upd_datetime
				) VALUES (
					
                    :don_no,
                    :tieude,
                    :noidung,
                --    don_id_cu,
                    :loaidon,
                    -1,
                    :user_kt,
                    :user_duyet,
                    :user_ttql,                   
                    :bophan,                    
					:add_user_id ,
					 NOW() ,
					:upd_user_id ,
					NOW() 
				);
			";
            if($sql_params['loaidon']=='1'){
                $sql_params['tieude'] = '[Cập nhật] '.$params['tieude'];
            }else{
                $sql_params['tieude'] = '[Thiết kế mới] '.$params['tieude'];
            }
            
		    $this->execute($sql, $sql_params);
            $result = $this->query("SELECT LAST_INSERT_ID() AS don_id");
            $params['don_id']= $result[0]['don_id'];
            
            //$params['version'] = 0;
        /*}else{
            
        } */       
        //$params['ver_id'] = $this->insert_ver($params);
		//eturn $result[0]['don_id'];
        $this->commit();
	}
    public function insert_ver($params){
        $sql_ver="INSERT INTO ver(
                    don_id,
                    version,
                    loaidon,
                    tieude,
                    noidung,
                    trangthai,
                    add_user_id,
                    add_datetime,
                    upd_user_id,
                    upd_datetime
                    ) VALUES (
                    :don_id,
                    :version,
                    :loaidon,
                    :tieude,
                    :noidung,
                    -1,
                    :add_user_id,
                    now(),
                    :add_user_id,
                    now()
                    )";
        $login_info = ACWSession::get('user_info');
        $param_ver['don_id']=$params['don_id'];
        $param_ver['version']=$params['version'];
        $param_ver['tieude']=$params['tieude'];
        $param_ver['loaidon']=$params['loaidon'];
        $param_ver['noidung']=$params['noidung'];
        $param_ver['add_user_id']=$login_info['user_id'];
        $this->execute($sql_ver, $param_ver);
        $result = $this->query("SELECT LAST_INSERT_ID() AS ver_id");
        return $result[0]['ver_id'];
    }
	public function _update($params,$flg_update_file=TRUE)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');		
		$sql_params = array(
            'upd_user_id'=>$login_info['user_id'],
            'don_id'=>$params['don_id'],
           // 'ver_id'=>$params['ver_id']
           // 'trangthai'=>$params['trangthai'],
            
		);		
        $sql = "update don v set 
                v.upd_datetime=NOW() 
                ,v.upd_user_id =:upd_user_id
                ";
        if(isset($params['noidung']) && !empty($params['noidung'])){
            //$sql .= " ,v.noidung = CONCAT(:noidung,COALESCE(CONCAT('----------------\n',v.ghichu),''))";
            $sql .= " ,v.noidung = :noidung ";
            $sql_params['noidung']= $params['noidung'];
        }
        if(isset($params['ghichu']) && !empty($params['ghichu'])){
            $sql .= " ,v.ghichu = :ghichu ";
            $sql_params['ghichu']= $params['ghichu'];
        }/*else{
            $sql .= " ,v.ghichu = null";
        }*/
        if(isset($params['lydo_muon']) && !empty($params['lydo_muon'])){
            $sql .= " ,v.lydo_muon = :lydo_muon ";
            $sql_params['lydo_muon']= $params['lydo_muon'];
        }
        if(isset($params['ngay_hen_tra']) && !empty($params['ngay_hen_tra'])){
            $sql .= " ,v.ngay_hen_tra = STR_TO_DATE(:ngay_hen_tra,'%d/%m/%Y') ";
            $sql_params['ngay_hen_tra']= $params['ngay_hen_tra'];
        }
        if(isset($params['trangthai']) ){
            $sql .= " ,v.trangthai = :trangthai ";
            $sql_params['trangthai']= $params['trangthai'];
            if($params['trangthai'] == DON_STATUS_TTQL){
                $sql .= " ,v.maso_duyet= LPAD(v.don_id,7,'0')";
                $sql_file="update file set status =".DON_STATUS_TTQL." where don_id=".$params['don_id'];
                $this->execute($sql_file); 
            }
            
            if($params['mode']=="kiemtra"){
            $sql .= " ,v.ngay_kt=NOW() ";
            }else if($params['mode']=="duyet"){
                $sql .= " ,v.ngay_duyet=NOW() ";
            }else if($params['mode']=="ttql"){
                $sql .= " ,v.ngay_ttql=NOW() ";
            }else if($params['mode']=="cho_duyet_cn"){
                $sql .= " ,v.ngay_duyet_muon=NOW() ";
            }
        }
        //$sql_params['don_id']=$params['don_id'];
	    $sql .= " where v.don_id=:don_id ";	
        if($params['mode']=="kiemtra"){
            $sql .= " and v.user_kt=:upd_user_id ";
        }else if($params['mode']=="duyet"){
            $sql .= " and v.user_duyet=:upd_user_id ";
        }else if($params['mode']=="ttql"){
            $sql .= " and v.user_ttql=:upd_user_id ";
        }else if($params['mode']=="upload"){
            $sql .= " and v.add_user_id=:upd_user_id ";
        }
		$this->execute($sql, $sql_params);		
		$fmodel = new File_model();
        $pa_file = ACWArray::filter($params,array('don_id'	));
       
        $pa_file['status']=$params['trangthai'];
      
        if($pa_file['status'] == DON_STATUS_TRA_CN || $pa_file['status'] == DON_STATUS_DUYET_CN ){
            $pa_file['status_old'] = DON_STATUS_XIN_CN;
        }
        if($pa_file['status'] == DON_STATUS_NEW){
            $pa_file['status_old'] = '-1';
        }else if($pa_file['status'] == DON_STATUS_KT){
            $pa_file['status_old'] = DON_STATUS_NEW;
        }else if($pa_file['status'] == DON_STATUS_DUYET){
            $pa_file['status_old'] = DON_STATUS_KT;
        }else if($pa_file['status'] == DON_STATUS_TTQL){
            $pa_file['status_old'] = DON_STATUS_DUYET;
        }
       // ACWLog::debug_var('-----upd-----','trang thai:'.$params['trangthai']);
        if(!(isset($params['trangthai']) && strlen($params['trangthai'])>0)){
            $flg_update_file = FALSE;
        }
        if($flg_update_file ){
            $fmodel->update_file_don($pa_file);
        }
        
		$this->commit();
		
		return TRUE;
	}	
	public function get_donno_max()
	{
		$rows = $this->query("
				SELECT
					max(t.don_no) don_no
				FROM
					don t
				
			"
		);
	    if(count($rows) > 0){
            return $rows[0]['don_no'];
        }
		return '0';
	}	
	public function get_don_row($don_id)
	{
		$rows = $this->query("
				select t.don_id,
                  t.don_no,
                  t.tieude,
                  t.noidung,
                  t.loaidon,
                  t.trangthai,
                  t.user_kt,
                  t.user_duyet,
                  t.user_ttql,
                  t.maso_duyet,
                  t.bophan,
                  t.ghichu,
                  t.lydo_muon,                  
                  DATE_FORMAT(t.ngay_kt,'%d/%m/%Y %H:%i:%s')  ngay_kt,
                  DATE_FORMAT(t.ngay_duyet,'%d/%m/%Y %H:%i:%s') ngay_duyet,
                  DATE_FORMAT(t.ngay_ttql,'%d/%m/%Y %H:%i:%s') ngay_ttql,
                  DATE_FORMAT(t.ngay_duyet_muon,'%d/%m/%Y %H:%i:%s') ngay_duyet_cn,
                  DATE_FORMAT(t.ngay_hen_tra,'%d/%m/%Y') ngay_hen_tra,
                  t.add_user_id,
                	DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime,
                	us.user_name_disp,
                	p.phongban_name
                    from don t										
                		LEFT JOIN m_user us on us.user_id = t.add_user_id
                		LEFT JOIN phong_ban p on p.phongban_id= t.bophan
                                where t.don_id = :don_id
			", array("don_id" => $don_id)
		);
		
		return $rows[0];
	}
	public function get_user_name_don($don_id){
        $sql="select us.user_name usr_kt, d.user_name usr_duyet
               from don t										
            LEFT JOIN m_user us on us.user_id = t.user_kt
            LEFT JOIN m_user d on d.user_id = t.user_duyet
            where don_id = :don_id ";
        $res = $this->query($sql,array('don_id'=>$don_id));
        if(count($res)> 0){
            return $res[0];
        }
        return NULL;
    }
	private function get_donvi_name_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				don_vi
			WHERE
				donvi_name = :donvi_name
		";
		
		$filter = ACWArray::filter($param, array('donvi_name'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	public static function get_folder_data_name($don_id, $new=TRUE)
    {
        $folder_name = "";
        $f = new File_lib();
        if($new){
            $folder_name = DATA_TMP_PATH.'/'.'D'.str_pad($don_id, 10, "0", STR_PAD_LEFT);
            /*if(!$f->FolderExists($folder_name)){
                $f->CreateFolder($folder_name);
            }*/
            //$folder_name .= '/'.'V'.str_pad($ver_id, 7, "0", STR_PAD_LEFT);
        }            
        else{
            $folder_name = DATA_MAIN_PATH.'/'.'D'.str_pad($don_id, 10, "0", STR_PAD_LEFT);
            /*if(!$f->FolderExists($folder_name)){
                $f->CreateFolder($folder_name);
            }*/
            //$folder_name .= '/'.'V'.str_pad($ver_id, 7, "0", STR_PAD_LEFT);
        }            
        return $folder_name;
    }
    public function move_folder_main($folder_pạth){
        $file = new File_lib();
        $new_folder = str_replace(DATA_TMP_PATH,DATA_MAIN_PATH,$folder_pạth) ;
        $file->MoveFolder($folder_pạth,$new_folder);
    }
    public function update_folder_main($don_id){
        $file = new File_lib();
        $tmp = self::get_folder_data_name($don_id);
        $main = self::get_folder_data_name($don_id,FALSE);
       
        $info = $this->get_don_row($don_id);
        if(isset($info['don_cn']) && !empty($info['don_cn'])){
            $old_folder = self::get_folder_data_name($info['don_cn'],FALSE);
            $file->CopyFolder($old_folder,$main);
            $filelist = $file->FileList($tmp);
            foreach($filelist as $item){
                $file->MoveFile($tmp.'/'.$item,$main.'/'.$item);
            }
            //$file->CopyFolder($tmp,$main);
            $file->DeleteFolder($tmp);
        }else{
            $file->MoveFolder($tmp,$main);
        }
        return $main;
    }
    public function copy_file_up_version($don_id,$ver_id,$version){
        $file = new File_lib();
        $tmp = self::get_folder_data_name($don_id,$ver_id);
        $ver_id_old = $this->get_ver_id($don_id,$version-1);
        $old_folder = self::get_folder_data_name($don_id,$ver_id_old,FALSE);
        $file->CopyFolder($old_folder,$tmp);
        $this->insert_file_up_version($don_id,$ver_id,$ver_id_old);
        return $tmp;
    }
    public function get_ver_id($don_id,$section){
        $sql="select ver_id  from ver 
                WHERE don_id = :don_id
                and version = :version";
        $res = $this->query($sql,array('don_id'=>$don_id,'version'=>$section));
        if(count($res) >0){
            return $res[0]['ver_id'];  
        }
        return 0;
    }
    public function insert_file_up_version($don_id,$ver_id,$old_ver_id){
        $sql="INSERT into file(file_name,don_id,ver_id,file_path,file_type,status,new_flg,
                    add_user_id,add_datetime,upd_user_id,upd_datetime)
            select file_name,don_id,:ver_id,file_path,file_type,3,0,
                    add_user_id,now(),upd_user_id,now() from file
            where  don_id = :don_id
            and ver_id =:old_ver_id
            and status = 3 ";
        $pa_sql['don_id'] = $don_id;
        $pa_sql['ver_id'] = $ver_id;
        $pa_sql['old_ver_id'] = $old_ver_id;
        /*ACWLog::debug_var('----sql---',$sql);
        ACWLog::debug_var('----sql---',$pa_sql);*/
        $this->begin_transaction();
        $this->execute($sql,$pa_sql);
        $this->commit();
        return TRUE;
    }
    public static function action_sendmail()
    {
        $mode = new Don_model();
        $mode->send_mail(21,0);
        return ACWView::json('OK');
    }
	public function send_mail($don_id,$status,$ghichu='')
    {
        if($status == -1 ){
            return;
        }
        $file = new File_model();
        $login_info = ACWSession::get('user_info');
        $don_info= $this->get_don_row($don_id);
        $body_tmp = '
                <p>
                Ngày：　'.date('d/m/Y H:i:s').'
                </p>
                <p>
                --------------------------------
                </p>
                <p>
                Tiêu đề：　'.$don_info['tieude'].'
                </p>
                <p>
                Nội dung：　'.$don_info['noidung'].'
                </p>
                <p>
                Người tạo：　'.$don_info['user_name_disp'].'
                </p>
                <p>
                Ngày tạo：　'.$don_info['add_datetime'].'
                </p>
                <p>
                Link web：　'.ACW_BASE_URL.'don/edit/'.$don_info['don_id'].'
                </p>
                <p>
                --------------------------------
                </p>
			    ';
            $flg_muon=FALSE;
            if($don_info['trangthai']== DON_STATUS_XIN_CN){
                
                $filemuon =$file->get_file_don($don_id,'notpdf',DON_STATUS_XIN_CN,'');
                if(count($filemuon) > 0){
                    $flg_muon = TRUE;
                    $body_tmp.="<p>Lý do mượn: " . $don_info['lydo_muon']."</p>";
                    $body_tmp.="<p>Ngày hẹn trả: " . $don_info['ngay_hen_tra'] ."</p>";
                    $body_tmp.="<p>Tên file mượn: </p>" ;
                    foreach($filemuon as $item){
                        $body_tmp .='<p>'.$item['file_name'].'</p>';
                    }
                }else{
                    $body_tmp.="<p>Lý do bổ xung: </p>" . $don_info['lydo_muon'];
                }
            }else{
                if(strlen($body_tmp) > 0){
                    $body_tmp.="<p>Lý do trả ： $ghichu</p>" ;
                }
                $body_tmp.="<p>Tên file đính kèm ： </p>" ;
                $del_flg=0;
                if($status==DON_STATUS_TRA_KT || $status==DON_STATUS_TRA_DUYET || $status==DON_STATUS_TRA_TTQL ||$status==DON_STATUS_TRA_CN)
                {
                    $del_flg =1;
                }
                $filelist =$file->get_file_don($don_id,'notdwg',$status,'1,2',$del_flg); // chi lay nhung file moi
                foreach($filelist as $item){
                    $body_tmp .='<p>'.$item['file_name'].'</p>';
                }
            }
            
            			
			$replacements['BODY'] = $body_tmp;
			$dont_send = 0;
			$mail_subject = '';
            $list_mail = $this->get_email_list($don_id);
            $mail_to = array();            
            $mail_cc = array();
            //ACWLog::debug_var('---don----','status: '.$status);
			switch ($status) {
			    case DON_STATUS_NEW:
			        $mail_subject = '[Duyệt] Yêu cầu kiểm tra';
			        $replacements['HEADER'] = '<h3 style="font-weight: bold;">Yêu cầu kiểm tra</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
                    $mail_cc[] =$list_mail['mail_add'];
			        break;
			    case DON_STATUS_KT:
			        $mail_subject = '[Duyệt] Yêu càu duyệt';
			        $replacements['HEADER'] = '<h3 style="font-weight: bold;">Yêu càu duyệt</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_ttql'];
                    $mail_cc[] =$list_mail['mail_add'];
			        break;
			    case DON_STATUS_DUYET:
			        $mail_subject = '[Duyệt] Yêu cầu trung tâm quản lý duyệt';
			        $replacements['HEADER'] = '<h3 style="font-weight: bold;">Yêu cầu trung tâm quản lý duyệt</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_ttql'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_add'];
			        break;
			    case DON_STATUS_TTQL:
			        $mail_subject = '[Duyệt] Trung tâm quản lý đã duyệt';
			        $replacements['HEADER'] = '<h3 style="color: green;font-weight: bold;">Trung tâm quản lý đã duyệt</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;
                case DON_STATUS_TRA_KT:
			        $mail_subject = '[Trả yêu cầu] Đã trả yêu cầu kiểm tra';
			        $replacements['HEADER'] = '<h3 style="color: red;font-weight: bold;">Đã trả yêu cầu kiểm tra</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;
                case DON_STATUS_TRA_DUYET:
			        $mail_subject = '[Trả yêu cầu] Đã trả yêu cầu duyệt';
			        $replacements['HEADER'] = '<h3 style="color: red;font-weight: bold;">Đã trả yêu cầu duyệt</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;
                case DON_STATUS_TRA_TTQL:
			        $mail_subject = '[Trả yêu cầu] Trung tâm quản lý đã trả duyệt';
			        $replacements['HEADER'] = '<h3 style="color: red;font-weight: bold;">Trung tâm quản lý đã trả duyệt</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;
                case DON_STATUS_XIN_CN:
                    if($flg_muon){
                        $mail_subject = 'Xin mượn bản vẽ';
			            $replacements['HEADER'] = '<h3 style="font-weight: bold;">Xin mượn bản vẽ</h3>';
                    }else{
                        $mail_subject = 'Xin bổ xung file';
			            $replacements['HEADER'] = '<h3 style="font-weight: bold;">Xin bổ xung file</h3>';
                    }
                    $mail_to[]['mail_address']= $list_mail['mail_ttql'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] = $list_mail['mail_add'];
			        break;
                case DON_STATUS_TRA_CN:
			        $mail_subject = '[Trả yêu cầu] Trả yêu cầu xin mượn bản vẽ';
			        $replacements['HEADER'] = '<h3 style="color: red;font-weight: bold;">Trả yêu cầu xin mượn bản vẽ</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;
                case DON_STATUS_DUYET_CN:
			        $mail_subject = '[Duyệt] Duyệt yêu cầu xin mượn bản vẽ';
			        $replacements['HEADER'] = '<h3 style="color: red;font-weight: bold;">[Duyệt]Duyệt yêu cầu xin mượn bản vẽ</h3>';
                    $mail_to[]['mail_address']= $list_mail['mail_add'];
                    $mail_cc[] =$list_mail['mail_kt'];
                    $mail_cc[] =$list_mail['mail_duyet'];
                    $mail_cc[] =$list_mail['mail_ttql'];
			        break;    
			    default:
					$dont_send = 1;
			        $mail_subject = 'default';
			        $replacements['HEADER'] = 'default';
			        break;
			}
			$email = new Mail_lib();
			//get_mail_user_permission
			
            
            /*$mail_cc[] =$list_to['mail_duyet'];
            $mail_cc[] =$list_to['mail_ttql'];*/
           // ACWLog::debug_var('---don----','$mail_to: ');
           // ACWLog::debug_var('---don----',$mail_to);
            //ACWLog::debug_var('---don----','dont_send: '.$dont_send);
			if(count($mail_to) > 0 && $dont_send == 0) {
			    $email->AddListAddress($mail_to);
                foreach($mail_cc as $cc){
                    $email->addCC($cc);
                }
			    $email->Subject = $mail_subject;                
			    $email->loadbody('template_mail.html');
			    $email->replaceBody($replacements);
			    $result = $email->send();
               // ACWLog::debug_var('---don----','result: '.$result);
			}
    }
    public function get_email_list($don_id)
    {
        $sql="select t.user_duyet,t.user_kt,t.user_ttql ,
            u1.email mail_add,
            u2.email mail_kt,
            u3.email mail_duyet,
            u4.email mail_ttql
            from don t
            INNER JOIN m_user u1 on u1.user_id= t.add_user_id
            LEFT JOIN m_user u2 on u2.user_id= t.user_kt
            LEFT JOIN m_user u3 on u3.user_id= t.user_duyet
            LEFT JOIN m_user u4 on u4.user_id= t.user_ttql
            where t.don_id= :don_id ";
        $res= $this->query($sql,array('don_id'=>$don_id));
        return $res[0];
    }
    public function get_user_don($don_id,$user_id){
        $sql="select t.don_id,
              t.trangthai,
              COALESCE(u1.user_id,0) upload,
              COALESCE(u2.user_id,0) kiemtra,
              COALESCE(u3.user_id,0) duyet,
              COALESCE(u4.user_id,0) ttql
                from don t	
                		left join m_user u1 on u1.user_id = t.add_user_id and u1.user_id = :user_id
                		left join m_user u2 on u2.user_id = t.user_kt and u2.user_id = :user_id
                		left join m_user u3 on u3.user_id = t.user_duyet and u3.user_id = :user_id
                		left join m_user u4 on u4.user_id = t.user_ttql and u4.user_id = :user_id
                  where don_id= :don_id";
        $res = $this->query($sql,array('user_id'=>$user_id
                                ,'don_id'=>$don_id));
        return $res[0];
    }
    public function check_don_no_exist($don_no){
        $sql="select count(*) as cnt
                from don t	
                  where don_no= :don_no
                  and del_flg=0";
        $res = $this->query($sql,array('don_no'=>$don_no));
        if($res[0]['cnt'] >0){
            return FALSE;  
        }
        return TRUE;
    }
    public function insert_file_pdf($folder_name,$don_id,$listfile){
        $f = new File_lib();
        //$listfile = $f->FileList($folder_name);
        $sql="INSERT into file(file_name,don_id,file_type,status,
                    add_user_id,add_datetime,upd_user_id,upd_datetime,convert_flg)
            select :file_pdf,don_id,'pdf',status,
                    add_user_id,add_datetime,upd_user_id,upd_datetime ,1
        	from file
            where new_flg = 1
            and file_name = :file_name
            and don_id =:don_id 
          ";
        $pa_sql['don_id'] = $don_id;
       // $pa_sql['ver_id'] = $ver_id;
        $this->begin_transaction();
        foreach($listfile as $key=>$item){
            $pa_sql['file_name'] = $item['file_name'] ;
            $pa_sql['file_pdf'] =$f->GetBaseName($item['file_name']).'.pdf' ;
            $this->execute($sql,$pa_sql);
        }
        $this->commit();
    }
    public static function action_test(){
       
        //echo str_replace('/',"\\",ACW_TMP_DIR);
        $folder ="D:/xampp/htdocs/qlfile/data/main/D0000040";
        $model = new Don_model();
        $model->convert_dwg_to_pdf($folder);
    }
    public function convert_dwg_to_pdf($folder_name,$listfile){
        $f = new File_lib();
       // $listfile = $f->FileList($folder_name);
        $folder_main = str_replace('/',"\\",$folder_name);
        $cmd="";
        $file_cmd = str_replace('/',"\\",ACW_TMP_DIR_BAT)."\\".uniqid("",TRUE).".bat" ;
        $php_exe = str_replace('htdocs/qlfile','php\php.exe', ACW_ROOT_DIR);
        $php_exe = '"'.str_replace('/',"\\",$php_exe) .'"';
        $batch_img = ACW_ROOT_DIR.'/batch/php/BatchAddImg.php';
        $batch_img = '"'.str_replace('/',"\\",$batch_img) .'"';
        foreach($listfile as $key=>$item){
            if($key >0){
                $cmd .=" \n";
            }
            $file_src = '"'.$folder_main."\\".$item['file_name'] .'"';
            $file_desti = '"'.$folder_main."\\".$f->GetBaseName($item['file_name']).'.pdf"';
            $cmd .= '"'.CONVERT_PDF_TOOL_PATH .'" /InFile '.$file_src." /OutFile ".$file_desti;
            
            $cmd .=" \n";
            $cmd .="timeout /T 1 ";  
            $cmd .=" \n";         
            $cmd .=$php_exe.' '.$batch_img.' '.$file_desti;
        }
        file_put_contents($file_cmd,$cmd);
        pclose(popen("start /B ". $file_cmd, "r"));
    }
}
/* ファイルの終わり */