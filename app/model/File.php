<?php
/**
 * ユーザ管理
 *
*/
class File_model extends ACWModel
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
                    if($param['search_file_name'] != ''){
                        $s_file_name = $param['search_file_name'];
                        $param['s_file_name'] = strtolower($s_file_name);
                    }
                break;  
        }
		return true;
	}

	/**
	* 初期表示
	*/
    public static function action_blank()
	{	 
		return ACWView::template('file.html',array(
                                    'data_rows' =>array()
                                    ,'search_file_name'=>''
                                    ,'flg_old'=>'0'
                                    )
                                    );
	}
    public static function action_cublank()
	{	 
		return ACWView::template('oldfile.html',array(
                                    'data_rows' =>array()
                                    ,'search_file_name'=>''
                                    ,'flg_old'=>'1'
                                    ));
	}
	public static function action_index()
	{	
        $param = self::get_param(array(
			'search_file_name',
            'flg_old'
		));
        $db= new File_model();
        $data = $db->get_file_rows($param);
		return ACWView::template('file.html',array(
                                    'data_rows' =>$data
                                    ,'search_file_name'=>$param['search_file_name']
                                    ,'flg_old'=>$param['flg_old']
                                    ));
	}
    public static function action_oldfile()
	{	
        $param = self::get_param(array(
			'search_file_name',
            'flg_old'
		));
        $db= new File_model();
        $data = $db->get_oldfile_rows($param);
		return ACWView::template('oldfile.html',array(
                                    'data_rows' =>$data
                                    ,'search_file_name'=>$param['search_file_name']
                                    ,'flg_old'=>$param['flg_old']
                                    ));
	}
    public static function action_existfile()
	{	
        $param = self::get_param(array(
			'search_file_name',
            'flg_old'
		));
        $db= new File_model();
        $data = $db->get_existfile_rows($param);
		return ACWView::template('existfile.html',array(
                                    'data_rows' =>$data
                                    ,'search_file_name'=>$param['search_file_name']
                                    ));
	}
    public static function action_mtblank()
	{	
        $param = self::get_param(array(
			'search_file_name',
            's_trangthai_tra'
		));
        $db= new File_model();
        $data = array();//$db->get_file_rows($param);
		return ACWView::template('muontra.html',array(
                                    'data_rows' =>$data
                                    ,'s_trangthai_tra'=>$param['s_trangthai_tra']
                                    ,'search_file_name'=>$param['search_file_name']));
	}
    public static function action_muontra()
	{	
        $param = self::get_param(array(
			'search_file_name',
            's_trangthai_tra'
		));
        $db= new File_model();
        $data = $db->get_muon_tra($param);
		return ACWView::template('muontra.html',array(
                                    'data_rows' =>$data
                                    ,'s_trangthai_tra'=>$param['s_trangthai_tra']
                                    ,'search_file_name'=>$param['search_file_name']));
	}
    public static function action_capphat()
	{	
        $param = self::get_param(array(
			'search_file_name'
            ,'search_tieude'
		));
        $db= new File_model();
        $data = $db->get_file_rows($param);
		return ACWView::template('capphat.html',array(
                                    'data_rows' =>$data
                                    ,'search'=>$param));
	}
    public static function action_capphatfile()
	{	
        $param = self::get_param(array(
			'file_id'
		));
        $usr = new User_model();
        $db = new File_model();
        $data = $db->get_capphat_rows($param['file_id']);
        $phongban = addslashes(json_encode($usr->get_phongban()));
        $tonhom = addslashes(json_encode($usr->get_tonhom()));
        $donvi = $usr->get_donvi();
		return ACWView::template('capphat/edit.html',array(
                                    'data_rows' =>$data,
                                    'file_id' =>$param['file_id'],
                                    'donvilist' =>$donvi,
                                    'phongbanlist' =>$phongban,
                                    'tonhomlist'=>$tonhom,
                                    'donvi' =>addslashes(json_encode($donvi)),
                                    'count'=>count($data)
                                    ));
	}
    public static function action_capphatupdate()
	{
        $param = self::get_param(array(
			'file_id',
            'capphat_id',
            'donvi',
            'phongban',
            'tonhom',
            'soluong',
            'add_donvi',
            'add_phongban',
            'add_tonhom',
            'add_soluong'
		));
        $result = array('status' => 'OK');
		//$result['param'] = $param;
				
		$db = new File_model();
		$db->capphat_update($param);
		
        $result['status'] ="OK";
		if (ACWError::count() > 0) {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
    }
    public function get_existfile_rows($param)
	{
		$sql = "select t.file_id ,
            	t.file_name,
            	t.file_type,
                t.don_id,
                t.`status`,
            --    u.user_name,
							d.tieude,d.don_no,
							(case 
				  when d.loaidon = 0 then 'Tạo mới'
				  when d.loaidon = 1 then 'Cập nhật'
				  end)  loaidon,
                  DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime  
                from file t
                inner join don d on d.don_id = t.don_id and d.del_flg=0
                where t.del_flg = 0
                and t.file_exist = 0
                and t.file_type='dwg' ";
        if (isset($param['search_file_name']) && !empty($param['search_file_name'])) {
			$sql_param = array(
					'file_name' =>  '%' . SQL_lib::escape_like($param['search_file_name']) . '%'
				);
			$sql .= " and lower(file_name) like lower(:file_name) ";
        }else{
            $sql_param = array();
        }
        $sql .= "
            ORDER BY t.file_id
		";
		return $this->query($sql, $sql_param);
	}
    public function get_capphat_rows($file_id)
	{
        $user = ACWSession::get('user_info');
		$sql = "
			SELECT	t.capphat_id, t.soluong,
            d.donvi_name,p.phongban_name,n.tonhom_name
            FROM	capphat   t
            INNER JOIN don_vi d on d.donvi_id = t.donvi_id
            INNER JOIN phong_ban p on p.phongban_id= t.phongban_id
            LEFT JOIN to_nhom n on n.tonhom_id = t.tonhom_id
            where file_id= :file_id
            and t.add_user_id =:user_id
            ORDER BY capphat_id
		";
		
		return $this->query($sql, array('file_id'=>$file_id,'user_id'=>$user['user_id']));
	}
	/**
	 * 
	 * @return string
	 */
    public static function action_upload()
    {
        $param = self::get_param(array(
			'folder_tmp'
		));
        $post = isset($_FILES['file_upload'])?$_FILES['file_upload']:array();
        //$template_file = $params['file_upload'];
        $flg_suc="0";
        $file = new File_lib();
        $db= new File_model();
        $flg_copy = TRUE;        
        $success_msg="";
        $error_message="";
        $data=array();
        $folder_tmp = ACW_TMP_DIR_IMG.'/'.$param['folder_tmp'];
        if(!$file->FolderExists($folder_tmp)){
			$file->CreateFolder($folder_tmp);
		}
        if(isset($post['name'])){
            $file_list = $post['name'];            
            for($i=0 ;$i< count($file_list);$i++)
            {
                $file_name = $folder_tmp.'/'.$file_list[$i];
                if($file->GetExtensionName($file_name)=='pdf'){
                    ACWError::add('file_name', 'File: "'.$file_list[$i].'" là file pdf, không thể upload !'); 
                	$flg_copy = FALSE;
                }
                if($file->FileExists($file_name)){
					ACWError::add('file_name', 'File: "'.$file_list[$i].'" bị trùng, không thể upload !');                                    
                	$flg_copy = FALSE;	
				}
            }
            $result =array();
            $arr_cop = array();
            if($flg_copy){
	            for($i=0 ;$i< count($file_list);$i++)
	            {
	                $file_name = $folder_tmp.'/'.$file_list[$i];
	                $file_tmp =$post['tmp_name'][$i];
                    //ACWLog::debug_var('----upload-----','$file_tmp: '.$file_tmp);
                    //ACWLog::debug_var('----upload-----','$file_name: '.$file_name);
	                if($file->MoveFile($file_tmp,$file_name)==FALSE){
	                	ACWError::add('file_name', 'File: "'.$file_list[$i].'" bị trùng, không thể upload !');                                    
	                    $flg_copy = FALSE;
	                }else{
						$arr_cop[] = $file_list[$i];
					}
	            } 
	            $result['data_row']=  $arr_cop;     
            }
            //$par_ins = array();
            /*if($flg_copy){
                $flg_suc ="1";            
                for($i=0 ;$i< count($file_list);$i++)
                {
                    $par_ins['file_name'] =$file_list[$i];
                    $par_ins['file_type'] =$file->GetExtensionName($file_list[$i]);
                    $par_ins['file_id'] = null;
                    $db->update($par_ins);                
                }            
            }*/
            $result['status']="OK";
            if (ACWError::count() <= 0) {
                if(count($file_list) >0)   {                	
                    $result['msg'] ="Upload thành công !";
                }             
    		} else {			
    			$err = ACWError::get_list();
    			$result['status']="LOI";
                $result['error']=$err[0]['info'];
    		}
        }
        return ACWView::json($result);
        //$data= $db->get_file_rows($param);
        //ACWLog::debug_var('----file----',$error_message);
        /*return ACWView::template('upload.html',array('success_msg'=>$success_msg,
                                 'error_message'=>$error_message,
                                 'search_file_name'=>$param['search_file_name'],
                                 'data_row'=>$data));*/
    } 
    public static function action_changefile()
    {
        $param = self::get_param(array(
			'folder_tmp',
            'change_file_id'
		));
        $post = isset($_FILES['change_file'])?$_FILES['change_file']:array();
        //$template_file = $params['file_upload'];
        $flg_suc="0";
        $file = new File_lib();
        $db= new File_model();
        $flg_copy = TRUE;        
        $success_msg="";
        $error_message="";
        $data=array();
        $folder_tmp = ACW_TMP_DIR_IMG.'/'.$param['folder_tmp'];
        if(!$file->FolderExists($folder_tmp)){
			$file->CreateFolder($folder_tmp);
		}
        if(isset($post['name'])){
            
            $file_name = $folder_tmp.'/'.$post['name'];
            if($file->FileExists($file_name)){
			    ACWError::add('file_name', 'File: "'.$post['name'].'" bị trùng, không thể upload1 !');  
                $flg_copy = FALSE;	
			}
            if(!$db->check_file_name_update($param['change_file_id'],$post['name'])){
                ACWError::add('file_name', 'File: "'.$post['name'].'" không dúng tên file cập nhật !');  
                $flg_copy = FALSE;	
            }
            $result =array();
            $arr_cop = array();
            if($flg_copy){
	                $file_name = $folder_tmp.'/'.$post['name'];
	                $file_tmp =$post['tmp_name'];
                   // ACWLog::debug_var('----upload-----','$file_tmp: '.$file_tmp);
                    //ACWLog::debug_var('----upload-----','$file_name: '.$file_name);
	                if($file->MoveFile($file_tmp,$file_name)==FALSE){
	                	ACWError::add('file_name', 'File: "'.$post['name'].'" bị trùng, không thể upload2 !');                                    
	                    $flg_copy = FALSE;
	                }else{
						$arr_cop[] = $post['name'];
					}	           
	            $result['data_row']=  $arr_cop;     
            }
            
            $result['status']="OK";
            $result['file_name'] = $post['name'];
            $result['file_id'] = $param['change_file_id'];
            if (ACWError::count() <= 0) {
                $result['msg'] ="Upload thành công !";
    		} else {			
    			$err = ACWError::get_list();
    			$result['status']="LOI";
                $result['error']=$err[0]['info'];
    		}
        }
        return ACWView::json($result);        
    }   
    public static function action_remove()
    {
		$param = self::get_param(array(
			'folder_tmp',
			'file_name'
		));
		$file = new File_lib();
		$file_path= ACW_TMP_DIR_IMG.'/'.$param['folder_tmp'].'/'.$param['file_name'];
		
		$result['error']="";
		if($file->DeleteFile($file_path)==FALSE){			
			$result['error']="Lỗi, không xóa được file";
		}
		return ACWView::json($result);
	}
	public static function action_edit()
	{
		$param = self::get_param(array(
			'search_file_name'
		));
		
		/*$donvi_id = null;
		if (isset($params['donvi_id'])) {
			$donvi_id = $params['donvi_id'];
		}*/
        $success_msg="";
        $error_message="";
		$model = new File_model();
		$data_row = array();
		/*if ($donvi_id == null) {			
			$data_row['donvi_id'] = null;
			$data_row['donvi_name'] = null;
			$data_row['del_flg'] = null;
		} else {
			$data_row = $model->get_user_row($donvi_id);			
		}*/
        $data= $model->get_file_rows($param);
		return ACWView::template('file/edit.html',array('success_msg'=>$success_msg,
                                 'error_message'=>$error_message,
                                 'search_file_name'=>$param['search_file_name'],
                                 'data_row'=>$data));
	}
	
	private static function _validate_update(&$param)
	{	        
		$validate = new Validate_lib();
		
		$param['donvi_name'] = $validate->trim_ext($param['donvi_name']);
		if ($validate->type_str('donvi_name', 'Tên đơn vị', $param['donvi_name'], true) == false) {
			return false;
		}        
        if($param['donvi_name'] != ''){
            if(strlen($param['donvi_name']) > 100){
                ACWError::add('lelng', 'Tên đơn vị không được quá 50 ký tự');
                return false;
            }
        }
		return true;
	}
    public static function action_checkmaxlenght() {
        
        $result['error'] = array();        
        return ACWView::json($result);    
    }
   
	
	/**
	* 更新
	*/
	public static function action_update()
	{
		$params = self::get_param(array(			
			'donvi_id',
			'donvi_name',
                'del_flg'
		));
		
		if (self::get_validate_result() === true) {
			$model = new File_model();
			$obj = $model->update($params);
		}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	
	
	public function get_file_rows($param)
	{
        $login_info = ACWSession::get('user_info');
        //lay tat ca folder ma user co quyen,co quyen o folder cha thi co quyen o tat ca folder con
        $this->query("select f_get_allfolder_child(:user_id) ",array('user_id'=>$login_info['user_id']));
		$sql = "SELECT	DISTINCT			 
				t.file_id ,
            	t.file_name,
            	t.file_type,
                t.don_id,
                t.`status` trangthai,
                u.user_name_disp user_name,
								utt.user_name_disp ttql,
							d.tieude,d.don_no,
							(case 
				  when d.loaidon = 0 then 'Tạo mới'
				  when d.loaidon = 1 then 'Cập nhật'
				  end)  loaidon,
          DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime,     
          DATE_FORMAT(d.ngay_ttql,'%d/%m/%Y %H:%i:%s') ngay_ttql
			FROM	file t
				 inner join (select a.file_name,max(a.file_id) file_id from file a where del_flg=0 group by a.file_name) mx
									on mx.file_id = t.file_id 
				inner join don d on d.don_id = t.don_id and d.del_flg = 0
				inner join don_folder df on df.don_id = t.don_id
				inner JOIN (SELECT DISTINCT * from temp_folder) tmp on tmp.folder_id = df.folder_id
        left join m_user u on t.add_user_id = u.user_id
				left join m_user utt on d.user_ttql = utt.user_id
            where /*t.`status`=3
                and*/ t.file_type ='pdf'
                and t.del_flg = 0
		";
		
		if (isset($param['search_file_name']) && !empty($param['search_file_name'])) {
			$sql_param = array(
					'file_name' =>  '%' . SQL_lib::escape_like($param['search_file_name']) . '%'
				);
			$sql .= " and lower(t.file_name) like lower(:file_name) ";
        }else if (isset($param['search_tieude']) && !empty($param['search_tieude'])) {
			$sql_param = array(
					':tieude' =>  '%' . SQL_lib::escape_like($param['search_file_name']) . '%'
				);
			$sql .= " and lower(d.tieude) like lower(:tieude) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.file_id
		";		
        //var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
    public function get_oldfile_rows($param)
	{
        $login_info = ACWSession::get('user_info');
        //lay tat ca folder ma user co quyen,co quyen o folder cha thi co quyen o tat ca folder con
        //$this->query("select f_get_allfolder_child(:user_id) ",array('user_id'=>$login_info['user_id']));
		$sql = "SELECT	DISTINCT			 
				t.file_id ,
            	t.file_name,
            	t.file_type,
                t.don_id,
                t.`status` trangthai,
                u.user_name,
							d.tieude,d.don_no,
							(case 
				  when d.loaidon = 0 then 'Tạo mới'
				  when d.loaidon = 1 then 'Cập nhật'
				  end)  loaidon,
          DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime     
               
			FROM	file t				 
				inner join don d on d.don_id = t.don_id and d.del_flg = 0
        left join m_user u on t.add_user_id = u.user_id
            where  t.file_type ='pdf'
                and t.del_flg = 0
		";
		
		if (isset($param['search_file_name']) && !empty($param['search_file_name'])) {
			$sql_param = array(
					'file_name' =>  '%' . SQL_lib::escape_like($param['search_file_name']) . '%'
				);
			$sql .= " and lower(t.file_name) like lower(:file_name) ";
        }else if (isset($param['search_tieude']) && !empty($param['search_tieude'])) {
			$sql_param = array(
					':tieude' =>  '%' . SQL_lib::escape_like($param['search_file_name']) . '%'
				);
			$sql .= " and lower(d.tieude) like lower(:tieude) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.file_id
		";		
        //var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
    public function get_muon_tra($param)
	{
        $login_info = ACWSession::get('user_info');
        //lay tat ca folder ma user co quyen,co quyen o folder cha thi co quyen o tat ca folder con
        //$this->query("select f_get_allfolder_child(:user_id) ",array('user_id'=>$login_info['user_id']));
		$sql = "SELECT	DISTINCT			 
				t.file_id ,
            	t.file_name,
            d.tieude,	
						d.don_no,
						DATE_FORMAT(d.ngay_hen_tra,'%d/%m/%Y %H:%i:%s') ngay_hen_tra  ,
						d.lydo_muon,
                t.don_id,
                t.`status` trangthai,
                u.user_name,						
							(case 
				  when t.`status` = 3 then 'Đã trả file mượn'
				  else  'Chưa trả file mượn'
				  end)  trangthai_tra,
          DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime     
               
			FROM	file t
				inner join (select a.file_name,max(a.file_id) file_id from file a where del_flg=0 group by a.file_name) mx
									on mx.file_id = t.file_id
				inner join don d on d.don_id = t.don_id and d.del_flg = 0
				inner join don_folder df on df.don_id = t.don_id
        left join m_user u on t.add_user_id = u.user_id
            where t.file_type ='dwg'
                and t.del_flg = 0
		";
		$sql_param = array();
		if (isset($param['search_file_name']) && !empty($param['search_file_name'])) {
			$sql_param['file_name'] = '%' . SQL_lib::escape_like($param['search_file_name']) . '%';
            $sql .= " and lower(t.file_name) like lower(:file_name) ";
        }
        if (isset($param['s_trangthai_tra']) && strlen($param['s_trangthai_tra'])>0) {
            if($param['s_trangthai_tra']=='0'){
                $sql .= " and t.`status` <> 3";
            }else{
                $sql .= " and t.`status` = 3";
            }
		}
		
		$sql .= "
			ORDER BY
				t.file_id
		";		
        //var_dump($sql);die;
		return $this->query($sql, $sql_param);
	}
	public function get_file_don($don_id,$type_file='',$status='',$new_flg = '',$del_flg = 0)
	{
		$sql = "
			SELECT	t.*,(@rownum := @rownum + 1) AS stt,'' dl_flg
			FROM	file t     ,(SELECT @rownum := 0) r        
            where del_flg = :del_flg
            and don_id=:don_id 
		";
        $param_sql['don_id']= $don_id;
        $param_sql['del_flg']= $del_flg;
		if(strlen($type_file)>0){
            if($type_file =='pdf' || $type_file =='dwg'){
                $sql .=" and file_type = :file_type";
                $param_sql['file_type']= $type_file;
            }else{
                $sql .=" and file_type <> 'pdf'";
            }
        }
        if(strlen($status)>0){
            $sql .=" and status= :status";
            $param_sql['status']= $status;
        }
        if(strlen($new_flg)>0){
            $sql .=" and new_flg in( $new_flg )";
           // $param_sql['new_flg']= $new_flg;
        }
        $sql .=" ORDER BY t.file_id";
        //ACWLog::debug_var('---sql----',$sql);
        //ACWLog::debug_var('---sql----',$param_sql);
		return $this->query($sql, $param_sql);
	}	
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		
		$pass_md5 = null;		
		$sql_params = array(			
			'file_name' => $params['file_name'], 
            'file_type' => $params['file_type'], 
            'don_id' => $params['don_id'], 
           // 'ver_id' => $params['ver_id'],
            'upd_user_id'=>$login_info['user_id']            
		);		
		if ($params['file_id'] == null && $params['flg_capnhat'] =='0') {		
			if(!isset($params['flg_muon']) || $params['flg_muon']=='0'){
    			$res = $this->get_file_name_count($params);
    			if ($res['cnt'] > 0) {
    				ACWError::add('file_name', 'File: "'.$params['file_name'].'" bị trùng, không thể upload !');
                    $this->rollback();
    				return;
    			}
                $sql_params['new_flg'] = 1; // file moi;
            }else{
                $sql_params['new_flg'] = 2;  // file muon
            }
		
			$sql = "
				INSERT INTO
				    file
				(
					
					 file_name	
                    , file_type
                    ,don_id
                		
                    , status
                    ,new_flg
					, add_user_id
					, add_datetime
					, upd_user_id
					, upd_datetime
				) VALUES (
					
					 :file_name 					
                    , :file_type
                    ,:don_id
                 
                    , -1
                    , :new_flg
					, :upd_user_id 
					, NOW() 
					, :upd_user_id 
					, NOW() 
				);
			";
			//$sql_params['add_user_id'] = $login_info['user_id'];
            //ACWLog::debug_var('---ins---',$sql);
            //ACWLog::debug_var('---ins---',$sql_params);
            $this->execute($sql, $sql_params);	
		} else {							
			if($params['flg_capnhat'] =='1'){
                $param_up['status'] = -1;
            }
			$sql = "
				UPDATE	file
				SET					
					 status = :status
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
				WHERE don_id=:don_id
				--and ver_id = :ver_id
                and file_name = :file_name
			";      
			//$param_up['ver_id'] = $params['ver_id'];
            $param_up['don_id'] = $params['don_id'];
            $param_up['file_name'] = $params['file_name'];
			//$param_up['status'] = $params['status'];
            $param_up['upd_user_id'] = $login_info['user_id'];
            ACWLog::debug_var('---sql---',$param_up);
            $this->execute($sql, $param_up);	
		}			
		
		$this->commit();
		
		return true;
	}
    public function update_file($params){
        $login_info = ACWSession::get('user_info');
        $sql = "update file t ,
                (select * from file 
                				WHERE	file_id = :file_id
                		) f
                set t.status = :status
                , t.upd_user_id = :upd_user_id
                , t.upd_datetime = NOW()
                where t.don_id = f.don_id
                and t.file_name like REPLACE(f.file_name,'.dwg','%')
                and t.del_flg = 0
			";      
	    $param_up['file_id'] = $params['file_id'];
		$param_up['status'] = $params['status'];
        $param_up['upd_user_id'] = $login_info['user_id'];
        $this->execute($sql, $param_up);        
    }
    public function update_file_don($params){
        $login_info = ACWSession::get('user_info');
        $sql = "update file t 
                set t.status = :status
                , t.upd_user_id = :upd_user_id
                , t.upd_datetime = NOW()
                where t.don_id = :don_id
                and t.del_flg = 0
			";      
	    $param_up['don_id'] = $params['don_id'];
//        $param_up['ver_id'] = $params['ver_id'];
		$param_up['status'] = $params['status'];
        $param_up['upd_user_id'] = $login_info['user_id'];
        if(isset($params['status_old']) && strlen($params['status_old']) > 0){
            $param_up['status_old'] = $params['status_old'];
            $sql .=" and t.status = :status_old";
        }
        /*ACWLog::debug_var('-------sql-----',$params);
        ACWLog::debug_var('-------sql-----',$sql);
        ACWLog::debug_var('-------sql-----',$param_up);*/
        $this->execute($sql, $param_up);
    }
    public function delete_file_don($don_id){
        $login_info = ACWSession::get('user_info');
        $sql = "update file t 
                set t.del_flg = 1
                , t.upd_user_id = :upd_user_id
                , t.upd_datetime = NOW()
                where t.don_id = :don_id
			";
        $param_up['don_id'] = $don_id;
        $param_up['upd_user_id'] = $login_info['user_id'];
        $this->execute($sql, $param_up);
        return TRUE;
    }
    public function delete_file($file_id,$status=''){
        $login_info = ACWSession::get('user_info');
        $sql = "update file t 
                set t.del_flg = 1
                , t.upd_user_id = :upd_user_id
                , t.upd_datetime = NOW()
			";
        if(strlen($status)>0){
            $sql .= ",t.status= :status";
            $param_up['status'] = $status;
        }
        $sql .= "
                where t.file_id = :file_id
			";
        $param_up['file_id'] = $file_id;
        $param_up['upd_user_id'] = $login_info['user_id'];
        $this->execute($sql, $param_up);
        return TRUE;
    }
    public function check_don_no($doncn_no)
    {
        $sql ="";
    }
	public function check_filename($list,$doncn_id = 0)
    {
        foreach($list as $item){
            $params['file_name']= $item;
            
            if( $doncn_id >0 ){
                $params['doncn_id']= $doncn_id;  // don cập nhật id, đơn cũ
                $res = $this->get_file_name_count($params);
    			if ($res['cnt'] == 0) {
    				ACWError::add('file_name', 'File: "'.$params['file_name'].'" Không có trong đơn cũ, không thể cập nhật !');
                    $this->rollback();
    				return FALSE;
    			}
            }else{
                $res = $this->get_file_name_count($params);
    			if ($res['cnt'] > 0) {
    				ACWError::add('file_name', 'File: "'.$params['file_name'].'" bị trùng, không thể thêm mới, vui lòng chọn file khác hoặc Xin mượn bản vẽ !');
                    $this->rollback();
    				return FALSE;
    			}
            }
        }
        return TRUE;
    }   
    public static function action_download()
    {
        //return ACWView::json('OK');
        $param  = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
        if(count($url_param)==3){
            $don = new Don_model();            
            $fmodel = new File_model();
            $res = $fmodel->get_file_row($url_param[2]);            
            //$ver_id = $res['ver_id'];
            $folder_name = $don->get_folder_data_name($url_param[0]);
            if($url_param[1]==DON_STATUS_TTQL || $url_param[1]==DON_STATUS_XIN_CN || $url_param[1]==DON_STATUS_XIN_CN){
                $folder_name=$don->get_folder_data_name($url_param[0],FALSE);
            }
            $full_path = $folder_name.'/'.$res['file_name'];
            //add section
            $list_dl = ACWSession::get('file_download');
            if(count($list_dl) > 0){
               if(in_array($url_param[2],$list_dl) === FALSE){
                    $list_dl[]= $url_param[2];
               } 
            }else{
                $list_dl[]= $url_param[2];
            }
            
            ACWSession::set('file_download', $list_dl);
            
            return ACWView::download_file($res['file_name'], $full_path);
        }
    }
    public static function action_view()
    {
        $param  = self::get_param(array('acw_url'));
		$url_param = $param['acw_url'];
        $full_path ="";
        if(count($url_param)==3){
            $don = new Don_model();
            $don_id = $url_param[0];
            //$ver_id = $url_param[1];
            $folder_name = "data/tmp/";
            if($url_param[1]==DON_STATUS_TTQL || $url_param[1]==DON_STATUS_XIN_CN || $url_param[1]==DON_STATUS_DUYET_CN){
                $folder_name= "data/main/";
            }
            $folder_name .= 'D'.str_pad($don_id, 10, "0", STR_PAD_LEFT);
            //$folder_name .= '/V'.str_pad($ver_id, 7, "0", STR_PAD_LEFT);
            $fmodel = new File_model();
            $res = $fmodel->get_file_row($url_param[2]);
            $full_path = $folder_name.'/'.$res['file_name'];
            $dl = new Download_model();
            $dl->insert_download($url_param[2]);
        }
        return ACWView::template('view.html', array(
			'full_path' => $full_path
            ,'file_name'=>$res['file_name']
		));
    }
    public static function action_checkview()
    {
        $params = self::get_param(array(			
			'file_id',			
		));
        $res['status']='OK';
        $db = new File_model();
        $result = $db->get_file_row($params['file_id']);
        if($result['status']!=DON_STATUS_TTQL && $result['status']!=DON_STATUS_XIN_CN){
            $res['status']='LOI';
        }
        return ACWView::json($res);
    }
	/**
	 * 
	 * @param integer 
	 * @return array
	 */
	public function get_file_row($file_id)
	{
		$rows = $this->query("
				SELECT
					t.*
				FROM
					file t
				WHERE	t.file_id = :file_id
               -- and del_flg =0 
			", array("file_id" => $file_id)
		);
		
		return $rows[0];
	}
    public function count_file($don_id)
	{
		$rows = $this->query("
				SELECT
					count(*) as cnt
				FROM
					file 
				WHERE	don_id = :don_id
                and del_flg = 0 
			", array("don_id" => $don_id)
		);
		
		return $rows[0]['cnt'];
	}
	
	private function get_file_name_count($param)
	{
		$sql = "
			SELECT	COUNT(*) AS cnt
			FROM	file
			WHERE	file_name = :file_name
            and del_flg = 0
            and status != 9
		";
        $filter = ACWArray::filter($param, array('file_name'));
        if(isset($param['doncn_id']) && !empty($param['doncn_id'])){
            $sql .=" and don_id =:doncn_id";
            $filter['doncn_id']= $param['doncn_id'];
        }		
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
    public function check_file_name_update($file_id,$file_name)
	{
		$sql = "
			SELECT	COUNT(*) AS cnt
			FROM	file
			WHERE	file_name = :file_name
            AND file_id =:file_id
		";       
		$rows = $this->query($sql, array('file_name'=>$file_name,'file_id'=>$file_id));
        if($rows[0]['cnt'] > 0 ){  // update dung ten file
            return TRUE;
        }else{
            return FALSE;
        }
	}
    
	public function capphat_update($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
        
        
		$sql = "INSERT INTO capphat
					(
					donvi_id
					,phongban_id
                    ,tonhom_id
                    ,file_id
                    ,soluong
					,add_user_id
					,add_datetime
                    ,upd_user_id
                    ,upd_datetime
					)
				VALUES
					(
                    :donvi_id					
					,:phongban_id
                    ,:tonhom_id
                    ,:file_id
                    ,:soluong
					,:user_id
					,now()	
                    ,:user_id
					,now()				
					)
				";
        $capphatlist = $param['capphat_id'];
        $olddata = $this->get_capphat_rows($param['file_id']);
        $param_new['user_id'] = $login_info['user_id'];
        $param_new['file_id'] = $param['file_id'];
        if(isset($param['add_tonhom'])){
            $add_soluong = $param['add_soluong'];
            $add_donvi = $param['add_donvi'];
            $add_phongban = $param['add_phongban'];
            $add_tonhom = $param['add_tonhom'];
            foreach($param['add_donvi'] as $key => $val){
                if(strlen($val)>0 && strlen($add_soluong[$key])>0 ){
                    $param_new['donvi_id'] = $val;//$add_donvi[$key];
                    $param_new['phongban_id'] = $add_phongban[$key];
                    $param_new['tonhom_id'] = $add_tonhom[$key];
                    $param_new['soluong'] = $add_soluong[$key];
                    $this->execute($sql,$param_new);
                }
            }
        }
		
       // $addnew = array();
        //$updrow = array();
        $delrow = array();
        /*foreach($capphatlist as $item){
            $flg_exist = TRUE;
            foreach($olddata as $row){
                if($item== $row['capphat_id'] )
                {
                    $flg_exist = FALSE;
                    $updrow[]=$item;  
                }
            }
            if($flg_exist){
                $addnew[] = $item;
            }
        }*/
        foreach($olddata as $row){
            $flg_del = TRUE;
            foreach($capphatlist as $item){
                if($item== $row['capphat_id'] )
                {
                    $flg_del = FALSE;
                    //$updrow[]=$item;
                }
            }
            if($flg_del){
                $delrow[] = $row['capphat_id'];
            }
        }
        $sql_upd = "Update capphat
			    set
					soluong =:soluong,
                    upd_user_id =:user_id,
                    upd_datetime = NOW()
				where capphat_id =:capphat_id
				";
        $param_upd['user_id'] = $login_info['user_id'];
        if(count($capphatlist) > 0){
            $soluong = $param['soluong'];
            foreach($capphatlist as $key=>$val){
                $param_upd['soluong'] = $soluong[$key];
                $param_upd['capphat_id'] = $val;
                $this->execute($sql_upd,$param_upd);
            }
        }
        
		/*$param_new['user_id'] = $login_info['user_id'];
        $param_new['folder_id'] = $param['folder_id'];
        foreach($addnew as $add){
            $addinfo = explode('_',$add);
            $param_new['phongban_id'] = $addinfo[0];
            $param_new['tonhom_id'] = null;
            if(isset($addinfo[1])){
                $param_new['tonhom_id'] =$addinfo[1];
            }
            $this->execute($sql,$param_new);
        }*/
		$sql_del = "delete from capphat
					where capphat_id = :capphat
				";
        //$param_del['folder_id'] =$param['folder_id'];        
        foreach($delrow as $del){
            //$param_del['don_id'] = $del;
            //$delinfo = explode('_',$del);
            //$param_del['phongban_id'] = $delinfo[0];
            //$param_del['tonhom_id'] = 0;
            /*if(isset($delinfo[1])){
                $param_new['tonhom_id'] =$delinfo[1];
            }*/
            $this->execute($sql_del,array('capphat'=>$del));
        }
		$this->commit();        
        return TRUE;
	}
	public static function action_convertpdf(){
        $params = self::get_param(array(			
			'file_id', 
            'don_id',
            'file_name'           
		));
        $don_model = new Don_model();
        $folder_name = $don_model->get_folder_data_name($params['don_id'],FALSE);
        $list_file[] = $params;
        $don_model->convert_dwg_to_pdf($folder_name,$list_file);
        
        $res['status']='OK';
        
        return ACWView::json($res);
    }
}
