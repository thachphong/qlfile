<?php
/**
 * May_model
 *
*/
class May_model extends ACWModel
{	
	public static function init()
	{
		Login_model::check();	
	}
	
	/**
	* action_index
	*/
	public static function action_index()
	{
		$param = self::get_param(array(
			's_may_name',
            's_may_no'
		));
		$model = new May_model();
		$rows = $model->get_may_rows($param);		
		return ACWView::template('may.html', array(
			'data_rows' => $rows,			
			'data_search'=>$param
		));
	}

	/**
	 * 編集
	 * @return string
	 */
	public static function action_edit()
	{
		$params = self::get_param(array(
			'may_id'
		));
		
		$may_id = null;
		if (isset($params['may_id'])) {
			$may_id = $params['may_id'];
		}
		$model = new May_model();
		$data_row = array();
		if ($may_id == null) {			
			$data_row['may_id'] = null;
			$data_row['may_no'] = null;
            $data_row['may_name'] = null;
			$data_row['del_flg'] = null;
		} else {
			$data_row = $model->get_may_row($may_id);			
		}
        
		return ACWView::template('may/edit.html', array(
			'data_row' => $data_row	
        ));
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
			'may_id',
			'may_no',
            'may_name',
            'del_flg'
		));
		
		//if (self::get_validate_result() === true) {
			$model = new May_model();
			$obj = $model->update($params);
		//}
		
		if (ACWError::count() <= 0) {
		    $result['status'] = 'OK';
		} else {
			$result['status'] = 'NG';
			$result['error'] = ACWError::get_list();
		}

		return ACWView::json($result);
	}
	
	
	public function get_may_rows($param)
	{
		$sql = "
			select * from may
            where 1=1
		";
		$sql_param = array();
		if (isset($param['may_no'])) {
			$sql_param['may_no'] = $param['may_no'];
			$sql .= " WHERE lower(may_no) like lower(:may_no) ";
		}else if (isset($param['may_name'])) {
			$sql_param['may_name'] = $param['may_name'];
			$sql .= " WHERE lower(may_name) like lower(:may_name) ";
		} 
		
		$sql .= "	ORDER BY may_id		";		
		return $this->query($sql, $sql_param);
	}
		
	public function update($params)
	{
		$this->begin_transaction();
		
		$login_info = ACWSession::get('user_info');
		
		$pass_md5 = null;		
		$sql_params = array(
			'may_id' => $params['may_id'],
			'may_no' => $params['may_no'],    
            'may_name' => $params['may_name'],           
            'del_flg' => $params['del_flg'],
            'add_user_id'=>$login_info['user_id']
		);		
		if ($params['may_id'] == null) {		
			
			$res = $this->get_may_no_count($params);
			if ($res['cnt'] > 0) {
				ACWError::add('may_no', 'Mã máy đã có, Vui lòng sử dụng mã khác !');
				return;
			}
		
			$sql = "
				INSERT INTO
					may
				(
					  may_id
                    ,may_no
					, may_name					
                    , del_flg
					, add_user_id
					, add_datetime
                    , upd_datetime
                    , upd_user_id
				) VALUES (
					  :may_id 
					, :may_no 
                    , :may_name					
                    , :del_flg
					, :add_user_id 
					, NOW() 
                    , :add_user_id 
                    , NOW()
				);
			";
			//$sql_params['add_user_id'] = $login_info['user_id'];
            $this->execute($sql, $sql_params);	
		} else {							
			
			$sql = "
				UPDATE
					may
				SET
					, may_name = :may_name					
					, del_flg = COALESCE(:del_flg, 0)
					, upd_user_id = :upd_user_id
					, upd_datetime = NOW()
				WHERE	may_id = :may_id
			";
			$sql_upd['may_name'] = $params['may_name'];
			$sql_upd['may_id'] = $params['may_id'];
            $sql_upd['del_flg'] = $params['del_flg'];
            $sql_upd['upd_user_id'] = $login_info['user_id'];
            $this->execute($sql, $sql_upd);	
		}
		
		$this->commit();
		
		return true;
	}
	
	/**
	 * ユーザ取得
	 * @param integer $m_user_id ユーザID
	 * @return array
	 */
	public function get_may_row($may_id)
	{
		$rows = $this->query("
				select * from may
                where may_id = :may_id
			", array("may_id" => $may_id)
		);
		
		return $rows[0];
	}
	
	private function get_may_no_count($param)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				may
			WHERE
				may_no = :may_no
		";
		
		$filter = ACWArray::filter($param, array('may_no'));
		$rows = $this->query($sql, $filter);
		return $rows[0];
	}
	
	
}
