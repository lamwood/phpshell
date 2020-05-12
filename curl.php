<?php
//demo
$url = 'http://m.huo.com/test.php';
$data = ['username' => 'jonh', 'password' => '123456', 'sex' => '未知'];
$header = [
    'HTTPHEADER' => ['CLIENT-IP:8.8.8.8', 'X-FORWARDED-FOR:8.8.4.4'],
    'REFERER' => 'http://localhost/refere.php',
    'USERAGENT' => 'My Agent Client For PHP',
];
$json = post_data($url, $data, $header);
$result = json_decode($json, true);
print_r($result);
/**
 * POST方式提交数据
 * @param type $url
 * @param type $data
 * @param type $header
 * @param type $cookie
 * @return boolean
 * @throws Exception
 */
function post_data($url, $data, $header = [], $cookie = ''){
    if($url == '' || !is_array($data)){
        return false;
    }
    $ch = curl_init();
    if(!$ch){
        return false;
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); //强制使用 HTTP/1.1
    curl_setopt($ch, CURLOPT_POST, 1); //发送POST类型数据
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回数据不直接输出
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //禁止cURL验证对等证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //设为0表示不检查证书,设为2表示校验当前的域名是否与CN匹配
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_1); //curl设置ssl版本1.1+
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); //用来告诉 PHP 在成功连接服务器前等待多久（连接成功之后就会开始缓冲输出）
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); //超时时间,用来告诉PHP成功从服务器接收缓冲完成前需要等待多长时间
    //curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); //代理方式:socks5
    //curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1'); //代理IP
    //curl_setopt($ch, CURLOPT_PROXYPORT, '1080'); //代理端口
    $cookie == '' ? null : curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    isset($header['HTTPHEADER']) ? curl_setopt($ch, CURLOPT_HTTPHEADER, $header['HTTPHEADER']) : null; //设置请求头
    isset($header['REFERER']) ? curl_setopt($ch, CURLOPT_REFERER, $header['REFERER']) : null; ////来路模拟
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); //POST数据，$post可以是数组，也可以是拼接
    curl_setopt($ch, CURLOPT_USERAGENT, $header['USERAGENT'] ? $header['USERAGENT'] : 'PHP CURL Client/1.0.0'); //伪造 User-Agent 
    $result = curl_exec($ch); //成功时返回 TRUE， 或者在失败时返回 FALSE。 然而，如果 设置了CURLOPT_RETURNTRANSFER选项，函数执行成功时会返回执行的结果，失败时返回 FALSE 。
    if(!$result){
        throw new Exception(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}