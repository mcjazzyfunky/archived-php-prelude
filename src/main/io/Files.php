<?php

namespace prelude\io;

require_once dirname(__FILE__) . '/File.php';
require_once dirname(__FILE__) . '/IOException.php';
require_once dirname(__FILE__) . '/../util/Seq.php';

use InvalidArgumentException;
use prelude\util\Seq;

final class Files {
    private function __construct() {
    }
    
    static function makeDir($dir, $mode = 0777, $recursive = false) {
        if ($mode === null) {
            $mode = 0777;
        }
        
        if (!@mkdir($dir, $mode, $recursive)) {
            throw new IOException("[Files.makeDir] Could not create directory '$dir'");
        }
    }

    static function removeDir($dir) {
        $errMsg = '';

        if (file_exists($dir)) {
            if (is_file($dir) || is_link($dir)) {
                if (!unlink($dir)) {
                     $errMsg = "[Files.removeDir] Could not remove '$dir'!";
                }
            } else {
                foreach (scandir($dir) as $item) {
                    if ($item != '.' && $item != '..') {
                        @chmod($dir . "/" . $item, 0777);
                        self::removeDir($dir . "/" . $item);
                    }
                }

                if (!rmdir($dir)) {
                    $errMsg = "Could not remove directory '$dir'";
                }
            }

            if ($errMsg) {
                throw $exception;
            }
        }
    }

    static function delete($file) {
        $ret = false;

        if (is_dir($file)) {
            throw new IOException("[Files.delete] '$file' is a directory");
        }

        if (is_file($file) || is_link($file)) {
            if (!unlink($file)) {
                throw new IOException("Could delete '$file'!");
            }

            $ret = true;
        }

        return $ret;
    }

    static function getFileName($path) {
        $ret = basename($path);
        
        if ($ret === '') {
            $ret = false;
        }
        
        return $ret;
    }

    static function getParentPath($file) {
        $path = is_string($file) ? $file : $file->getPath();
        
        $ret = false;
        $parentPath = basedir($path);
        
        if (strlen($parentPath) > 0) {
            $last = $parentName[-1];
            
            if ($last !== ':' && $last !== '/') {
                $ret = $parentPath;
            } else {
                
                
            }
        }
        
        return $ret;
    }
    
    static function getParentFile($file) {
        $parentPath = self::getParentPath($file);
        
        return $parentPath === false ? false : File::from($parentPath);
    }
    
    static function isAbsolute($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.isAbsolute] First argument $file must be a string '
                . 'or a File object');
        }
        
        $ret = false;
        $path = is_string($file) ? $file : $file->getPath();
        $firstChar = isset($path[0]) ? $path[0] : '';
        $secondChar = isset($path[1]) ? $path[1] : '';
        
        if ($firstChar == '/' || $firstChar == '\\' || $secondChar == ':') {
            $ret = true;
        }
        
        return $ret;
    }
    
    static function combinePaths($path1, $path2, $useNativeDirectorySeparator = false) {            
        $ret = '';
        $path1 = self::normalizePath($path1, true);
        $path2 = self::normalizePath($path2, true);
        
        if ($path1 === '' || self::isAbsolute($path2)) {
            $ret = $path2;
        } elseif ($path2 === '') {
            $ret = $path1;
        } else {
            $lastChar = substr($path1, strlen($path1) - 1);
            
            if ($lastChar == '/') {
                $ret = $path1 . $path2;
            } else {
                $ret = "$path1/$path2";
            }
        }
        
        $ret = self::normalizePath($ret, $useNativeDirectorySeparator);
        return $ret;    
    }
    
    static function normalizePath($path, $useNativeDirectorySeparator = false) {
        $ret = '';
        $ret = trim($path); // TODO?
        $ret = str_replace('\\', '/', $ret);
        $ret = str_replace('/./', '/', $ret);
            
        if ($useNativeDirectorySeparator) {
            $ret = str_replace('/', DIRECTORY_SEPARATOR, $ret);
        }
        
        return $ret;
    }
    
    static function isFile($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.isFile] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        
        return is_file($path);
    }

    static function isDir($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.isDir] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        
        return is_dir($path);
    }

    static function isLink($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.isLink] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        
        return is_link($path);
    }
    
    static function getSize($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        
        return filesize($path);
    }
    
    static function getCreationTime($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        return @filectime($path);
    }
    
    
    static function getLastModifiedTime($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        return @filemtime($path);
    }
    
    static function getLastAccessTime($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        return @fileatime($path);
    }
    
    static function getSecondsSinceCreation($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        $creationTime = $this->getCreationTime();
        
        return $creationTime === false ? false : now() -> $creationTime;
    }

    static function getSecondsSinceLastModified($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        $lastModifiedTime = $this->getCreationTime();
        
        return $lastModifiedTime === false ? false : now() -> $lastModifiedTime;
    }
    
    static function getSecondsSinceLastAccess($file) {
        if (!is_string($file) && !($file instanceof File)) {
            throw new InvalidArgumentException(
                '[Files.getSize] First argument $file must be a string '
                . 'or a File object');
        }
        
        $path = is_string($file) ? $file : $file->getPath();
        
        $lastAccessTime = $this->getLastAccessTime();
        
        return $lastAccessTime === false ? false : now() -> $lastAccessTime;
    }
}
