<?php

/**
 * 画像操作ライブラリ
 * ImageMagicをインストールしないとエラーとなる
 * Ghostscript 9.10がないとエラーとなる
 */
class Image_lib
{
	/**
	 * コンストラクタ
	 */
	/* 	public function __construct()
	  {
	  } */

	/**
	 * ImageMagicによるサムネイル作成　今は小さくしない
	 * @param string $from_file 読み込み元のファイルフルパス
	 * @param string $to_file 書き込み先のファイルフルパス
	 * @param integer $width 幅最大
	 * @param integer $height 高さ最大
	 * @param string $formatt 出力ファイルフォーマット jpg/png
	 * @return bool 正常or異常
	 */
	public function thumb($from_file, $to_file, $width, $height, $format)
	{
		try {
			$im = new Imagick();
			// 解像度300dpiで開く
			// $im->setresolution(300, 300);
			// 解像度160dpiで開く
			$im->setresolution(320, 320);
//			$im->setresolution(160, 160);
			$im->readimage($from_file);

			$im->setResourceLimit(6, 1); // 1スレッドに制限
			$im->setIteratorIndex(0); // 画像リストの1番目
			$im->setImageFormat($format); // jpgに変換
			$im->setBackgroundColor('white'); // 背景色
			// CMYK→RGB 色変更
			$this->_cmyk_to_rgb($im);

			$w = $im->getImageWidth();
			$h = $im->getImageHeight();

			// 横が760px以上は、760pxへ変換
			// 変換を廃止
//			if ($w >= $width) {
//				$w = $width;
//			}

			// 横px数に合わせる。bestfitは使わない
			$im->thumbnailImage($w*(0.5), 0, false); // 3番目はbestfit
//			$im->thumbnailImage($w, 0, false); // 3番目はbestfit

//			// 解像度を埋め込み 72dpi
			$im->setImageResolution(72,72);

//			$im->thumbnailImage($width, $height, true); // 3番目はbestfit
			// bestfitは使わない
//			$im->thumbnailImage($w, 0, false); // 3番目はbestfit
			
			// 72→300dpi
//			$w = $im->getImageWidth();
//			$h = $im->getImageHeight();
//			$scale = 300 / 72;
//			$w *= $scale;
//			$h *= $scale;
			// 大きさ変更
//			$im->thumbnailImage($w, $h);
			
			$im->writeImage($to_file . '.' . $format); // ファイルへ
			$im->destroy(); // 破棄
		} catch (Exception $e) {
			ACWLog::write_file('IMAGEERROR', $e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * ImageMagicによるサムネイル作成
	 * thumbが違う処理になったので別処理を作成
	 * @param string $from_file 読み込み元のファイルフルパス
	 * @param string $to_file 書き込み先のファイルフルパス
	 * @param integer $width 幅最大
	 * @param integer $height 高さ最大
	 * @return bool 正常or異常
	 */
	public function create_thumbnail($from_file, $to_file, $width, $height)
	{
		try {
			// 出力ファイルの拡張子に従う
			$pinfo = Path_lib::info($to_file);
			$ext = $pinfo['extension'];

			$im = new Imagick();
			$im->readimage($from_file);

			$im->setResourceLimit(6, 1); // 1スレッドに制限
			$im->setIteratorIndex(0); // 画像リストの1番目
			$im->setImageFormat($ext); // 変換
			$im->setBackgroundColor('white'); // 背景色
			// CMYK→RGB 色変更
			$this->_cmyk_to_rgb($im);

			// 解像度変更テスト 72dpi
			$im->setImageResolution(72,72);
			$w = $im->getImageWidth();
			$h = $im->getImageHeight();
			if ($w > $width && $h > $height) {
				// 元より小さくなる場合
				$im->thumbnailImage($width, $height, true); // 3番目はbestfit
			}
			
			$im->writeImage($to_file); // ファイルへ
			$im->destroy(); // 破棄
		} catch (Exception $e) {
			ACWLog::write_file('IMAGEERROR', $e->getMessage());
			return false;
		}
		return true;
	}

	private function _cmyk_to_rgb($img)
	{
		// don't use this (it inverts the image)
		//    $img->setImageColorspace (imagick::COLORSPACE_RGB);

		if ($img->getImageColorspace() != Imagick::COLORSPACE_CMYK) {
			return;
		}
		$profiles = $img->getImageProfiles('*', false);
		// we're only interested if ICC profile(s) exist
		$has_icc_profile = (array_search('icc', $profiles) !== false);
		// if it doesnt have a CMYK ICC profile, we add one
		if ($has_icc_profile === false) {
			$icc_cmyk = file_get_contents(ACW_VENDOR_DIR . '/color_profile/JapanWebCoated.icc');
			$img->profileImage('icc', $icc_cmyk);
			unset($icc_cmyk);
		}
		// then we add an RGB profile
		$icc_rgb = file_get_contents(ACW_VENDOR_DIR . '/color_profile/sRGB_v4_ICC_preference.icc');
		$img->profileImage('icc', $icc_rgb);
		unset($icc_rgb);

		$img->stripImage(); // this will drop down the size of the image dramatically (removes all profiles)
	}

    //Add Start - LIXD-321 - TrungVNIT - 2015/09/11
    public static function imagick_supported_formats(){
        $imagick = new Imagick();
        $list = $imagick->queryformats();
        $unset = array('htm', 'html', 'xml', 'xlsx', 'xls');
        $data = array();
        foreach ($list as $value) {
            $ext = strtolower($value);
            if(!in_array($ext, $unset)){
                $data[] = strtolower($value);
            }
        }
        return $data;
    }
    //Add End - LIXD-321 - TrungVNIT - 2015/09/11
}

/* ファイルの終わり */