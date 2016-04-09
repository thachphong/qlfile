<?php
/**
 * カテゴリ登録
 *
 * TODO:親カテゴリによる排他制御
 *
*/
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
        $param = self::get_param(array('s_banve_no','s_banve_name'));
		/*$param = array(
			'section_type' =>null//=> json_encode(Section_common_model::get_type())
			,'common_spec' =>null// Section_common_model::get_icon_all()
			);*/
		return ACWView::template('banve.html', $param);
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('acw_url'));	// 親IDを取る
		if (self::get_validate_result() == false) {
			return ACWView::OK;	// 何も返さない
		}		
		$db = new Banve_model();	
		$param['banve_no'] = "";	
		$param['banve_name'] = "";	
		$param['banve_child'] = NULL;
		$param['parent_level'] = 1;
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
        $param['banve_name'] = $result['banve_name'];
		$param['parent_id'] = $result['parent_id'];		
		$param['banve_child'] = $db->get_child_banve($param['my_id']);
        $param['parent_level'] = 1;
        if(count($result)>0){
			$param['parent_level'] = $result['level']-1;
		}
		return ACWView::template('banve/edit.html', $param);
	}

	/**
	 * 更新
	 */
	public static function action_update()
	{
		// 最初に決まっているもの
		$param = self::get_param(array(
			'banve_name'
			,'banve_no'
            ,'level'
			, 'my_id'	
			, 'parent_id'
			, 'add_child'
            ,'add_level'
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
				if(isset($param['add_child'])){
					$add_child = $param['add_child'];
					foreach($add_child as $key => $item){
						if(!in_array($item,$list_child))	
						{
							$add_new[] = $item ;
                            $add_level[]= $param['add_level'][$key]	;						
						}
						$list_child[]= $item;
					}	
					
				}
				$param['add_child'] = $add_new;
                $param['add_level'] = $add_level;
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
		case 'update':
			return self::_validate_category($param);
		case 'upload':
			return self::_validate_upload($param);  
		}
		return true;
	}
    
	/**
	* カテゴリ入力チェック
	*/
	private static function _validate_category(&$param)
	{
		if ((isset($param['banve_name']) == false) || ($param['banve_name'] == '')) {
			ACWError::add('banve_name', 'Vui lòng nhập tên thư mục !');
			return false;
		}
		return true;
	}

	/**
	 * アップロード時のチェック
	 */
	private static function _validate_upload(&$param)
	{
		ACWLog::debug_var('UPLOAD_APPEND', $param);
		
		if ((isset($param['t_ctg_section_id']) == false) || ($param['t_ctg_section_id'] == '')) {
			ACWError::add('upload', '未登録の項目ではアップロードできません。');
			return false;
		}
		
		if (is_null($param['upload_file'])) {
			// NULL →　ファイルの容量制限オーバー
			ACWError::add('upload', 'アップロードに失敗しました。');
			return false;
		}
		
		if ($param['upload_file']['error'] != UPLOAD_ERR_OK) {
			// ファイルアップロードエラーあり
			if ($param['upload_file']['error'] == UPLOAD_ERR_NO_FILE) {
				// ファイル未選択
				ACWError::add('upload_file', 'アップロードするファイルが選択されていません。');
			} else {
				// その他
				ACWError::add('upload_file', 'アップロードに失敗しました。');
			}
			return false;
		}
		
		$exp = explode('/', $param['upload_file']['type']);
		if (strcmp($exp[0], 'image') != 0) {
			ACWError::add('upload_file', '画像ファイルを選択してください。');
			return false;
		}
		
		return true;
	}		
	//kiem tra trung ten trong chung folder cha
	public function check_banve_id($param)
	{
		//$this->begin_transaction();

		$sel_param = ACWArray::filter($param, array('banve_name'));
		$sql = "SELECT COUNT(*) cnt FROM banve WHERE del_flg=0 and banve_name = :banve_name ";
		if (isset($param['my_id'])) {
			$sel_param['banve_id'] = $param['my_id'];
			$sql .= ' AND banve_id <> :banve_id';	
		}
		$result = $this->query($sql, $sel_param);	
		if ($result[0]['cnt'] > 0) {
			ACWError::add('banve_name', 'Tên bản vẽ "'.$sel_param['banve_name'].'" đã có, vui lòng nhập tên khác !');
			return false;
		}
		if(isset($param['banve_no'])&& strlen($param['banve_no'])>0 ){
			if(isset($param['my_id'])===FALSE){
				$sel_param = ACWArray::filter($param, array('banve_no'));
				$sql = "SELECT COUNT(*) cnt FROM banve WHERE del_flg=0 and banve_no = :banve_no ";
				
				$result = $this->query($sql, $sel_param);				
				if ($result[0]['cnt'] > 0) {
					ACWError::add('banve_no', 'Mã bản vẽ "'.$sel_param['banve_no'].'" đã có, vui lòng nhập tên khác !');
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
					,banve_no
					,banve_name
					,level
					,del_flg
					,add_user_id
					,add_datetime
					,upd_user_id
					,upd_datetime
					)
				VALUES
					(
					:parent_id
					,:banve_no
					,:banve_name
					,:level
					,0
					,:user_id
					,now()
					,:user_id
					,now()
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
                $param['banve_no'] = substr( $parent_info['banve_no'],0,4). str_pad($maxno,4,'0',STR_PAD_LEFT) ;
            }
			$this->execute($sql, ACWArray::filter($param, array(
					'parent_id'
					,'banve_name'
					,'banve_no'
					,'level'
					,'user_id'
					//,'user_id_2'
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
					)));			
		}
		//insert folder con
		$parent_info = $this->get_banve_info($param_new['parent_id']);
		/*$param_new['level']=1;
		if(count($parent_info)>0){
			$param_new['level']=$parent_info['level']+1;	
		}*/
		if(isset($param['add_child'])){
            //$maxno = $this->get_banve_maxno($param_new['level'],$parent_info['banve_no']);
			foreach($param['add_child'] as $key=>$row){
				if(isset($row) && !empty($row)){                    
					$param_new['banve_name'] = $row;
                    $param_new['level'] = $param['add_level'][$key];
                    $maxno = $this->get_banve_maxno($param_new['level'],$parent_info['banve_no']);
                    $param_new['banve_no']=substr( $parent_info['banve_no'],0,4). str_pad($maxno,4,'0',STR_PAD_LEFT) ;
                    if ($this->check_banve_id($param_new)){
    					$this->execute($sql,$param_new);
                        $maxno++;
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
			ACWError::add('banve_child', 'Tồn tại bản vẻ con, không thể xóa');
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
	public function get_all($level = '')
	{
        $sql ="select * from banve where del_flg=0 and banve_id <>1 ";
        if($level !=''){
            $sql .=" and level = ".$level;
        }
        $sql .=" order by banve_no";
        return $this->query($sql);
    }
	public function get_banve_all($param)
	{
        $this->begin_transaction();
		$sql = "SELECT
				  chd.banve_id AS id
				,chd.banve_no AS category_name
				,chd.parent_id AS parent_id
				,true AS is_folder
				,1 AS upd_sec
			FROM		banve chd
			WHERE	 banve_id = 1
            union 
            SELECT
				  chd.banve_id AS id
				,concat(chd.banve_no,'-',chd.banve_name) AS category_name
				,chd.parent_id AS parent_id
				,true AS is_folder
				,1 AS upd_sec
			FROM		banve chd
			WHERE	 chd.del_flg = 0
			and chd.banve_id > 1
		";
        $param_sql =array();        
        $flg_check = FALSE;
        $where ="";
        if(isset($param['s_banve_no']) && strlen(trim($param['s_banve_no']))>0){
            //$where .="and banve_no = :s_banve_no";
            $param_sql['banve_no']=$param['s_banve_no'];
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
        if($flg_check){
            $this->query("select f_get_all_banve(:banve_no,:banve_name)",$param_sql);
            $sql .="and chd.banve_id in (select distinct banve_id from temp_banve)";
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
        }
		$res = $this->query($sql);
        $this->commit();
        return $res;
	}
	/**
	 * カテゴリ情報獲得
	 */
	public function get_banve_info($banve_id)
	{
		$r = $this->query("SELECT *	FROM banve f
				WHERE	 f.del_flg = 0
				and	f.banve_id = :banve_id 
			", array ('banve_id' => $banve_id));
		if(count($r) >0)
			return $r[0];
		else
			return null;
	}
	public function get_banve_maxno($level,$parent_no)
	{
		$r = $this->query("SELECT max(SUBSTR(f.banve_no,5,LENGTH(f.banve_no))) mx FROM banve f
				WHERE	 f.del_flg = 0
				 and	f.level = :level
         and SUBSTR(f.banve_no,1,4) = :parent_no
			", array ('level' => $level,'parent_no'=>substr($parent_no,0,4)));
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
	/*public function get_ctg_child($ctg_parent_id,&$all_ctg_child = NULL)
	{
		if($all_ctg_child == NULL){
			$all_ctg_child = array();
		}
		$obj_select = $this->query("
			SELECT
				t_ctg_head_id,oya_t_ctg_head_id
			FROM
				t_ctg_head
			WHERE
				oya_t_ctg_head_id = :ctg_parent_id AND del_flg = 0
			", array ('ctg_parent_id' => $ctg_parent_id));
			
		if(count($obj_select) > 0){
			foreach($obj_select as $key => $value){
				$all_ctg_child[] = $value;
				$this->get_ctg_child($value["t_ctg_head_id"],$all_ctg_child);
			}
			
		}
			
		return $all_ctg_child;
	}*/
	
	public function get_child_banve($folder_id)
	{
		$sql="select * from banve where parent_id = :banve_id and del_flg=0 order by banve_id";
		return $this->query($sql,array('banve_id'=>$folder_id));
	}
	
    public function get_ctg_head_id($ctg_id){
        $sql="select t_ctg_head_id from t_ctg_head where ctg_id =:ctg_id ";
        $res = $this->query($sql,array("ctg_id"=>$ctg_id));
        if(count($res)>0){
            return $res[0]['t_ctg_head_id'];
        }else{
            return 0;
        }
    }
}
/* ファイルの終わり */