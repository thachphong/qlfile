<?php
/**
 * Import Export 処理 共通
 *
 */
class ImportExport_lib extends Excel_lib
{
	/**
	 * マスタ等の取得結果から、対象の値を持ったデータを探して、キー値を返す
	 * @param array  $mst_rows 対象のマスタ等
	 * @param string $value    検索値
	 * @param string $col_name 検索項目
	 * @return キー値、見つからない場合 false
	 */
	protected function get_data_key($mst_rows, $search_value, $col_name) {
		foreach ($mst_rows as $key => $row) {
			if ($search_value == $row[$col_name]) {
				return $key;
			}
		}
		return false;
	}

	// -----------------------------------------------------------------------
	// XML(配列) 取得関連
	// -----------------------------------------------------------------------

	/**
	 * 多次元配列の有無チェックと、値取得
	 * @param 可変配列、１つ目には xml(配列) を指定、２つ以降は配列のキー（１次元～）
	 * @return 取得結果、途中で存在しないキーアクセスがあった場合、null を返す。結果が配列の場合も null を返す
	 */
	public function xml_get_value() {
		$list = func_get_args();
		if (count($list) < 1) return null;

		$xml = $list[0];
		unset($list[0]);

		foreach ($list as $key) {
			if (isset($xml[$key]) === false) {
				return null;
			}
			$xml = $xml[$key];
		}

		if (is_array($xml) && count($xml) == 0) {
			return null;
		}
		return $xml;
	}

	/**
	 * 多次元配列の有無チェックと、配列取得
	 * もし取得位置の配列が通常配列ではないなら、[0] に格納して返す
	 * @param  可変引数、１つ目には xml(配列) を指定、２つ以降は配列のキー（１次元～）
	 * @return 取得結果、途中で存在しないキーアクセスがあった場合、空配列 を返す、取得値が連想配列や配列でない場合、[0]に格納した値
	 */
	public function xml_get_array() {
		$list = func_get_args();              // 引数の取得

		if (count($list) < 1) return array(); // パラメータなしのときも、、、空配列を返す

		$xml = $list[0]; // 先頭は対象の xml なので、別途取得して
		unset($list[0]); // xml を引数から除去（下記ループのため）

		foreach ($list as $key) {
			if (isset($xml[$key]) === false) {
				return array();         // サーチ対象が無いなら、空配列を返す
			}
			$xml = $xml[$key];          // 見つかったキーを xml の先頭にして、繰り返す
		}

		if (is_array($xml) === false) { // 配列ではないなら、無理やり [0] 形式にして返す
			return array($xml);
		}

		if (count($xml) == 0) {
			return $xml;                // ０件配列なら、そのまま返す（ありえるのか？）
		}

		if (isset($xml[0]) === false) { // [0] が無いなら、連想配列とみなす
			return array($xml);         // ので、[0]['key'] 形式にして返す
		}

		return $xml;                    // でないなら、配列を返す
	}


	// -----------------------------------------------------------------------
	// Excel 操作関連
	// -----------------------------------------------------------------------


	/** 現在選択中のシート */
	protected $sheet = null;

