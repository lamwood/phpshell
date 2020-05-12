<?php
/**
 * 删除一个目录及目录下的所有文件夹和文件
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
deldir('E:\hxo_bak');
/**
 * 删除一个目录及目录下的所有文件夹和文件
 * @param string $dir 要删除的目录
 * @return boolean false 删除失败 true 删除成功
 */
function deldir($dir){
    if(!is_dir($dir)){
        return false;
    }
    $files = scandir($dir);
    if($files === false){
        return false;
    }
    foreach($files as $file){
        if(in_array($file, ['.', '..'])){
            continue;
        }
        $_file = in_array(substr($dir, -1), ['/', '\\']) ? $dir.$file : $dir.DIRECTORY_SEPARATOR.$file;
        if(is_dir($_file)){
            deldir($_file);
        }else{
            unlink($_file);
        }
    }
    return rmdir($dir);
}