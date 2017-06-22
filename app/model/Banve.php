<?php
/**
 * カテゴリ登録
 *
 * TODO:親カテゴリによる排他制御
 *
*/
require_once ACW_VENDOR_DIR . '/phpexcel/PHPExcel.php';

class Banve_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// ログインチェック
	}

	/**
	* カテゴリ登録
	*/
	public static function action_index()
	{
        $param = self::get_param(array('s_banve_no','s_banve_name','bvid'));
		/*$param = array(
			'section_type' =>null//=> json_encode(Section_common_model::get_type())
			,'common_spec' =>null// Section_common_model::get_icon_all()
			);*/


		return ACWView::template('banve.html', $param);
	}
	public static function action_excel()
	{        
        $result = array('status' => 'OK');
        $bv = new Banve_model();
        
        $file_excel = ACW_TMP_DIR.'/'.uniqid(TRUE).'.xlsx';  
        
        $bv->export_exce($file_excel);
		if(file_exists($file_excel)){
			return ACWView::download_file('Danhsach_banve.xlsx', $file_excel,TRUE);            
		}
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('acw_url'));	// 親IDを取る
		if (self::get_validate_result() == false) {
			return ACWView::OK;	// 何も返さない
		}		
		$db = new Banve_model();	
		$param['banve_no'] = "";	
        $param['kho_giay'] = "";
		$param['banve_name'] = "";	
		$param['banve_child'] = NULL;
		$param['parent_level'] = 1;
		$param['dungchung'] = 0;
        $param['user_name'] ='';
        $param['add_datetime'] ='';
        $param['del_flg'] =0;
		$pa_info=$db->get_banve_info($param['parent_id']);
		if(count($pa_info)>0){
			$param['parent_level'] = $pa_info['level'];
		}
        $param['level'] = $param['parent_level'] +1 ;
		return ACWView::template('banve/edit.html', $param);
	}

	/**
	 * HTML取得（編集）
	 */
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false) {
			return ACWView::OK;	
		}
		$db = new Banve_model();
		$result = $db->get_banve_info($param['my_id']);
        $param['level'] = $result['level'];
		$param['banve_no'] = $result['banve_no'];
        $param['kho_giay'] = $result['kho_giay'];
        $param['banve_name'] = $result['banve_name'];
		$param['parent_id'] = $result['parent_id'];		
		$param['banve_child'] = $db->get_child_banve($param['my_id']);
        $param['parent_level'] = 1;
        $param['dungchung'] = $result['dungchung'];
        $param['user_name'] =$result['user_name'];
        $param['add_datetime'] =$result['add_datetime'];
        $param['del_flg'] =$result['del_flg'];
        if(count($result)>0){
			$param['parent_level'] = $result['level']-1;
		}
		return ACWView::template('banve/edit.html', $param);
	}
    public static function action_copy()
	{
		$param = self::get_param(array('banve_id'));
        $db = new Banve_model();
        $res['banve_id'] = $param['banve_id'];
        $info = $db->get_banve_info($param['banve_id']);
        $level = array();
        for($i=1; $i<$info['level'];$i++){
            $level[]=$i ; 
        }
        $ds_level ='0';
        if(count($level)>0){
            $ds_level = implode(',',$level);
        }        
        $res['ds_banve'] = $db->get_all($ds_level);
		return ACWView::template('banve/copy.html', $res);
	}
	public static function action_copyexe()
	{
		$param = self::get_param(array('banve_form','banve_to'));
        $res['error']='';
        $db = new Banve_model();
        $db->copy_exe($param);
        
		return ACWView::json($res);
	}
	public static function action_update()
	{
		// 最初に決まっているもの
		$param = self::get_param(array(
			'banve_name'
			,'kho_giay'
			,'banve_no'
            ,'level'
			,'my_id'
            ,'dungchung'	
			,'parent_id'
			,'add_child'
            ,'add_level'
            ,'add_khogiay'
            ,'add_dungchung'
            ,'add_banve_no'
			));
		
		$result = array('status' => 'OK');
		$result['param'] = $param;
		if (self::get_validate_result() === true) {			
			$db = new Banve_model();
			if ($db->check_banve_id($param) == true) { 
				$res = $db->get_child_banve($param['my_id']);    
				$list_child= array();
				foreach($res as $row){
					$list_child[] = $row['banve_name'];
				}           
				$add_new = array();
                $add_level = array();
                $add_khogiay = array();
                $add_banve_no = array();
                $add_dungchung = array();
				/*if(isset($param['add_child'])){
					$add_child = $param['add_child'];
					foreach($add_child as $key => $item){
						if(!in_array($item,$list_child))	
						{
							$add_new[] = $item ;
                            $add_level[]= $param['add_level'][$key]	;	
                            $add_khogiay[] =$param['add_khogiay'][$key]	;	
                            $add_dungchung[] = $param['add_dungchung'][$key]; 		
                            $add_banve_no[] = $param['add_banve_no'][$key]	; 			
						}
						$list_child[]= $item;
					}	
					
				}
				$param['add_child'] = $add_new;
                $param['add_level'] = $add_level;
                $param['add_khogiay'] = $add_khogiay;
                $param['add_dungchung'] = $add_dungchung;
                $param['add_banve_no'] = $add_banve_no;*/
                
				$db->update_banve($param);
			}
		}
		if (ACWError::count() > 0) {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
	}
    /**
	 * カテゴリ削除
	 */
	public static function action_delete()
	{
		$param = self::get_param(array('acw_url'));	// 自分のIDを取る
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$db = new Banve_model();
		$db->delete_banve($param['my_id']);
		if (ACWError::count() > 0) {
			// エラー
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			// 正常
			$result['status'] = 'OK';
		}
		return ACWView::json($result);
	}
	public static function action_cancel()
	{
		$param = self::get_param(array('acw_url'));	// 自分のIDを取る
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$db = new Banve_model();
		$db->cancel_banve($param['my_id']);
		if (ACWError::count() > 0) {
			// エラー
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			// 正常
			$result['status'] = 'OK';
		}
		return ACWView::json($result);
	}
	public static function action_restore()
	{
		$param = self::get_param(array('acw_url'));	// 自分のIDを取る
		if (self::get_validate_result() == false)  {
			ACWError::add('acw_url', 'Tham số không hợp lệ');
		}
		$db = new Banve_model();
		$db->cancel_banve($param['my_id'],0);
		if (ACWError::count() > 0) {
			// エラー
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		} else {
			// 正常
			$result['status'] = 'OK';
		}
		return ACWView::json($result);
	}
	/**
	* ツリー表示
	*/
	public static function action_tree()
	{
        $param = self::get_param(array(
			's_banve_no'
			,'s_banve_name'			
			));
		$db = new Banve_model();
		$result = $db->get_banve_all($param);
		return ACWView::json($result);
	}

	public static function validate($action, &$param)
	{
		switch ($action) {
		case 'new':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は親ID
			$param = array(
				'parent_id' => $param['acw_url'][0]
				,'my_id' => ''
				);
			break;
		case 'edit':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は自分のID
			$param = array(
				'parent_id' => ''
				,'my_id' => $param['acw_url'][0]
				);
			break;
		case 'delete':
		case 'cancel':
		case 'restore':
			if (count($param['acw_url']) != 1) {
				return false;
			}
			// 一個目は自分のID
			$param = array(
				'my_id' => $param['acw_url'][0]
				);
			break;
		case 'sort':
			if (isset($param['my']) == false || isset($param['parent']) == false) {
				return false;
			}
			break;
		/*case 'update':
			return self::_validate_category($param);*/
		case 'upload':
			return self::_validate_upload($param);  
		}
		return true;
	}
    		
	//kiem tra trung ten trong chung folder cha
	public function check_banve_id(&$param)
	{
		//$this->begin_transaction();
        if(isset($param['dungchung']) && $param['dungchung'] =='1'){
            
            $dc_data = $this->get_banve_byno($param['banve_no']);
            if(isset($dc_data['banve_no']) && strlen($dc_data['banve_no'])>0){
                $param['banve_no'] = $dc_data['banve_no'];
                $param['banve_name'] = $dc_data['banve_name'];
                //$param['kho_giay'] = $dc_data['kho_giay'];
                return TRUE;     
            }else{
            	$msg_err= sprintf(Message_model::get_msg('BVE027'), $param['banve_no']);
            	ACWError::add('banve_no', $msg_err);
                //ACWError::add('banve_no', 'Mã bản vẽ "'.$param['banve_no'].'" chưa có, không thể tạo dùng chung, vui lòng tạo mới !');
                return FALSE;
            }
        }

		$sel_param = ACWArray::filter($param, array('banve_name'));
		$sql = "SELECT COUNT(*) cnt FROM banve WHERE del_flg=0 and dungchung=0
                    and banve_name = :banve_name ";
		if (isset($param['my_id'])) {
			$sel_param['banve_id'] = $param['my_id'];
			$sql .= ' AND banve_id <> :banve_id';	
		}
		$result = $this->query($sql, $sel_param);	
		if ($result[0]['cnt'] > 0) {
			$msg_err= sprintf(Message_model::get_msg('BVE028'), $sel_param['banve_name']);
            ACWError::add('banve_name', $msg_err);
			//ACWError::add('banve_name', 'Tên bản vẽ "'.$sel_param['banve_name'].'" đã có, vui lòng nhập tên khác !');
			return false;
		}
		if(isset($param['banve_no'])&& strlen($param['banve_no'])>0 ){
			if(isset($param['my_id'])===FALSE){
				$sel_param = ACWArray::filter($param, array('banve_no'));
				$sql = "SELECT COUNT(*) cnt FROM banve WHERE del_flg=0 and banve_no = :banve_no ";
				
				$result = $this->query($sql, $sel_param);				
				if ($result[0]['cnt'] > 0) {
					$msg_err= sprintf(Message_model::get_msg('BVE029'), $sel_param['banve_no']);
           			ACWError::add('banve_no', $msg_err);
					//ACWError::add('banve_no', 'Mã bản vẽ "'.$sel_param['banve_no'].'" đã có, vui lòng nhập tên khác !');
					return false;
				}	
			}				
		}
        //$this->commit();
		return true;
	}
	
	public function update_banve($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];
		//$param['user_id_2'] = $login_info['user_id'];
		if(!isset($param['del_flg'])){
			$param['del_flg']='0';
		}
		$sql = "INSERT INTO banve
					(
					parent_id
                    ,kho_giay
					,banve_no
					,banve_name
					,level
					,del_flg
					,add_user_id
					,add_datetime
					,upd_user_id
					,upd_datetime
                    ,dungchung
					)
				VALUES
					(
					:parent_id
                    ,:kho_giay
					,:banve_no
					,:banve_name
					,:level
					,0
					,:user_id
					,now()
					,:user_id
					,now()
                    ,:dungchung
					)
				";
		
		$param_new['parent_id'] = $param['my_id'];
		$param_new['user_id'] = $login_info['user_id'];        
		if (isset($param['my_id']) == false) {  //add new 
			$parent_info = $this->get_banve_info($param['parent_id']);
			/*$param['level']=1;
			if(count($parent_info)>0){
				$param['level']=$parent_info['level']+1;	
			}*/			
            if(isset($param['banve_no'])===FALSE || strlen($param['banve_no'])==0){
                $maxno= $this->get_banve_maxno($param['level'],$parent_info['banve_no']);
                $param['banve_no'] = substr( $parent_info['banve_no'],0,3). str_pad($maxno,4,'0',STR_PAD_LEFT) ;
            }else
            if($param['dungchung'] != '1'){
                $param['dungchung'] ='0';
            }
			$this->execute($sql, ACWArray::filter($param, array(
					'parent_id'
					,'banve_name'
					,'banve_no'
					,'level'
					,'user_id'
					,'kho_giay'
                    ,'dungchung'
					)));            			
			$result = $this->query("SELECT LAST_INSERT_ID() AS banve_id");			
			$param_new['parent_id'] = $result[0]['banve_id'];			
		} else 
		{
			$param['ctg_head_id'] = $param['my_id'];			
			$this->execute("
				UPDATE banve
					SET
					banve_name = :banve_name
                    ,kho_giay = :kho_giay
					,del_flg = :del_flg
					,upd_user_id = :user_id
					,upd_datetime = now()
				WHERE
					banve_id = :my_id
				", ACWArray::filter($param, array(
					'my_id'
					,'banve_name'
					,'user_id'
					,'del_flg'
                    ,'kho_giay'
					)));			
		}
		//insert folder con
		$parent_info = $this->get_banve_info($param_new['parent_id']);
		/*$param_new['level']=1;
		if(count($parent_info)>0){
			$param_new['level']=$parent_info['level']+1;	
		}*/
		if(isset($param['add_child']) || isset($param['add_banve_no'])){
            //$maxno = $this->get_banve_maxno($param_new['level'],$parent_info['banve_no']);
			foreach($param['add_child'] as $key=>$row){
				if((isset($row) && !empty($row)) || $param['add_dungchung'][$key] == '1'){                    
					$param_new['banve_name'] = $row;
                    $param_new['level'] = $param['add_level'][$key];
                    $param_new['kho_giay'] = $param['add_khogiay'][$key];
                    $param_new['banve_no'] = $param['add_banve_no'][$key];
                    $param_new['dungchung'] = $param['add_dungchung'][$key];
                    
                    if(isset($param_new['banve_no'])== FALSE || strlen($param_new['banve_no'])==0)
                    {
                        $maxno = $this->get_banve_maxno($param_new['level'],$parent_info['banve_no']);
                        $param_new['banve_no']=substr( $parent_info['banve_no'],0,3). str_pad($maxno,4,'0',STR_PAD_LEFT) ;
                    }
                    if($param_new['dungchung'] != '1'){
                        $param_new['dungchung'] ='0';
                    }
                    if ($this->check_banve_id($param_new)){
    					$this->execute($sql,$param_new);
                        //$maxno++;
                    }	
				}				
			}
		}

		$this->commit();
	}
	
	public function delete_banve($banve_id)
	{
		$this->begin_transaction();
		
		$series = $this->query("
			SELECT
				count(*) AS cnt
			FROM
				banve
			WHERE parent_id = :banve_id and del_flg = 0
			", array(
				'banve_id' => $banve_id
			));
		if ($series[0]['cnt'] > 0) {			
           	ACWError::add('banve_child', Message_model::get_msg('BVE037'));
			//ACWError::add('banve_child', 'Tồn tại bản vẻ con, không thể xóa');
			return;
		}
	    $user_login = ACWSession::get('user_info');
		$this->execute("UPDATE banve SET del_flg = 1 
                        ,upd_user_id = :user_id
					    ,upd_datetime = now()
                        WHERE banve_id = :banve_id",
				array('banve_id' => $banve_id,'user_id'=>$user_login['user_id']));		

		$this->commit();
	}
	public function cancel_banve($banve_id,$status = 2)
	{
		$this->begin_transaction();
		
		$series = $this->query("
			SELECT
				count(*) AS cnt
			FROM
				banve
			WHERE parent_id = :banve_id and del_flg = 0
			", array(
				'banve_id' => $banve_id
			));
		if ($series[0]['cnt'] > 0) {			
           	ACWError::add('banve_child', Message_model::get_msg('BVE052'));
			//ACWError::add('banve_child', 'Tồn tại bản vẻ con, không thể hủy');
			return;
		}
	    $user_login = ACWSession::get('user_info');
		$this->execute("UPDATE banve SET del_flg = :status
                        ,upd_user_id = :user_id
					    ,upd_datetime = now()
                        WHERE banve_id = :banve_id",
				array('banve_id' => $banve_id,'status'=>$status,'user_id'=>$user_login['user_id']));		

		$this->commit();
	}
	public function get_all($level = '')
	{
        $sql ="select t.banve_id,t.banve_name,t.dungchung,
                        t.banve_no,t.kho_giay,t.level,t.parent_id ,u.user_name
												,DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime	,
												t.upd_datetime,t.upd_user_id,
												concat(kho_giay,banve_no) banve_tong 
                        FROM banve t
                        LEFT JOIN m_user u on u.user_id = t.add_user_id
				where t.del_flg=0 and t.banve_id <>1  ";
        if($level !=''){
            $sql .=" and level in ( ".$level ." )";
        }
        $sql .=" order by banve_id";
        return $this->query($sql);
    }
    public function get_banve_tong($level = '')
	{
        $sql ="select distinct concat(kho_giay,banve_no) banve_tong from banve where del_flg=0 and banve_id <>1 ";
        if($level !=''){
            $sql .=" and level = ".$level;
        }
        $sql .=" order by banve_no";
        return $this->query($sql);
    }
	public function get_banve_all($param,$flg_move = FALSE)
	{
        $this->begin_transaction();
		$sql = "select t.id
				,t.category_name			
				,t.parent_id
				,t.is_folder
				,t.upd_sec
				,t.level
				,t.del_flg
				from ( SELECT
				  chd.banve_id AS id
				,chd.banve_no AS category_name
				,chd.banve_no
				,chd.parent_id AS parent_id
				,true AS is_folder
				,1 AS upd_sec
				,chd.level
				,chd.del_flg
			FROM		banve chd
			WHERE	 banve_id = 1
            union 
            SELECT
				  chd.banve_id AS id
				,concat(kho_giay,chd.banve_no,'-',chd.banve_name) AS category_name
				,chd.banve_no
				,chd.parent_id AS parent_id
				,true AS is_folder
				,1 AS upd_sec
				,chd.level
				,chd.del_flg
			FROM		banve chd
			WHERE	 chd.del_flg <> 1
			and chd.banve_id > 1
		";
        $param_sql =array();        
        $flg_check = FALSE;
        $where ="";
        if(isset($param['s_banve_no']) && strlen(trim($param['s_banve_no']))>0){
            //$where .="and banve_no = :s_banve_no";
            $param_sql['banve_no']='%'.$param['s_banve_no'].'%';
            $flg_check = TRUE;
        }else{
            $param_sql['banve_no']= "%";
        }
        if(isset($param['s_banve_name']) && strlen(trim($param['s_banve_name']))>0){
            //$where .="and LOWER(banve_name) like LOWER(:s_banve_name)";
            $param_sql['banve_name']='%'.$param['s_banve_name'].'%';
            $flg_check = TRUE;
        }else{
            $param_sql['banve_name']= "%";
        }
        if($flg_move){
			$sql .=" and COALESCE(chd.level,0) <> 5";
		}
        if($flg_check){
        	/*$this->execute("CREATE TEMPORARY TABLE IF NOT EXISTS temp_banve  (banve_id int,chk_mk int,parent_id int,chk_parent int) ENGINE=MEMORY ;");
            $this->query("select f_get_all_banve(:banve_no,:banve_name)",$param_sql);
            $sql .= " and chd.banve_id in (select distinct banve_id from temp_banve)";*/
            /*$sql .= "and chd.banve_id in (select banve_id 
                    from banve
                    where del_flg = 0
                    ".$where."
                    union
                    select  banve_id
                    from    banve,
                    			(select @pv := (select group_concat(banve_id separator ',') 
                    			 from banve
                    			 where del_flg = 0
                    			".$where." )
                    			) initialisation
                    where   find_in_set(parent_id, @pv) > 0
                    and     @pv := concat(@pv, ',', banve_id))
                    ";*/
            /*$sql .=" and chd.banve_id in
            (select  banve_id
              from    banve,
                       (select @pv := (select group_concat(parent_id separator ',') 
                        from banve
                        where del_flg = 0
                        and banve_no like :banve_no
												and lower(banve_name) like  lower(:banve_name) )
                       ) initialisation
            where   find_in_set(banve_id, @pv) > 0
                    and     @pv := concat(@pv, ',', parent_id)
			union 
			select  banve_id
			from    banve,
			        (select @pv2 := (select group_concat(banve_id separator ',') 
			        from banve
			        where del_flg = 0
			            and banve_no like :banve_no
			            and lower(banve_name) like  lower(:banve_name) )
			        ) initialisation
			where   find_in_set(parent_id, @pv2) > 0
				and     @pv2 := concat(@pv2, ',', banve_id)
			union 
			select banve_id         
			from banve
			where del_flg = 0
				and banve_no like :banve_no
				and lower(banve_name) like  lower(:banve_name)
			)";*/
			$this->execute("SET @@session.group_concat_max_len = 10000;");
			$res = $this->query("select f_get_all_banve_new(:banve_no,:banve_name) as bv_id ",$param_sql);
			$list_id ="1";
			if(count($res) >0 ){
				$list_id = $res[0]['bv_id'];
			}
			$sql .=" and FIND_IN_SET( banve_id,'$list_id') ";
        }
        $sql.=" ) t  order by t.banve_no";
		$res = $this->query($sql);		
        $this->commit();
        return $res;
	}
	/**
	 * カテゴリ情報獲得
	 */
	public function get_banve_info($banve_id)
	{
		$r = $this->query("SELECT t.banve_id,t.banve_name,t.dungchung,
                        t.banve_no,t.kho_giay,t.level,t.parent_id ,u.user_name
                        ,DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime	
                        ,t.del_flg
                        FROM banve t
                        LEFT JOIN m_user u on u.user_id = t.add_user_id
                        				WHERE	 t.del_flg <> 1
                        				and	t.banve_id = :banve_id 
			", array ('banve_id' => $banve_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
    public function get_banve_byno($banve_no)
	{
		$r = $this->query("SELECT distinct t.banve_name,t.banve_no,t.kho_giay
                            FROM banve t
                            LEFT JOIN m_user u on u.user_id = t.add_user_id
                            WHERE	 t.del_flg = 0
                            		and	t.banve_no = :banve_no
			", array ('banve_no' => $banve_no));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	public function get_banve_maxno($level,$parent_no)
	{
		$r = $this->query("SELECT max(SUBSTR(f.banve_no,4,LENGTH(f.banve_no))) mx FROM banve f
				WHERE	 f.del_flg = 0
                and dungchung =0
				and	f.level = :level
         and SUBSTR(f.banve_no,1,3) = :parent_no
			", array ('level' => $level,'parent_no'=>substr($parent_no,0,3)));
		if(isset($r[0]['mx'])){
            return ($r[0]['mx']+1);
        }			
		else{
            if($level=='1')
                return 1;
            else if($level=='2'){
                return 10;
            }else if($level=='3'){
                return 100;
            }else if($level=='4'){
                return 1000;
            }else if($level=='5'){
                return 5000;
            }
        }			
	}
	
	public function get_child_banve($folder_id)
	{
		$sql="select t.banve_id,t.banve_name,t.dungchung,
            t.banve_no,t.kho_giay,t.level,t.parent_id ,u.user_name
            ,DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime 
            from banve t
            LEFT JOIN m_user u on u.user_id = t.add_user_id
            where parent_id =:banve_id 
            and t.del_flg=0 order by banve_id";
		return $this->query($sql,array('banve_id'=>$folder_id));
	}
	
   /* public function log_csv($path_output,$line_insert)
	{
		$file = new FileWindows_lib();
		if ($file->FileExists($path_output) == FALSE) {
			$csv_header = "Khổ giấy,Mã bản vẽ,Loại bản vẽ,Tên bản vẽ\r\n";
			$csv_header = mb_convert_encoding($csv_header, "UTF-8", "UTF-8");
			file_put_contents($path_output, $csv_header);
		}
		$line_insert = mb_convert_encoding($line_insert, "UTF-8", "UTF-8");
		file_put_contents($path_output, $line_insert, FILE_APPEND);		
	}*/	
    public function export_exce($file_name)
    {
        $file = new FileWindows_lib();
        $excel = new ImportExport_lib();
        if ($file->FileExists($file_name) == FALSE) {
            $file->CopyFile(ACW_ROOT_DIR.'/shared/Template.xlsx',$file_name);
        }
        $excel->load($file_name);
        $lang = Message_model::get_message('BANVE');        
        $excel->set_value_no(5,1,$lang['BVE031']); //'Khổ giấy'
        $excel->set_value_no(6,1,$lang['BVE032']);  //'Mã bản vẽ'
        $excel->set_value_no(7,1,$lang['BVE033']);  //'Loại bản vẽ'
        $excel->set_value_no(8,1,$lang['BVE034']);  // 'Tên bản vẽ'
        $excel->set_value_no(9,1,$lang['BVE030']); //'Dùng chung'
        $excel->set_value_no(10,1,$lang['BVE035']);  //'Người tạo'
        $excel->set_value_no(11,1,$lang['BVE036']);  //'Ngày tạo'     
        $list = $this->get_all();
        $list = $this->sort_to_tree($list);
        $row_index = 1;
        $this->write_to_excel($excel,$list,$lang,$row_index);
        /*foreach($list as $key=>$row){
        	$loai_bv ='';
        	if($row['level']=='1'){
				$loai_bv =$lang['BVE011'];//'Bản vẽ tổng thể';
			}else if($row['level']=='2'){
				$loai_bv =$lang['BVE012'];//'Bản vẽ cụm lớn';
			}else if($row['level']=='3'){
				$loai_bv =$lang['BVE013'];//'Bản vẽ cụm nhỏ';
			}else if($row['level']=='4'){
				$loai_bv =$lang['BVE014'];//'Bản vẽ chi tiết';
			}else if($row['level']=='5'){
				$loai_bv =$lang['BVE015'];//'Bản vẽ phôi';
			}
			if($row['dungchung']=='1'){
				$dungchung = 'Có' ;		
			}else{
				$dungchung = 'Không' ;
			}
			$excel->set_value_no(0,$key+2,$dungchung);
            $excel->set_value_no(1,$key+2,$row['kho_giay']);
            $excel->set_value_no(2,$key+2,$row['banve_no']);	
            $excel->set_value_no(3,$key+2,$loai_bv);	
            $excel->set_value_no(4,$key+2,$row['banve_name']);	
            $excel->set_value_no(5,$key+2,$row['user_name']);
        	$excel->set_value_no(6,$key+2,$row['add_datetime']); 
		}*/
        $excel->save($file_name);
        $excel->free();
       
    }
    public function write_to_excel($excel,$data, $lang,&$row_index)
    {
		if(count($data > 0)){
			foreach($data as $key=>$row){
	        	$loai_bv ='';
	        	$row_index++;
	        	if($row['level']=='1'){
					$loai_bv =$lang['BVE011'];//'Bản vẽ tổng thể';
					$excel->set_background_color('blue',$row_index);
					$excel->set_value_no(0,$row_index,"+");
					$excel->set_value_no(1,$row_index,"-");
					$excel->set_value_no(2,$row_index,"-");
					$excel->set_value_no(3,$row_index,"-");
					$excel->set_value_no(4,$row_index,"-");
					$this->set_color($excel,0,11,$row_index,'C5D9F1');
				}else if($row['level']=='2'){
					$loai_bv =$lang['BVE012'];//'Bản vẽ cụm lớn';					
					$excel->set_value_no(1,$row_index,"+");
					$excel->set_value_no(2,$row_index,"-");
					$excel->set_value_no(3,$row_index,"-");
					$excel->set_value_no(4,$row_index,"-");
					$this->set_color($excel,1,11,$row_index,'F2DDDC');
				}else if($row['level']=='3'){
					$loai_bv =$lang['BVE013'];//'Bản vẽ cụm nhỏ';					
					$excel->set_value_no(2,$row_index,"+");
					$excel->set_value_no(3,$row_index,"-");
					$excel->set_value_no(4,$row_index,"-");
					$this->set_color($excel,2,11,$row_index,'EAF1DD');
				}else if($row['level']=='4'){
					$loai_bv =$lang['BVE014'];//'Bản vẽ chi tiết';					
					$excel->set_value_no(3,$row_index,"+");
					$excel->set_value_no(4,$row_index,"-");
					$this->set_color($excel,3,11,$row_index,'E5E0EC');
				}else if($row['level']=='5'){
					$loai_bv =$lang['BVE015'];//'Bản vẽ phôi';
					$excel->set_value_no(4,$row_index,"+");
					//$this->set_color($excel,4,11,$row_index,'C5D9F1');
				}
				if($row['dungchung']=='1'){
					$dungchung = 'Có' ;		
				}else{
					$dungchung = 'Không' ;
				}
				
				
	            $excel->set_value_no(5,$row_index,$row['kho_giay']);
	            $excel->set_value_no(6,$row_index,$row['banve_no']);	
	            $excel->set_value_no(7,$row_index,$loai_bv);	
	            $excel->set_value_no(8,$row_index,$row['banve_name']);	
	            $excel->set_value_no(9,$row_index,$dungchung);
	            $excel->set_value_no(10,$row_index,$row['user_name']);
	        	$excel->set_value_no(11,$row_index,$row['add_datetime']); 
	        	$this->write_to_excel($excel,$row['child'],$lang,$row_index);
			}
		}
	}
	public function set_color($excel,$from,$to,$row,$color){
		for($i=$from ; $i<=$to ;$i++){
			$excel->set_bg_color($i,$row,$color);
		}		
	}
    public function sort_to_tree($data){
    	$list = array();
    	$index =0;
    	foreach($data as $key=>$row){    		
    		if($row['level']=='1'){
    			$index++;
    			$list[$index]['level']=$row['level'];
    			$list[$index]['kho_giay']=$row['kho_giay'];
    			$list[$index]['banve_no']=$row['banve_no'];
    			$list[$index]['banve_name']=$row['banve_name'];
    			$list[$index]['dungchung']=$row['dungchung'];
    			$list[$index]['user_name']=$row['user_name'];
    			$list[$index]['add_datetime']=$row['add_datetime'];
    			$list[$index]['child']= $this->get_child($data,$row['banve_id']);
    		}
    	}
		return $list;
	}
	public function get_child($data,$parent_id){
		$list = array();
    	$index =0;
		foreach($data as $key=>$row){
			if($row['parent_id']==$parent_id){
				$index++;
    			$list[$index]['level']=$row['level'];
    			$list[$index]['kho_giay']=$row['kho_giay'];
    			$list[$index]['banve_no']=$row['banve_no'];
    			$list[$index]['banve_name']=$row['banve_name'];
    			$list[$index]['dungchung']=$row['dungchung'];
    			$list[$index]['user_name']=$row['user_name'];
    			$list[$index]['add_datetime']=$row['add_datetime'];
    			$list[$index]['child']= $this->get_child($data,$row['banve_id']);
			}
		}
		return $list;		
	}
    public function get_all_child($banve_id){
        $sql ="	select  banve_id,
								banve_name,
								parent_id ,kho_giay,banve_no,level
		from banve where banve_id = :banve_id
		union
select  banve_id,
								banve_name,
								parent_id  ,kho_giay,banve_no,level
		from    banve,
								(select @pv := CONCAT( :banve_id,'')) initialisation
		where   find_in_set(parent_id, @pv) > 0
				and     @pv := concat(@pv, ',', banve_id)
ORDER BY parent_id;";
        $result = $this->query($sql,array('banve_id'=>$banve_id)); 
        return $result;
    }
    public function copy_exe($param){
        
        $sql = "INSERT INTO banve
					(
					parent_id
                    ,kho_giay
					,banve_no
					,banve_name
					,level
					,del_flg
					,add_user_id
					,add_datetime
					,upd_user_id
					,upd_datetime
                    ,dungchung
					)
				VALUES
					(
					:parent_id
                    ,:kho_giay
					,:banve_no
					,:banve_name
					,:level
					,0
					,:user_id
					,now()
					,:user_id
					,now()
                    ,:dungchung
					)
				";
        $all_child = $this->get_all_child($param['banve_form']);
        $parent_id = 1;
        $user_login = ACWSession::get('user_info');
        $sql_pa['user_id'] = $user_login['user_id'];
        $sql_pa['dungchung'] = 1;
        if(strlen($param['banve_to']) > 0){
            $parent_info = $this->get_banve_info($param['banve_to']);
            $parent_id = $parent_info['banve_id'];
        }
        $this->begin_transaction();
        foreach($all_child as $key => $row){
            if($key === 0){
                $sql_pa['parent_id'] = $parent_id;
            }else{
                $sql_pa['parent_id'] = $this->find_parent_new($all_child,$row['parent_id']);
            }
            $sql_pa['kho_giay'] =$row['kho_giay'];
            $sql_pa['banve_no'] =$row['banve_no'];
            $sql_pa['level'] =$row['level'];
            $sql_pa['banve_name'] =$row['banve_name'];
            $this->execute($sql, $sql_pa);
            $result = $this->query("SELECT LAST_INSERT_ID() AS banve_new_id");            
            $all_child[$key]['banve_new_id'] =  $result[0]['banve_new_id'];
        }
        $this->commit();
    }
    public function find_parent_new($list ,$banve_id){
        foreach($list as $key =>$val){
            if($val['banve_id'] == $banve_id){
                return $val['banve_new_id'];
            }
        }
        return false;
    }
    public static function action_movebv()
	{
		/*$params = self::get_param(array('banve_id'));

		$db = new Banve_model();
		$param_s['s_banve_no']= '9891001';
		$param_s['s_banve_name']= '';
		$list = $db->get_banve_all($param_s); 
		$out = $params;
		$out['json_series_list'] = json_encode( $list);

		return ACWView::template('banve/move.html', $out);*/
		
		$param = self::get_param(array(
			's_banve_no'
			,'s_banve_name'	
			//'banve_id'		
			));
		$db = new Banve_model();
		//$param['s_banve_no']= '9891001';
		//$list = $db->get_banve_all($param,TRUE);
		//return ACWView::template($result);
		
		$out = $param;
		$out['select_series_head_id'] = 0;
		//$out['list']= array();
		return ACWView::template('banve/move.html', $out);
				
	}
	public static function action_move()
	{		
		$param = self::get_param(array(
			'banve_id'
			,'to_parent_id'						
			));
		$db = new Banve_model();
		$result['status'] ='OK';
		$result['msg'] = "Di chuyển thành công !";		
		if($param['banve_id']==$param['to_parent_id']){
			$result['status'] ='NG';
			$result['msg'] ='Không thể di chuyển vào chính nó';
			return ACWView::json($result);
		}
		$row = $db->get_banve_byid($param['to_parent_id']);
		if($row['level']=='5'){
			$result['status'] ='NG';
			$result['msg'] ='Không thể di chuyển vào bản vẽ phôi';
		}else{
			$db->move_banve($param);
		}
		
		return ACWView::json($result);
				
	}
	public function move_banve($param){
		$sql=" update banve 
				set parent_id = :to_parent_id
				,upd_datetime = NOW()
				,upd_user_id = :user_id
				where banve_id =:banve_id ";
		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id']; 
		$this->execute($sql,$param);
	}
	public function get_banve_byid($banve_id)
	{
		$r = $this->query("SELECT t.banve_name,t.banve_no,t.kho_giay,t.level
                            FROM banve t                        
                            WHERE	 t.del_flg = 0
                            		and	t.banve_id = :banve_id
			", array ('banve_id' => $banve_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	public static function action_movetree()
	{
        $param = self::get_param(array(
			'top_banve_id'	
			));
		$db = new Banve_model();
		$row = $db->get_banve_byid($param['top_banve_id']);
		$param_seach['s_banve_no'] = $row['banve_no'];
		$result = $db->get_banve_all($param_seach,true);
		return ACWView::json($result);
	}
}
/* ファイルの終わり */