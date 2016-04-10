<?php
/**
 * カテゴリ登録
 *
 * TODO:親カテゴリによる排他制御
 *
*/
class Foldernhom_model extends ACWModel
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
		/*$param = array(
			'section_type' =>null//=> json_encode(Section_common_model::get_type())
			,'common_spec' =>null// Section_common_model::get_icon_all()
			);*/
		return ACWView::template('foldernhom.html');
	}
	
	/*public static function action_new()
	{
		$param = self::get_param(array('acw_url'));	// 親IDを取る
		if (self::get_validate_result() == false) {
			return ACWView::OK;	// 何も返さない
		}		
		$db = new Category_model();	
		$param['category_code'] = "";	
		$param['folder_child'] = NULL;
		return ACWView::template('category/edit.html', $param);
	}*/

	/**
	 * HTML取得（編集）
	 */
	public static function action_edit()
	{
		$param = self::get_param(array('acw_url'));	
		if (self::get_validate_result() == false) {
			return ACWView::OK;	
		}
		//$db = new Category_model();
		//$result = $db->get_ctg_info($param['my_id']);
		//$param['category_code'] = $result['folder_name'];
		//$param['parent_id'] = $result['parent_folder_id'];
		//$param['folder_child'] = $db->get_child_folder($param['my_id']);
        $fol= new Foldernhom_model();
        $param['donlist'] = $fol->get_tonhom_list($param['my_id']);
        $param['folderfile'] =$fol->get_phanquyen_rows($param['my_id']);
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
			, 'nhom_id'	
	    ));
		
		$result = array('status' => 'OK');
		$result['param'] = $param;
		if (self::get_validate_result() === true) {			
			$db = new Foldernhom_model();
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

		$sql = "INSERT INTO phanquyen
					(
					folder_id
					,phongban_id
                    ,tonhom_id
					,add_user_id
					,add_datetime
					)
				VALUES
					(
					:folder_id
					,:phongban_id
                    ,:tonhom_id
					,:user_id
					,now()					
					)
				";
		$nhomlist = $param['nhom_id'];
        $olddata = $this->get_phanquyen_list($param['folder_id']);
        $addnew = array();
        $delrow = array();
        if(count($nhomlist)>0){
            foreach($nhomlist as $item){
                $flg_exist = TRUE;
                foreach($olddata as $row){
                    if($item== $row['old_id'] )
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
            if(count($nhomlist)>0){
                foreach($nhomlist as $item){
                    if($item== $row['old_id'] )
                    {
                        $flg_del = FALSE;
                    }
                }
            }
            if($flg_del){
                $delrow[] = $row['old_id'];
            }
        }
		$param_new['user_id'] = $login_info['user_id'];
        $param_new['folder_id'] = $param['folder_id'];
        foreach($addnew as $add){
            $addinfo = explode('_',$add);
            $param_new['phongban_id'] = $addinfo[0];
            $param_new['tonhom_id'] = null;
            if(isset($addinfo[1])){
                $param_new['tonhom_id'] =$addinfo[1];
            }
            $this->execute($sql,$param_new);
        }
		$sql_del = "delete from phanquyen
					where folder_id = :folder_id
					and phongban_id= :phongban_id
                    and COALESCE(tonhom_id,0) = :tonhom_id
				";
        $param_del['folder_id'] =$param['folder_id'];        
        foreach($delrow as $del){
            //$param_del['don_id'] = $del;
            $delinfo = explode('_',$del);
            $param_del['phongban_id'] = $delinfo[0];
            $param_del['tonhom_id'] = 0;
            if(isset($delinfo[1])){
                $param_del['tonhom_id'] =$delinfo[1];
            }
            $this->execute($sql_del,$param_del);
        }
		$this->commit();        
        return TRUE;
	}
		
	public function get_tonhom_list($folder_id)
	{
		$sql = "select CONCAT(t.phongban_id,'_',t.tonhom_id) tonhom_id,t.tonhom_name,'0' as flg_pban 
                from to_nhom t where t.del_flg =0
                and NOT EXISTS (SELECT q.folder_id
                        		FROM	phanquyen q
                        		WHERE q.tonhom_id = t.tonhom_id
                                    and q.folder_id = :folder_id
                        	   )
                union
                select CONCAT(p.phongban_id,'_'), p.phongban_name, '1' as flg_pban 
                from phong_ban p where p.del_flg =0
                and NOT EXISTS (SELECT q.folder_id
                        		FROM phanquyen q
                        		WHERE	q.phongban_id = p.phongban_id
                                    and q.folder_id = :folder_id
                        	   )
		";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
	public function get_phanquyen_rows($folder_id)
	{        
		$sql = "select  CONCAT(q.phongban_id,'_',COALESCE(q.tonhom_id,'')) tonhom_id,
                COALESCE(t.tonhom_name,t.tonhom_name ,p.phongban_name) as tonhom_name
                				 
                FROM phanquyen q
                LEFT JOIN to_nhom t on t.tonhom_id = q.tonhom_id and t.phongban_id= q.phongban_id 
                LEFT JOIN phong_ban p on p.phongban_id = q.phongban_id and q.tonhom_id is NULL
                where q.folder_id  =:folder_id
		";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
    public function get_phanquyen_list($folder_id)
	{
		$sql = "select q.* ,CONCAT(q.phongban_id,'_',COALESCE(q.tonhom_id,'')) old_id	 
                FROM phanquyen q               
                where q.folder_id  = :folder_id
		";
		return $this->query($sql,array('folder_id'=>$folder_id));
	}
	
	
}
/* ファイルの終わり */