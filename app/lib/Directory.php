<?php

/**
 * ディレクトリ操作ライブラリ
 */
class Directory_lib
{

	/**
	 * ディレクトリ削除(ファイル込)
	 */
	function rm($dir)
	{
		if ($handle = @opendir($dir)) {
			while (false !== ($item = readdir($handle))) {
				if ($item != '.' && $item != '..') {
					if (is_dir($dir . '/' . $item)) {
						// 再帰
						$this->rm($dir . '/' . $item);
					} else {
						@unlink($dir . '/' . $item);
					}
				}
			}
			@closedir($handle);
			@rmdir($dir);
		}
	}

	/**
	 * ディレクトリコピー(ファイル込)
	 * $source_dir コピー元, $target_dir コピー先
	 */
	function cp($source_dir, $target_dir)
	{
		$handle = @opendir($source_dir);
		if ($handle) {
			while ($filename = readdir($handle)) {
				if (strcmp($filename, '.') != 0 && strcmp($filename, '..') != 0) {
					$old_name = $source_dir . '/' . $filename;
					$new_name = $target_dir . '/' . $filename;
					if (is_dir($old_name)) {
						if (!empty($filename) && !file_exists($new_name)) {
							@mkdir($new_name);
						}
						$this->cp($old_name, $new_name);
					} else {
						if (file_exists($new_name)) {
							// あるので消す
							@unlink($new_name);
						}
						@copy($old_name, $new_name);
					}
				}
			}
			@closedir($handle);
		}
	}

	/**
	 * 指定ディレクトリのファイルリストを取得
	 */
	function find_files($target_dir)
	{
		//対象ディレクトリ
		$files = scandir($target_dir);
		foreach ($files as $key => $file) {
			if ($file == '.' || $file == '..') {
				unset($files[$key]);
			}
		}
		return $files;
	}

	/*
	 * ************************************************************************************************
	 * 静的なメソッド
	 * こっち優先
	 *
	 */

	/*
	 * 安全な削除
	 */
	public static function safe_remove($target)
	{
		if (file_exists($target) == false) {
			return;
		}

		if (is_dir($target) == false) {
			// ディレクトリではない
			@unlink($target);
			return;
		}

		self::_remove_dir($target);
	}

	private static function _remove_dir($target)
	{
		$list = self::get_file_list($target);
		foreach ($list as $filename) {
			$path = $target . '/' . $filename;
			if (is_dir($path)) {
				// 再帰
				self::_remove_dir($path);
			} else {
				@unlink($path);
			}
		}
	}

	/*
	 * 安全なコピー
	 */
	public static function safe_copy($source_dir, $target_dir)
	{
		if (file_exists($source_dir) == false) {
			throw new Exception('[safe_copy]コピー元が存在しません。' . $source_dir);
		}

		if (file_exists($target_dir) == false) {
			@mkdir($target_dir);
		}

		self::_copy($source_dir, $target_dir);
	}

	private static function _copy($source_dir, $target_dir)
	{
		$list = self::get_file_list($source_dir);
		foreach ($list as $filename) {
			$old_name = $source_dir . '/' . $filename;
			$new_name = $target_dir . '/' . $filename;
			if (is_dir($old_name)) {
				// ディレクトリ
				if (file_exists($new_name) == false) {
					// ないので作る
					@mkdir($new_name);
				}
				self::_copy($old_name, $new_name);
			} else {
				if (file_exists($new_name)) {
					// あるので消す
					@unlink($new_name);
				}
				@copy($old_name, $new_name);
			}
		}
	}
	
	//Add Start NBKD-85 Minh Vnit 2014/11/06
	public static function copy_file_of_series($source,$target){
		if (file_exists($target)) {
			// あるので消す
			@unlink($target);
		}
		if(file_exists($source)){
			@copy($source,$target);
		}
		
		//Add Start NBKD-911 Minh VNit
		$_file = new File_lib();
		$source_file = $_file->GetBaseName($source);
		$target_file = $_file->GetBaseName($target);
		
		$source_file_html = $source_file.".html";
		$target_file_html = $target_file.".html";
		
		$source_array = explode("/", $source);
		if(count($source_array) >0){
			$source_array[count($source_array) -1 ] = $source_file_html;
			$source = implode("/", $source_array);
		}
		
		$target_array = explode("/", $target);
		if(count($target_array) >0){
			$target_array[count($target_array) -1 ] = $target_file_html;
			$target = implode("/", $target_array);
		}
		//Add End NBKD-911 Minh VNit
		
		if (file_exists($target)) {
			// あるので消す
			@unlink($target);
		}
		if(file_exists($source)){
			@copy($source,$target);
		}
	}
	//Add End NBKD-85 Minh Vnit 2014/11/06

	/*
	 * ファイル名一覧
	 */
	public static function get_file_list($source_dir)
	{
		$file_list = array();
		$handle = @opendir($source_dir);
		if ($handle) {
			while ($filename = readdir($handle)) {
				if (strcmp($filename, '.') != 0 && strcmp($filename, '..') != 0) {
					$file_list[] = $filename;
				}
			}
			@closedir($handle);
		}
		return $file_list;
	}
}

/* ファイルの終わり */