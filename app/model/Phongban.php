<?php
/**
 * ユーザ管理
 *
*/
class Phongban_model extends ACWModel
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
                    if($param['search_phongban_name'] != ''){
                        $s_phongban_name = $param['search_phongban_name'];
                        $param['s_phongban_name'] = strtolower($s_phongban_name);
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
		$param = self::get_param(array(
			'search_phongban_name'
		));
		$model = new Phongban_model();
		$rows = $model->get_phongban_rows($param);		
		return ACWView::template('phongban.html', array(
			'data_rows' => $rows,			
			'search_phongban_name'=>$param['search_phongban_name']
		));
	}

	/**
	 * 編集
	 * @return string
	 */
	public static function action_edit()
	{
		$params = self::get_param(array(
			'phongban_id'
		));
		
		$phongban_id = null;
		if (isset($params['phongban_id'])) {
			$phongban_id = $params['phongban_id'];
		}
		$model = new Phongban_model();
		$data_row = array();
		if ($phongban_id == null) {			
			$data_row['phongban_id'] = null;
			$data_row['phongban_name'] = null;
			$data_row['donvi_id'] = null;
			$data_row['del_flg'] = null;
		} else {
			$data_row = $model->get_phongban_row($phongban_id);			
		}
        $usr= new User_model();
		return ACWView::template('phongban/edit.html', array(
			'data_row' => $data_row	
			,'don_vi'=>$usr->get_donvi()
        ));
	}
	
	private static function _validate_update(&$param)
	{
	    /**
	     * 
	     * */	    
		$validate = new Validate_lib();
		
		$param['phongban_name'] = $validate->trim_ext($param['phongban_name']);
		if ($validate->type_str('phongban_name', 'Tên đơn vị', $param['phongban_name'], true) == false) {
			return false;
		}        
        if($param['phongban_name'] != ''){
            if(strlen($param['phongban_name']) > 100){
                ACWError::add('lelng', 'Tên đơn vị không được quá 100 ký tự');
                return false;
            }
        }
        if($param['donvi_id'] == '' ){
        	ACWError::add('dv', 'Bạn chưa chọn đơn vị');
            return false;
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
			'phongban_id',
			'phongban_name',
			'donvi_id',
                'del_flg'
		));
		
		if (self::get_validate_result() === true) {
			$model = new Phongban_model();
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
	
	
	public function get_phongban_rows($param)
	{
		$sql = "
			SELECT				 
				 t.phongban_id
				, t.phongban_name  
                ,d.donvi_name            
                ,t.del_flg
			FROM
				phong_ban t
				left join don_vi d on t.donvi_id=d.donvi_id
		";
		
		if (isset($param['s_phongban_name'])) {
			$sql_param = array(
					'phongban_name' =>  '%' . SQL_lib::escape_like($param['s_phongban_name']) . '%'
				);
			$sql .= " WHERE lower(t.phongban_name) like lower(:phongban_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.phongban_id
		";		
		//var_dump($this->query($sql, $sql_param)); die;
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		
		$pass_md5 = null;		
		$sql_params = array(
			'phongban_id' => $params['phongban_id'],
			'phongban_name' => $params['phongban_name'],
			'donvi_id' => $params['donvi_id'],            
          //  'del_flg' => $params['del_flg'],
            'upd_user_id'=>$login_info['user_id']
		);		
        $res = $this->get_phongban_name_count($params);
		if ($res['cnt'] > 0) {
			ACWError::add('phongban_name', 'Tên phòng ban "'.$params['phongban_name'].'" đã có, vui lòng sử dụng tên khác');
			return;
		}
		if ($params['phongban_id'] == null) {	
		
			$sql = "
				INSERT INTO
					phong_ban
				(
					  phongban_id
					, phongban_name					
					, donvi_id
                    , del_flg
					, add_user_id
					, add_datetime
					, upd_user_id
					, upd_datetime
				) VALUES (
					  :phongban_id 
					, :phongban_name
					, :donvi_id 					
                    , 0
					, :add_user_id 
					, NOW() 
					, :upd_user_id 
					, NOW() 
				);
			";
			$sql_params['add_user_id'] = $login_info['user_id'];
		} else {							
			
			$sql = "
				UPDATE
					phong_ban
				SET
					  phongban_id = :phongban_id
					, phongban_name = :phongban_name
					,donvi_id = :donvi_id					
					, del_flg = COALESCE(:del_flg, 0)
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
				WHERE
					phongban_id = :phongban_id
			";
			$sql_params['donvi_id'] = $params['donvi_id'];
			$sql_params['del_flg'] = $params['del_flg'];
		}
		$this->execute($sql, $sql_params);		
		
		$this->commit();
		
		return true;
	}
	
	/**
	 * ユーザ取得
	 * @param integer $m_user_id ユーザID
	 * @return array
	 */
	public function get_phongban_row($phongban_id)
	{
		$rows = $this->query("
				SELECT
					t.*
				FROM
					phong_ban t
				WHERE
					t.phongban_id = :phongban_id
			", array("phongban_id" => $phongban_id)
		);
		
		return $rows[0];
	}
	
	private function get_phongban_name_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				phong_ban
			WHERE
				phongban_name = :phongban_name
		";
		$param_sql['phongban_name'] = $param['phongban_name'];
        if(isset($param['phongban_id'])){
            $sql .= " and phongban_id <> :phongban_id";
            $param_sql['phongban_id'] = $param['phongban_id'];
        }
		//$filter = ACWArray::filter($param, array('phongban_name'));
		$rows = $this->query($sql, $param_sql);
		return $rows[0];
	}
	
	
}
/* ファイルの終わり */