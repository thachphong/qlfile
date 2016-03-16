<?php
/**
 * 基本ファイル
 *
 * フレームワーク開始処理です。必要な部分を最初に変更します
 *
 * @category   ACWork
 * @copyright  2013 
 * @version    0.9
*/

define('ACW_PROJECT', 'qlfile');	// プロジェクト名

/**
* 公開ディレクトリ
*/
define('ACW_PUBLIC_DIR', str_replace("\\", '/', __DIR__));

// 1階層上をルートディレクトリに設定
//define('ACW_ROOT_DIR', str_replace("\\", '/', dirname(__DIR__)));
define('ACW_ROOT_DIR', ACW_PUBLIC_DIR);

// 上記のACW_PROJECTのプロジェクト名はinitialize内でdefineを切り替えるために主に使います。
/**
* デフォルトディレクトリ定義
*/
define('ACW_SYSTEM_DIR', ACW_ROOT_DIR . '/acwork');	// ルートディレクトリ
define('ACW_APP_DIR', ACW_ROOT_DIR . '/app');
define('ACW_USER_CONFIG_DIR', ACW_ROOT_DIR . '/user_config');
define('ACW_SMARTY_PLUGIN_DIR', ACW_APP_DIR . '/ext/smarty');
define('ACW_TEMPLATE_DIR', ACW_APP_DIR . '/template');
define('ACW_VENDOR_DIR', ACW_APP_DIR . '/vendor');
/**
* 一時ディレクトリ
*/
define('ACW_TMP_DIR', ACW_ROOT_DIR . '/tmp');
define('ACW_TEMPLATE_CACHE_DIR', ACW_TMP_DIR . '/template_cache');
define('ACW_LOG_DIR', ACW_TMP_DIR . '/log');
define('ACW_TMP_DIR_IMG', ACW_TMP_DIR . '/images');
define('ACW_TMP_DIR_BAT', ACW_TMP_DIR . '/bat');

// プロジェクトの初期化処理
require ACW_USER_CONFIG_DIR . '/initialize.php';

// 実行
ACWCore::acwork();
/* ファイルの終わり */