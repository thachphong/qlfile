<?php
/**
 * ユーザ管理
 *
*/
class Folder_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
		Login_model::check();	// check login 
	}
	
	public static function validate($action, &$param)
	{
		switch ($action) {
            case 'update':
			return self::_validate_update($param);
                case 'index':
                    if($param['search_folder_name'] != ''){
                        $s_folder_name = $param['search_folder_name'];
                        $param['s_folder_name'] = strtolower($s_folder_name);
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
			'search_folder_name'
		));
		$model = new Folder_model();
		//$rows = $model->get_donvi_rows($param);		
		return ACWView::template('folder.html', array(
			'data_rows' => null,//$rows,			
			'search_folder_name'=>$param['search_folder_name']
		));
	}

	/**
	 * 編集
	 * @return string
	 */
	public static function action_edit()
	{
		$params = self::get_param(array(
			'donvi_id'
		));
		
		$donvi_id = null;
		if (isset($params['donvi_id'])) {
			$donvi_id = $params['donvi_id'];
		}
		$model = new Donvi_model();
		$data_row = array();
		if ($donvi_id == null) {			
			$data_row['donvi_id'] = null;
			$data_row['donvi_name'] = null;
			$data_row['del_flg'] = null;
		} else {
			$data_row = $model->get_user_row($donvi_id);			
		}
        
		return ACWView::template('donvi/edit.html', array(
			'data_row' => $data_row	
        ));
	}
	
	private static function _validate_update(&$param)
	{
	    /**
	     * 
	     * */	    
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
			$model = new Donvi_model();
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
	
	
	public function get_donvi_rows($param)
	{
		$sql = "
			SELECT				 
				 t.donvi_id
				, t.donvi_name                
                ,t.del_flg
			FROM
				don_vi t
		";
		
		if (isset($param['s_donvi_name'])) {
			$sql_param = array(
					'donvi_name' =>  '%' . SQL_lib::escape_like($param['s_donvi_name']) . '%'
				);
			$sql .= " WHERE lower(t.donvi_name) like lower(:donvi_name) ";
		} else {
			$sql_param = array();
		}
		
		$sql .= "
			ORDER BY
				t.donvi_id
		";		
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		
		$pass_md5 = null;		
		$sql_params = array(
			'donvi_id' => $params['donvi_id'],
			'donvi_name' => $params['donvi_name'],            
            'del_flg' => $params['del_flg'],
            'upd_user_id'=>$login_info['user_id']
		);		
		if ($params['donvi_id'] == null) {		
			
			$res = $this->get_donvi_name_count($params);
			if ($res['cnt'] > 0) {
				ACWError::add('donvi_name', 'Tên đơn vị đã có, vui lòng sử dụng tên khác');
				return;
			}
		
			$sql = "
				INSERT INTO
					don_vi
				(
					  donvi_id
					, donvi_name					
                    , del_flg
					, add_user_id
					, add_datetime
					, upd_user_id
					, upd_datetime
				) VALUES (
					  :donvi_id 
					, :donvi_name 					
                    , :del_flg
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
					don_vi
				SET
					  donvi_id = :donvi_id
					, donvi_name = :donvi_name					
					, del_flg = COALESCE(:del_flg, 0)
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
				WHERE
					donvi_id = :donvi_id
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
	public function get_user_row($donvi_id)
	{
		$rows = $this->query("
				SELECT
					t.*
				FROM
					don_vi t
				WHERE
					t.donvi_id = :donvi_id
			", array("donvi_id" => $donvi_id)
		);
		
		return $rows[0];
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
	
	
}
/* ファイルの終わり */