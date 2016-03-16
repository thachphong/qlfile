<?php

/**
 * アップロードファイル操作ライブラリ
 */
class Upload_lib
{
	private $uploadfile;	// アップロードされたファイル
	private $ext;			// ファイル情報
	private $filename;		// ファイル名拡張子なし

	/**
	 * コンストラクタ
	 */
	public function __construct($uploadfile)
	{
		$this->uploadfile = $uploadfile;
		/*if (ACW_WINDOWS) {
			// ファイルコード変換
			$this->uploadfile['name'] = mb_convert_encoding($this->uploadfile['name'], 'UTF-8', 'SJIS-win');
		}*/
		$ps = Path_lib::info($this->uploadfile['name']);
		if (isset($ps['extension'])) {
			$this->ext = strtolower($ps['extension']);
		} else {
			$this->ext = '';
		}
		if (isset($ps['filename'])) {
			$this->filename = $ps['filename'];
		} else {
			$this->filename = '';
		}
	}

	/*
	 * エラーかどうか
	 * UPLOAD_ERR_OK
		値: 0; エラーはなく、ファイルアップロードは成功しています。
		UPLOAD_ERR_INI_SIZE
		値: 1; アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。
		UPLOAD_ERR_FORM_SIZE
		値: 2; アップロードされたファイルは、HTML フォームで指定された MAX_FILE_SIZE を超えています。
		UPLOAD_ERR_PARTIAL
		値: 3; アップロードされたファイルは一部のみしかアップロードされていません。
		UPLOAD_ERR_NO_FILE
		値: 4; ファイルはアップロードされませんでした。
		UPLOAD_ERR_NO_TMP_DIR
		値: 6; テンポラリフォルダがありません。PHP 4.3.10 と PHP 5.0.3 で導入されました。
		UPLOAD_ERR_CANT_WRITE
		値: 7; ディスクへの書き込みに失敗しました。PHP 5.1.0 で導入されました。
		UPLOAD_ERR_EXTENSION
		値: 8; PHP の拡張モジ
	 */
	public function get_error()
	{
		return $this->uploadfile['error'];
	}

	/*
	 * ファイル名
	 */
	public function get_name()
	{
		return $this->uploadfile['name'];
	}

	/*
	 * 拡張子なしのファイル名
	 */
	public function get_filename()
	{
		return $this->filename;
	}

	/*
	 * 拡張子
	 */
	public function get_ext()
	{
		return $this->ext;
	}
	
	/*
	 * ファイルの移動
	 */
	public function move_to($to_path)
	{
		return move_uploaded_file($this->uploadfile['tmp_name'], $to_path);
	}
}

/* ファイルの終わり */