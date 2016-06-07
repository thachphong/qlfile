<?php
/**
 * カテゴリ登録
 *
 * TODO:親カテゴリによる排他制御
 *
*/
class Folderfile_model extends ACWModel
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
		$param = array(
			'section_type' =>null//=> json_encode(Section_common_model::get_type())
			,'common_spec' =>null// Section_common_model::get_icon_all()
			);
		return ACWView::template('folderfile.html', $param);
	}
	
	public static function action_new()
	{
		$param = self::get_param(array('acw_url'));	// 親IDを取る
		if (self::get_validate_result() == false) {
			return ACWView::OK;	// 何も返さない
		}		
		$db = new Category_model();	
		$param['category_code'] = "";	
		$param['folder_child'] = NULL;
		return ACWView::template('category/edit.html', $param);
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
		$db = new Category_model();
		$result = $db->get_ctg_info($param['my_id']);
		$param['category_code'] = $result['folder_name'];
		$param['parent_id'] = $result['parent_folder_id'];
		$param['folder_child'] = $db->get_child_folder($param['my_id']);
        $fol= new Folderfile_model();
        $param['donlist'] = $fol->get_don_list($param['my_id']);
        $param['folderfile'] =$fol->get_donfolder_rows($param['my_id']);
		return ACWView::json( $param);
	}

	/**
	 * 更新
	 */
	public static function action_update()
	{
		// 最初に決まっているもの
		$param = self::get_param(array(
			'folder_id'
			, 'don_id'	
	    ));
		
		$result = array('status' => 'OK');
		$result['param'] = $param;
		if (self::get_validate_result() === true) {			
			$db = new Folderfile_model();
			$db->_update($param);
		}
        $result['status'] ="OK";
		if (ACWError::count() > 0) {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}
		return ACWView::json($result);
	}
    /**
	 * カテゴリ削除
	 */
	
	
	/**
	* ツリー表示
	*/
	public static function action_tree()
	{
		$db = new Category_model();
		$result = $db->get_category_all();
		return ACWView::json($result);
	}

	/**
	* 並び替え
	*/
	public static function action_history()
	{
		$param = self::get_param(array(
			'folder_name'
            ,'tieude'           
            ,'tu_ngay'
            ,'den_ngay'
            ,'file_name'
		));
		$model = new Folderfile_model();
		$rows = $model->get_history_rows($param);		
        $usr = new User_model();
		return ACWView::template('lichsuphanbo.html', array(
			'data_rows' => $rows			
			,'search_data'=>$param
            ,'donvi_list' =>$usr->get_donvi()
            ,'phongban_list' =>$usr->get_phongban()
            ,'tonhom_list' =>$usr->get_tonhom()
		));
	}
    
	public static function validate($action, &$param)
	{
		switch ($action) {
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
		
		/*case 'update':
			return self::_validate_category($param);*/
		case 'upload':
			return self::_validate_upload($param);  
		}
		return true;
	}
    
	public function _update($param)
	{
		$this->begin_transaction();

		$login_info = ACWSession::get('user_info');
		$param['user_id'] = $login_info['user_id'];

		$sql = "INSERT INTO don_folder
					(
					folder_id
					,don_id
					,add_user_id
					,add_datetime
					)
				VALUES
					(
					:folder_id
					,:don_id
					,:user_id
					,now()					
					)
				";
		$donlist = $param['don_id'];
        $olddata = $this->get_donfolder_rows($param['folder_id']);
        $addnew = array();
        $delrow = array();
        if(count($donlist)>0){
            foreach($donlist as $item){
                $flg_exist = TRUE;
                foreach($olddata as $row){
                    if($item== $row['don_id'] )
                    {
                        $flg_exist = FALSE;
                    }
                }
                if($flg_exist){
                    $addnew[] = $item;
                }
            }
        }
        foreach($olddata as $row){
            $flg_del = TRUE;
            if(count($donlist)>0){
                foreach($donlist as $item){
                    if($item== $row['don_id'] )
                    {
                        $flg_del = FALSE;
                    }
                }
            }
            if($flg_del){
                $delrow[] = $row['don_id'];
            }
        }
		$param_new['user_id'] = $login_info['user_id'];
        $param_new['folder_id'] = $param['folder_id'];
        foreach($addnew as $add){
            $param_new['don_id'] = $add;
            $this->execute($sql,$param_new);
        }
		$sql_del = "delete from don_folder
					where folder_id = :folder_id
					and don_id=:don_id
				";
        $param_del['folder_id'] =$param['folder_id'];        
        foreach($delrow as $del){
            $param_del['don_id'] = $del;
            $this->execute($sql_del,$param_del);
        }
		$this->commit();        
        return TRUE;
	}
	
	public function get_don_list($folder_id)
	{
		$sql = "select t.don_id,
              t.don_no,
              t.tieude,
              lower(t.tieude) tieude_search,
              t.noidung,
              t.loaidon,
              t.trangthai from don t
                where t.trangthai = 3
              and NOT EXISTS (
        		SELECT
        			f.folder_id
        		FROM
        			don_folder f
        		WHERE
        			f.don_id = t.don_id
              and f.folder_id =:folder_id
              )
		";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
	public function get_donfolder_rows($folder_id)
	{
		$sql = "select t.don_id,
              t.folder_id,
			d.tieude
        		FROM
        			don_folder t
						inner join don d on d.don_id = t.don_id
        		WHERE t.folder_id = :folder_id
		";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
    public function get_history_rows($param)
	{
		$sql = "select d.tieude,f.folder_name ,e.file_name,
                DATE_FORMAT(t.add_datetime,'%d/%m/%Y %H:%i:%s') add_datetime
                from don_folder t
                INNER JOIN don d on d.don_id = t.don_id
                INNER JOIN folder f on f.folder_id = t.folder_id
                INNER JOIN file e on e.don_id = t.don_id
                where 1= 1
		";        
        if (isset($param['folder_name']) && empty($param['folder_name'])==FALSE) {
			$sql_param['folder_name'] = '%'.SQL_lib::escape_like($param['folder_name']).'%';
			$sql .= " and lower(f.folder_name) like lower(:folder_name) ";
		}
        if (isset($param['tieude']) && empty($param['tieude'])==FALSE) {
			$sql_param['tieude'] = '%'.SQL_lib::escape_like($param['tieude']).'%';
			$sql .= " and lower(d.tieude) like lower(:tieude) ";
		}
        if (isset($param['file_name']) && empty($param['file_name'])==FALSE) {
			$sql_param['file_name'] = '%'.SQL_lib::escape_like($param['file_name']).'%';
			$sql .= " and lower(e.file_name) like lower(:file_name) ";
		}
        $sql_param['tu_ngay'] ='00/00/0000';        
        if (isset($param['tu_ngay']) && empty($param['tu_ngay'])==FALSE) {
			$sql_param['tu_ngay'] = $param['tu_ngay'];
		}
        if (isset($param['den_ngay']) && empty($param['den_ngay'])==FALSE) {
			$sql_param['den_ngay'] = $param['den_ngay'];
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and STR_TO_DATE(:den_ngay,'%d/%m/%Y %H:%i')";
		}else{
            $sql .= " and t.add_datetime between STR_TO_DATE(:tu_ngay,'%d/%m/%Y %H:%i') and SYSDATE()";
        }
		return $this->query($sql,$sql_param);
	}
	
	
}
/* ファイルの終わり */