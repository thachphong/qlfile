<?php
require_once ACW_VENDOR_DIR . '/phpexcel/PHPExcel.php';
require_once ACW_VENDOR_DIR . '/phpexcel/PHPExcel/IOFactory.php';


/**
 * Excel操作ライブラリ
*/
class Excel_lib
{
	/** PHPExcel */
	protected $xl = null;

	/** エラーメッセージ */
	protected $error_message;

	/**
	 * エラーメッセージ
	*/
	public function get_error()
	{
		return $this->error_message;
	}

	/**

	*/
	public function to_array($excel_file)
	{
		if ($this->_load($excel_file) === false) {
			return null;
		}

		$result = array();

		try {
			$sheet = $this->xl->setActiveSheetIndex(0);
			//行でループ
			foreach ($sheet->getRowIterator() as $row) {
				$data = array();
				//列でループ
				foreach ($row->getCellIterator() as $cell) {
					if (is_null($cell)) {
						$data[] = null;
					} else {
						$data[] = $cell->getValue();
					}
				}
				$result[] = $data;
			}
		} catch (Exception $e) {
			$this->error_message = $e->getMessage();
			ACWLog::write_file('EXCELERROR', $e->getMessage());
			$result = null;
		}

		$this->_free(); // メモリ解放
		return $result;
	}

	/*
	 * PHPExcel版
	 *
	 */
	public function to_html($from_file, $to_file, $not_free = FALSE)
	{
		//Edit start LIXD-284 Tin VNIT 10/29/2015
		if(!isset($this->xl))
		{
			if ($this->_load($from_file) === false) {
				return false;
			}
		}
		//Edit end LIXD-284 Tin VNIT 10/29/2015
		$result = false;

		try {
			$this->_save_to_html($to_file);
			$result = true;
		} catch (Exception $e) {
			ACWLog::write_file('EXCELERROR', $e->getMessage());
		}
        if($not_free == FALSE)
		    $this->_free(); // メモリ解放

		return $result;
	}

	/**
	 * 読み込み
	*/
	protected function _load($from_file)
	{
		//$reader = PHPExcel_IOFactory::createReader(Excel_lib::EXCEL2003);
		//Edit start LIXD-284 Tin VNIT 10/29/2015
		//$this->xl = PHPExcel_IOFactory::load($from_file);
		
		/**  Create a new Reader of the type defined in $inputFileType  **/ 
		$objReader = PHPExcel_IOFactory::createReaderForFile($from_file); 
		/**  Read the list of worksheet names and select the one that we want to load  **/
		$worksheetList = $objReader->listWorksheetNames($from_file);
		$sheetname = $worksheetList[0]; 

		/**  Advise the Reader of which WorkSheets we want to load  **/ 
		$objReader->setLoadSheetsOnly($sheetname); 
		/**  Load $inputFileName to a PHPExcel Object  **/ 
		$this->xl = $objReader->load($from_file); 
		//Edit end LIXD-284 Tin VNIT 10/29/2015
	}
	//Add start LIXD-284 Tin VNIT 10/29/2015
	protected function _load_data_only($from_file)
	{
		//$reader = PHPExcel_IOFactory::createReader(Excel_lib::EXCEL2003);
		//Edit start LIXD-284 Tin VNIT 10/29/2015
		//$this->xl = PHPExcel_IOFactory::load($from_file);
		
		/**  Create a new Reader of the type defined in $inputFileType  **/ 
		$objReader = PHPExcel_IOFactory::createReaderForFile($from_file); 
		/**  Read the list of worksheet names and select the one that we want to load  **/
		$worksheetList = $objReader->listWorksheetNames($from_file);
		$sheetname = $worksheetList[0]; 

		/**  Advise the Reader of which WorkSheets we want to load  **/ 
		$objReader->setLoadSheetsOnly($sheetname); 
		$objReader->setReadDataOnly(true);
		/**  Load $inputFileName to a PHPExcel Object  **/ 
		$this->xl = $objReader->load($from_file); 
		//Edit end LIXD-284 Tin VNIT 10/29/2015
	}
	//Add end LIXD-284 Tin VNIT 10/29/2015
	/**
	* html として保存
	*/
	protected function _save_to_html($to_file)
	{
		$writer = PHPExcel_IOFactory::createWriter($this->xl, 'html');
		$writer->setSheetIndex(0);
		$writer->save($to_file);
	}

	/**
	* メモリを解放する
	*/
	protected function _free()
	{
		if (isset($this->xl)) {
			try {
				// メモリ解放
				$this->xl->disconnectWorksheets();
				unset($this->xl);
				$this->xl = null;
			} catch (Exception $e) {
				ACWLog::write_file('EXCELERROR', $e->getMessage());
			}
		}
	}

	/*
	 * スタイルを範囲で獲得する
	 * colは0から
	 * rowは1から
	 */
	function get_range_style($sheet, $col_from, $row_from, $col_to, $row_to)
	{
		$v_range = PHPExcel_Cell::stringFromColumnIndex($col_from) . $row_from . ":" .
				   PHPExcel_Cell::stringFromColumnIndex($col_to) . $row_to ;

		return $sheet->getStyle($v_range);
	}
    //Add Start LIXD-287 hungtn VNIT 20151106
    public static function isOneSheet($inputFileName) {
        
        $inputFileType = 'Excel2007'; 
        //$inputFileName = './sampleData/example1.xls'; 

        /**  Create a new Reader of the type defined in $inputFileType  **/ 
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        /**  Read the list of worksheet names and select the one that we want to load  **/
        $worksheetList = $objReader->listWorksheetNames($inputFileName);
        $count_worksheet = count($worksheetList);
        
        unset($objReader);
        
        if($count_worksheet == 1) {
            return true;
        } else {
            return false;
        }
         
    }
    //Add Start LIXD-287 hungtn VNIT 20151106
}
/* ファイルの終わり */