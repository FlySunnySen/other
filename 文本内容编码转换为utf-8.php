<?php
/**
汉字编码就这三种常用的 GBK BIG5 UTF-8
使用些使用请先确认你的 文件编码在这这三种编码内，同时 a 文件夹下是文本文件，如：txt

判断编码原理：
先从GBK转换成UTF-8，再转换回来，对比是否和以前的内容相同，相同就证明是GBK，不相同就证明不是
如果不是GBK进行下一个判断
*/

$dir = "./";
$newdir = "./new/";

if(!is_dir($newdir))
	mkdir($newdir, 0777);

if (is_dir($dir))
{
    if ($dh = opendir($dir))
	{
		$i = $j = $k = 0;
        while (($file = readdir($dh)) !== false)
		{
			if($file != '.' && $file != '..' && filetype($dir . $file) != 'dir')
			{
				$text = file_get_contents($dir.$file);

				if (!function_exists('iconv'))
				{
					exit('您的服务器环境不支持iconv函数');
				}
				elseif ($text === iconv('UTF-8', 'GBK//IGNORE', iconv('GBK', 'UTF-8//IGNORE', $text)))
				{
					$text = iconv("GBK", "UTF-8//IGNORE", $text);
				}
				elseif ($text === iconv('UTF-8', 'BIG5//IGNORE', iconv('BIG5', 'UTF-8//IGNORE', $text)))
				{
					$text = iconv("GBK", "BIG5//IGNORE", $text);
				}

				file_put_contents($newdir.$file, $text, LOCK_EX) ? $i++ : $j++;
			}
        }

		echo '转换成功'.$i.'个文件，失败'.$j.'个文件';

        closedir($dh);
    }
}
?>