	/**
	 * ファイルを開く（編集・参照用途）
	 * @param string $file_name    対象ファイル名
	 * @param string $file_type    ファイル形式 'Excel5'  'Excel2007' など
	 * @return 失敗したら false、成功したら true
	 */
	public function load($file_name)
	{
		try {
			if(!isset($this->xl))
			{
				$this->_load($file_name);
			}
			$this->sheet = $this->set_sheet(0); 
		} catch (Exception $e) {
			ACWLog::write_file('EXCELERROR', $e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * 現在の編集をファイルに保存する
	 * @param unknown $file_name 保存するファイル名
	 * @param string $file_type    ファイル形式 'Excel5'  'Excel2007' など
	 * @return 失敗したら false、成功したら true
	 */
	public function save($file_name, $file_type = 'Excel2007') {
		try {
			$writer = PHPExcel_IOFactory::createWriter($this->xl, $file_type); // テンプレを編集したオブジェクトを、指定形式で保存するオブジェクトを生成
			$writer->save($file_name);                                         // 指定ファイル名で、ファイルに保存
		} catch (Exception $e) {
			ACWLog::write_file('EXCELERROR', $e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * 解放する
	 */
	public function free()
	{
		unset($this->sheet);
		$this->sheet = null;
		$this->_free(); // メモリ解放
	}

	/** 編集対象のシートを選択する（0からの連番で指定） */
	public function set_sheet($index) {
		$this->sheet = $this->xl->getSheet($index); return  $this->sheet;
	}

	/** 編集対象に選択中のシートオブジェクトを取得 */
	public function get_sheet() {
		return $this->sheet;
	}

	/** シートを削除する（0からの連番で指定） */
	public function RemoveSheet($index) {
		$this->xl->removeSheetByIndex($index);
	}

	/** 編集中の EXCEL オブジェクトを取得 */
	public function get_excel() {
		return $this->xl;
	}

	/**
	 * 現在のシートのタイトル（シート名）を設定する
	 * @param string $sheet_name シート名
	 */
	public function set_title($sheet_name) {
		$this->sheet->setTitle($sheet_name);
	}

	/** 現在のシートのタイトル（シート名）を取得する */
	public function get_title() {
		return $this->sheet->getTitle();
	}


	/**
	 * 指定セルのオブジェクトを取得する
	 * @param string $cell  対象セルの位置文字列（'A1'等）
	 * @return 取得したセルオブジェクト
	 */
	public function get_cell($cell) {
		return $this->sheet->getCell($cell);
	}

	/**
	 * 指定セルのオブジェクトを取得する
	 * @param int $col    対象セルの列番号（0からの値）
	 * @param int $row    対象セルの行番号（1からの値）
	 * @return 取得したセルオブジェクト
	 */
	public function get_cell_no($col, $row) {
		return $this->sheet->getCellByColumnAndRow($col, $row);
	}

	/**
	 * 指定セルのスタイルオブジェクトを取得する
	 * @param string $cell  対象セルの位置文字列（'A1'等）
	 * @return 取得したスタイルオブジェクト
	 */
	public function get_cell_style($cell) {
		return $this->sheet->getStyle($cell);
	}

    //Add Start - LIXD-10 - TrungVNIT - 2015/06/26
    public function get_total_column($num = TRUE) {
        if($num == FALSE){
            $col = $this->sheet->getHighestDataColumn(); 
        }else{
            $col = PHPExcel_Cell::columnIndexFromString($this->sheet->getHighestDataColumn()); 
        }
		return $col;
	}
    
    public function get_total_row() {
		return $this->sheet->getHighestDataRow();
	}
    
    public function merge_cells($fcell, $tcell){
        $this->sheet->mergeCells($fcell . ':' . $tcell);
        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $this->sheet->getStyle($fcell . ':' . $tcell)->applyFromArray($style);
    }
    
    public function arrayToExcel($data, $autoSize = FALSE){
        $this->xl = new PHPExcel();
        $this->xl->getActiveSheet()->fromArray($data, null, 'A1');
        if($autoSize == TRUE){
            $this->xl->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $this->xl->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        }
 
    }
    
    public function applyStyleDefault($col, $row, $styleArray){
        $this->get_cell_style_no($col, $row)->applyFromArray($styleArray);
    }
    
    public function get_position_cell_no($col, $row) {
    	//row tính từ 1, col tính từ 0
		$cell = $this->sheet->getCellByColumnAndRow($col, $row);
		$position = $cell->getCoordinate(); 
		return $position;
	}
   
	/**
	 * 指定セルのスタイルオブジェクトを取得する
	 * @param int $col   対象セルの列番号（0からの値）
	 * @param int $row   対象セルの行番号（1からの値）
	 * @return 取得したスタイルオブジェクト
	 */
	public function get_cell_style_no($col, $row) {
		return $this->sheet->getStyleByColumnAndRow($col, $row);
	}

	/**
	 * 指定セルに値を設定する
	 * @param string $cell  対象セルの位置文字列（'A1'等）
	 * @param string $value 設定する値
	 */
	public function set_value($cell, $value) {
		$this->get_cell($cell)->setValue($value);
	}

	/**
	 * 指定セルに値を設定する
	 * @param int $col   対象セルの列番号（0からの値）
	 * @param int $row   対象セルの行番号（1からの値）
	 * @param string $value 設定する値
	 */
	public function set_value_no($col, $row, $value, $border = FALSE) {
		$this->get_cell_no($col, $row)->setValue($value);
        if($border == TRUE){
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $this->get_cell_style_no($col, $row)->applyFromArray($styleArray);
        }        
	}
        
       
        public function set_background_color($col, $row){
            $bg = array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'rgb' => 'FFFFCC',
                    ),
                ),
            );
            $this->get_cell_style_no($col, $row)->applyFromArray($bg);
        }
       
        /**
	 * 指定セルから値を取得する
	 * @param string $cell  対象セルの位置文字列（'A1'等）
	 * @return 取得した値
	 */
	public function get_value($cell) {
		return $this->get_text($this->get_cell($cell)->getValue());
	}
	
	/**
	 *	Get calculated cell value
	 *
	 *	@param	string $cell
	 *	@return	value after execute fomular
	 *	@throws	PHPExcel_Exception
	 */
	public function get_calculated_value($cell) {
		return $this->get_text($this->get_cell($cell)->getCalculatedValue());
	}
	/**
	 * Is a given cell a date/time?
	 *
	 * @param	 string $cell
	 * @return	 boolean
	 */
	public function is_date_time($cell) {
		return PHPExcel_Shared_Date::isDateTime($this->get_cell($cell));
	}
	/**
	 * Get format code of cell
	 *
	 * @param string $cell
	 * @return string format code of cell
	 */
	public function get_format_code($cell) {
		return PHPExcel_Shared_Date::getFormatCode($this->get_cell($cell));
	}
	/**
	 * Get type code of cell
	 *
	 * @param string $cell
	 * @return string type of cell
	 */
	public function get_type($cell) {
		return $this->get_cell($cell)->getDataType();
	}
	//Add end LIXD-155 TinVNIT 9/17/2015
	/**
	 * 指定セルから値を取得する
	 * @param int $col   対象セルの列番号（0からの値）
	 * @param int $row   対象セルの行番号（1からの値）
	 * @return 取得した値
	 */
	public function get_value_no($col, $row) {
		return $this->get_text($this->get_cell_no($col, $row)->getValue());
	}

	public function get_text($valueCell = null) {
		if (is_null($valueCell)) {
			return '';
		}
		$txt = '';

		if (is_object($valueCell)) {                      // オブジェクトなら、リッチテキスト要素を取得
			$rtfCell = $valueCell->getRichTextElements(); // 配列で返ってくるので、そこからさらに文字列を抽出
			$txtParts = array();
			foreach ($rtfCell as $v) {
				$txtParts[] = $v->getText();
			}
			$txt = implode('', $txtParts); // 連結する
		} else {
			//if (!empty($valueCell)) {
				$txt = $valueCell;
			//}
		}

		return $txt;
	}

	/**
	 * セルの列位置文字(A , B , AA など) から、 0～の数値に変換する
	 * @param string $cell_str  セルの列文字列('A' など)
	 * @return int ※0からの値( A = 0, B = 0 ...)
	 */
	public static function str_to_index($cell_str) {
		return PHPExcel_Cell::columnIndexFromString($cell_str) - 1;
	}

	/**
	 * 0～の数値から、セルの列位置文字に変換する
	 * @param int $cell_num  0からの値( A = 0, B = 1 ...)
	 * @return string  セル列文字列('A' など)
	 */
	public static function index_to_str($cell_num) {
		return PHPExcel_Cell::stringFromColumnIndex($cell_num);
	}

	// -----------------------------------------------------------------------
	// 指定フォルダへのログ出力
	// -----------------------------------------------------------------------

	/**
	 * 個別のログ出力(特定フォルダに出力したいので、別途作成した)
	 * @param string ログ出力先
	 * @param int    ログ出力レベル syslog 関数のパラメータ参照
	 * @param string メッセージ
	 */
	public function write_log($log_path, $lv, $msg)
	{
		$lvmsg = $lv;

		switch ($lv) {
		default:
		case LOG_INFO:    $lvmsg = '[情報]'; break;
		case LOG_NOTICE:  $lvmsg = '[通知]'; break;
		case LOG_WARNING: $lvmsg = '[警告]'; break;
		case LOG_ERR:     $lvmsg = '[失敗]'; break;
		}

		if (($fp = @fopen($log_path, "a")) === false) {
			return;
		}

		if (!flock($fp, LOCK_EX)) {
			@fclose($fp);
			return;
		}

		if (fwrite($fp, date('Y/m/d H:i:s') . "\t" . $lvmsg . "\t" . $msg . "\n") === false) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!fflush($fp)) {
			@flock($fp, LOCK_UN);
			@fclose($fp);
			return;
		}

		if (!flock($fp, LOCK_UN)) {
			@fclose($fp);
			return;
		}

		if (!fclose($fp)) {
			return;
		}
ACWLog::debug($msg);
	}
}

/* ファイルの終わり */