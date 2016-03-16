<?php

class Ftp_lib {
	
	public function __construct($connect_flg = true)
	{
	}
	
	public function connect_test()
	{
		if (AKAGANE_WEB_FTP_NAME == '') {
			return 'FTPサーバー名が指定されていません。';
		}
		
		try {
			// 接続
			if (AKAGANE_WEB_FTP_PORT == '') {
				$connect = ftp_connect(AKAGANE_WEB_FTP_NAME);
			} else {
				$connect = ftp_connect(AKAGANE_WEB_FTP_NAME, AKAGANE_WEB_FTP_PORT);
			}
			
			if ($connect === false) {
				return 'FTPサーバーへの接続に失敗しました。FTPサーバーのアドレスはまたはポート番号が違う可能性があります。';
			}
			
			// ログイン
			$login = ftp_login($connect, AKAGANE_WEB_FTP_ID, AKAGANE_WEB_FTP_PASS);
			if ($login === false) {
				return 'FTPサーバーへの接続に失敗しました。IDまたはパスワードが違う可能性があります。';
			}
			
			ftp_close($connect);
			
		} catch (Exception $ex) {
			ACWLog::debug($ex->getMessage());
			return 'FTPサーバーへのアップロード中にエラーが発生しました。' . $ex->getMessage();
		}
		
		return '接続成功';
	}
	
	/**
	 * 圧縮ファイルをFTPにアップロード
	 */
	public function upload($path, $filename)
	{
		if (AKAGANE_WEB_FTP_NAME == '') {
			return 'FTPサーバー名が指定されていません。';
		}
		
		try {
			// 接続
			if (AKAGANE_WEB_FTP_PORT == '') {
				$connect = ftp_connect(AKAGANE_WEB_FTP_NAME);
			} else {
				$connect = ftp_connect(AKAGANE_WEB_FTP_NAME, AKAGANE_WEB_FTP_PORT);
			}
			
			if ($connect === false) {
				return 'FTPサーバーへの接続に失敗しました。FTPサーバーのアドレスはまたはポート番号が違う可能性があります。';
			}
			
			// ログイン
			$login = ftp_login($connect, AKAGANE_WEB_FTP_ID, AKAGANE_WEB_FTP_PASS);
			if ($login === false) {
				return 'FTPサーバーへの接続に失敗しました。IDまたはパスワードが違う可能性があります。';
			}
			
//			// パッシブモードをオンにする
//			$pasv = ftp_pasv($connect, true);
//			if ($pasv === false) {
//				return 'FTPサーバーへの接続に失敗しました。パッシブモードをオンにできません。';
//			}
			
			// アップロード
			$upload = ftp_put($connect, $filename, $path, AKAGANE_WEB_FTP_MODE);
			if ($upload === false) {
				return 'FTPサーバーへのアップロード中に失敗しました。';
			}
			
			ftp_close($connect);
			
		} catch (Exception $ex) {
			ACWLog::debug($ex->getMessage());
			return 'FTPサーバーへのアップロード中にエラーが発生しました。' . $ex->getMessage();
		}
		
		return true;
	}
}
