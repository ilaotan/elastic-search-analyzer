<?php
header("Content-type: text/html; charset=utf-8"); 
/**
 * 提供远程 字扩展词字典、远程扩展停止词字典 
 *
 *
 * ik 接收两个返回的头部属性 Last-Modified 和 ETag，只要其中一个有变化，就会触发更新，ik 会每分钟获取一次
 * 内容为一词一行 "\n" 换行
 * 为了避免对es造成不必要的压力，最好做成更新了词库 再改变头部属性
 *
 * liukelin
 * 
 * http://xxx/api/custom_word.php?action=hot
 * http://xxx/api/custom_word.php?action=stop
 * 
 */

$action = isset($_GET['action'])?$_GET['action']:null;
$dir = __DIR__.'/../words/';
$words = '';
$file = array();
// $action = 'hot';

switch ($action) {

    case 'hot': // 扩展词
        
        $dir = $dir.'hot/';
        $file = scandir($dir);

        break;  
    case 'stop': // 扩展停止词

        $dir = $dir.'stop/';
        $file = scandir($dir);

        break;
    default:
        break;

    foreach ($file as $key => $v) {
        if (pathinfo($dir.$v)['extension']=='dic') {
            $words .= @include($dir.$v);
            $words .= "\n"; // 添加一个换行符
        }
    }
    $words = trim($words);

    $s = <<<'EOF'
        {$words}
EOF;

    // 为了避免对es造成不必要的压力，最好做成更新了词库 再改变头部属性 
    $ETag = '"' . time() . '"';
    // header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT', true, 200);
    header('Last-Modified: '.gmdate('D, d M Y H:i:s', time() ).' GMT');
    // header('ETag: "5816f348-23"');
    header("ETag: $ETag");
    echo $s;
}







