<?php
/* 需要GoogleTranslate */
require_once '../../composer/Composer/autoload.php';

use Stichoza\GoogleTranslate\TranslateClient;


$curDir = dirname(__FILE__);

$dir = $curDir . '/香村多娇';
// 读出全部的章节
$dirs = scandir($dir);

foreach ($dirs as $key => $value) {
	if (!in_array($value, ['.', '..', '.DS_Store', 'en', 'chapter_info.txt'])) {
		doing($value);
	}
}

function doing($txt)
{
	static $times = 0;

	$transStr = ''; // 待翻译字符串
	$strLen = 0;
	$transStrLen = 0;
	$totalStrLen = 0;

	global $dir;
	$filePath = $dir . '/' . $txt;

	$newDir = $dir . '/en/';
	if(!is_dir($newDir)) mkdir($newDir, 0777, true);
	$newFile = $newDir . $txt;

	echo $newFile . "Start ...\n";

	$file = fopen($filePath, "r"); // 以只读的方式打开文件
	while(!feof($file)) {
		$itemStr = fgets($file); //fgets()函数从文件指针中读取一行
		$str = trim($itemStr);
		if (!empty($str)) {
			// echo $str;
			// 计算文本长度
			$str .= "\n\n";
			$strLen = mb_strlen($str) + 2;
			$transStrLen = mb_strlen($transStr);
			$totalStrLen = $transStrLen + $strLen;
			// echo $totalStrLen;exit;
			if ($totalStrLen > 4000) { // 判断如果加上新行是否超过字数限制，如超过，则提交当前内容
				// 翻译
				$translate = TranslateClient::translate('zh-CN', 'en', $transStr);
				file_put_contents($newFile, $translate . "\n\n", FILE_APPEND);
				echo "... $times\n";
				// 把待翻译字符串置空
				$transStr = '';
				// 频率随机控制
				$rand = rand(3, 5);
				echo "=ing= ... sleep $rand seconds\n";
				sleep($rand);
				// 执行次数+1
				$times++;
			}
			// 拼接待翻译字符串
			$transStr .= $str;
		}
	}

	// 如果全部跑完，$transStr还有未处理内容提交
	if (!empty($transStr)) {
		// 翻译
		$translate = TranslateClient::translate('zh-CN', 'en', $transStr);
		file_put_contents($newFile, $translate . "\n\n", FILE_APPEND);
		echo "... $times\n";
		// 把待翻译字符串置空
		$transStr = '';
		// 频率随机控制
		$rand = rand(3, 5);
		echo "... =Last= sleep $rand seconds\n";
		sleep($rand);
		// 执行次数+1
		$times++;
	}

	echo $newFile . " End ...\n\n\n\n";

}
