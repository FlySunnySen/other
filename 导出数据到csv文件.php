<?php
for ($i=1; $i < 11; $i++) {
    $url = "http://read.xiaoshuo1-sm.com/novel/i.php?do=is_caterank&p=1&page={$i}&words=&shuqi_h5=&onlyCpBooks=1&firstCate=%E6%A0%A1%E5%9B%AD&sort=monthHot&_=1545187616519c";
    $url = "http://read.xiaoshuo1-sm.com/novel/i.php?do=is_caterank&p=1&page={$i}&words=&shuqi_h5=&onlyCpBooks=1&firstCate=%E6%A0%A1%E5%9B%AD&sort=monthHot&_=1545205847905";
    $content = file_get_contents($url);
    $content = json_decode($content, true);
    $data = $content['data'];
    foreach ($data as $key => $value) {
        $datas[] = [
            'title' => $value['title'],
        ];
    }
}
$headlist = ['标题'];
csvExport($datas, $headlist, './test.csv');

/**
 * 导出excel(csv)
 * @data 导出数据
 * @headlist 第一行,列名
 * @fileName 输出Excel文件名
 */
function csvExport($data = array(), $headlist = array(), $fileName) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
    header('Cache-Control: max-age=0');

    //打开PHP文件句柄,php://output 表示直接输出到浏览器
    $fp = fopen('php://output', 'a');
    //输出Excel列名信息
    foreach ($headlist as $key => $value) {
        //CSV的Excel支持GBK编码，一定要转换，否则乱码
        $headlist[$key] = iconv('utf-8', 'gbk', $value);
    }
    //将数据通过fputcsv写到文件句柄
    fputcsv($fp, $headlist);
    //计数器
    $num = 0;
    //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
    $limit = 100000;
    //逐行取出数据，不浪费内存
    $count = count($data);
    for ($i = 0; $i < $count; $i++) {
        $num++;
        //刷新一下输出buffer，防止由于数据过多造成问题
        if ($limit == $num) {
            ob_flush();
            flush();
            $num = 0;
        }
        $row = $data[$i];
        foreach ($row as $key => $value) {
            $string = iconv('utf-8', 'gbk', $value) . "\t";
            $row[$key] = (string) $string;
        }
        fputcsv($fp, $row);
    }
}
