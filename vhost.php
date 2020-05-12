<?php
/**
 * php设置虚拟主机
 */
$change = false;
$dfile = '/root/domain.ser';
if(!file_exists($dfile)){
    $data = ['test.com', 'default'];
    file_put_contents($dfile, serialize($data));
}
$old_dir = unserialize(file_get_contents($dfile));

$path = '/alidata/www';
$handle = opendir($path);
$new_dir = [];
if($handle){
    while(false !== ($file = readdir($handle))){
        if($file == '.' || $file == '..' || substr($file, 0, 1) == '.'){
            continue;
        }
        if(is_dir($path.'/'.$file)){
            $new_dir[] = $file;
        }
    }
    closedir($handle);
}
$newArr = array_diff($new_dir, $old_dir);

foreach($newArr as $domain){
    $conf = <<<EOT
server {
    listen       80;
    server_name  {$domain} www.{$domain};
    index index.html index.htm index.php;
    root /alidata/www/{$domain};

    location ~ .*\.(php|php7)?$
    {
        fastcgi_pass  127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi.conf;
    }
    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires 1d;
    }
    location ~ .*\.(js|css)?$
    {
        expires 1d;
    }
    access_log  /alidata/log/nginx/access/default.log;
}
EOT;
    $conf_file = '/alidata/server/nginx/conf/vhosts/'.$domain.'.conf';
    if(!file_exists($conf_file)){
        file_put_contents($conf_file, $conf);
    }
    $result = shell_exec('/alidata/server/nginx/sbin/nginx -t 2>&1');
    if(stripos($result, 'nginx.conf test is successful') !== false){
        shell_exec('/etc/init.d/nginx reload');
        $old_dir[] = $domain;
        $change = true;
    }
}
$deleteArr = array_diff($old_dir, $new_dir);

foreach($deleteArr as $domain){
    $conf_file = '/alidata/server/nginx/conf/vhosts/'.$domain.'.conf';
    if(file_exists($conf_file)){
        if(rename($conf_file, $conf_file.'.bak')){
            $result = shell_exec('/alidata/server/nginx/sbin/nginx -t 2>&1');
            if(stripos($result, 'nginx.conf test is successful') !== false){
                shell_exec('/etc/init.d/nginx reload');
                $old_dir = array_diff($old_dir, [$domain]);
                $change = true;
            }
        }
    }
}
if($change){
    file_put_contents($dfile, serialize($old_dir));
}
