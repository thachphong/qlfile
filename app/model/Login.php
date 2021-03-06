<?php
/**
 * ログインを行う
*/
class Login_model extends ACWModel
{
	/**
	* 共通初期化
	*/
	public static function init()
	{
	}


	/**
	* ログイン画面表示
	*/
	public static function action_index()
	{  
		return ACWView::template('login.html', array('user_id' => ''));
	}
	
	// ログインエラー画面表示
	public static function action_error()
	{
		return ACWView::template('error.html', array());
	}

	/**
	* ログイン認証
	*/
	public static function action_auth()
	{
		$param = self::get_param(array('user_id', 'passwd','lang'));
		if (self::get_validate_result()) {
			$login = new Login_model();
			$user_info = $login->check_login($param);
			if (is_null($user_info) == false) {
				ACWSession::set('user_info', $user_info);
				ACWSession::set('lang', $param['lang']);
                ACWSession::set('file_download', array());
                if($user_info['upload'] > 0 || $user_info['kiemtra'] > 0 || $user_info['duyet'] > 0 || $user_info['trungtam_quanly'] > 0){
                    return ACWView::redirect(ACW_BASE_URL . 'don');
                }else if($user_info['print'] > 0){
                    return ACWView::redirect(ACW_BASE_URL . 'file/blank');
                }else if($user_info['phanbo'] > 0){  // cap phat
                    return ACWView::redirect(ACW_BASE_URL . 'file/capphat');
                }else if($user_info['admin'] > 0){  
                    return ACWView::redirect(ACW_BASE_URL . 'file/donvi');
                }
				return ACWView::redirect(ACW_BASE_URL . 'don');
			} else {
				ACWError::add('message', 'Tên đăng nhập hoặc mật khẩu không đúng');
			}
		}

		// もう一度表示
		return ACWView::template('login.html', $param);
	}

	/**
	* ログアウト
	*/
	public static function action_delete()
	{
		// セッション削除
		ACWSession::del('user_info');
		// ログイン画面へリダイレクト
		return ACWView::redirect(ACW_BASE_URL . 'login');
	}

	/**
	* 入力チェック
	*/
	public static function validate($action, &$param)
	{
		if ($action == 'auth') {
			if (isset($param['user_id']) == false) {
				ACWError::add('message', 'Vui lòng nhập tên đăng nhập !');
			} else if ($param['passwd'] == false) {
				ACWError::add('message', 'Vui lòng nhập mật khẩu !');
			}
			if (ACWError::count() == 0) {
				return true;	// チェックOK
			}
		}
		return false;
	}

	/**
	* 他モデルからのログインチェック
	*/
	public static function check()
	{
		// セッション取得
		$user_info = ACWSession::get('user_info');
		if (is_null($user_info)) {
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
				if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
					// ajaxリクエストでセッション切れ判明
					header('HTTP/1.0 401 Unauthorized');
					exit();	// 終わり
				}
			}

			ACWView::redirect(ACW_BASE_URL . 'login/error');
			exit();
		}
		if (isset($user_info['user_id'])) {
			// user_idがある事を条件に
			ACWView::init_template_var(array('user_info' => $user_info));
			// ログをユーザーごとに
			ACWLog::set_user_suffix($user_info['user_id']);
			return true;
		}
		return false;
	}


	///////////////////////////////////////////////////////////////////////////
	// 以下DB処理
	///////////////////////////////////////////////////////////////////////////
	/**
	* DBでのログインチェック
	*/
	public function check_login($param)
	{
		$param['passwd'] = md5(AKAGANE_SALT . $param['passwd']);
		$sql_param['user_id'] =$param['user_id'];
		$sql_param['passwd'] =$param['passwd'];		
		$result = $this->query('
			SELECT
                    user_id
				, user_name
                ,user_name_disp
				, PASS
				, donvi
				, phong_ban
				,b.phongban_name
				, to_nhom
				, IP
				, LEVEL
				,l.admin
				,l.upload
                ,l.kiemtra
                ,l.duyet
                ,l.duyet
                ,l.phanbo
                ,l.trungtam_quanly
                ,l.print
                ,email
			FROM
				m_user
			left join phong_ban b on b.phongban_id = m_user.phong_ban
			left join level  l on l.level_id = m_user.level
			WHERE m_user.del_flg = 0
			AND	upper(USER_NAME) = upper(:user_id)
			AND
				PASS = :passwd			
			', $sql_param);
		if (count($result) != 1) {
			return null;
		}else{
            if(strlen($result[0]['IP']) >0 ){  // co nhap ip moi kiem tra
    			if($_SERVER['REMOTE_ADDR'] != $result[0]['IP']){
    				ACWError::add('ip', 'IP đăng ký không đúng !');
    				return null;
    			}		
            }
			return $result[0];
		}
		return null;
	}

}
/* ファイルの終わり */