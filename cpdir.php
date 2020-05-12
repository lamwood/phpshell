<?php
/**
 * 复制目录及目录内所有文件
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
//demo:
cpdir('E:\hxo', 'E:\hxo_bak', ['GatewayWorker', 'node']);

/**
 * 复制目录及目录内所有文件
 * @param string $source 源文件夹
 * @param string $dest 目标文件夹
 * @param array $except 要过滤的文件夹或文件
 */
function cpdir($source, $dest, $except = []){
    if(!file_exists($dest)){
        mkdir($dest);
    }
    $_except = array_merge($except, ['.', '..']);
    $handle = opendir($source);
    while(($item = readdir($handle)) !== false){
        if(in_array($item, $_except)){
            continue;
        }
        $_source = $source.'/'.$item;
        $_dest = $dest.'/'.$item;
        if(is_file($_source)){
            copy($_source, $_dest);
        }
        if(is_dir($_source)){
            cpdir($_source, $_dest);
        }
    }
    closedir($handle);
}