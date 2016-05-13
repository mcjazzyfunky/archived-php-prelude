<?php

namespace prelude\io;

require_once dirname(__FILE__) . '/File.php';
require_once dirname(__FILE__) . '/IOException.php';
require_once dirname(__FILE__) . '/../util/Seq.php';

use prelude\util\Seq;

class Files {
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
            throw new IOException("'$file' is a directory - cannot be removed by function 'Sys::delete'!");
        }

        if (is_file($file) || is_link($file)) {
            if (!unlink($file)) {
                throw new IOException("Could delete '$file'!");
            }

            $ret = true;
        }

        return $ret;
    }

    static function listDir($dir, array $options = null) {
        return new Seq(function () use ($dir, $options) {
            if (is_dir($dir)) {
                $returnFilenames = @$options['returnFilenames'];
                
                if (!is_bool($returnFilenames)) {
                    $returnFilenames = false;
                }
                
                $types = @$options['types'];
                
                if (!is_array($types)) {
                    $types = ['f', 'd'];
                }
                
                $includeFiles = in_array('f', $types);
                $includeDirs = in_array('d', $types);
                $includeLinks = in_array('l', $types);
                
                $recursive = @$options['recursive'];
                
                if (!is_bool($recursive)) {
                    $recursive = false;
                }
                
                $fileSelector = @$options['fileSelector'];
                
                if (!is_string($fileSelector) || trim($fileSelector) === '') {
                    $fileSelector = null;
                }

                $dirSelector = @$options['dirSelector'];
                
                if (!is_string($dirSelector) || trim($dirSelector) === '') {
                    $dirSelector = null;
                }

                $linkSelector = @$options['linkSelector'];
                
                if (!is_string($linkSelector) || trim($linkSelector) === '') {
                    $linkSelector = null;
                }
                
                $items = scandir($dir, SCANDIR_SORT_ASCENDING);
                
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    
                    $path = Files::combinePathes($dir, $item);
                    $isDir = is_dir($path);
                    
                    if ($includeFiles && is_file($path) && self::pathIncluded($path, $fileSelector)
                        || $includeDirs && $isDir && self::pathIncluded($path, $dirSelector)
                        || $includeLinks && is_link($path) && self::pathIncluded($path, $linkSelector)) {

                        if ($returnFilenames) {
                            yield $path;
                        } else {
                            yield new File($path);
                        }
                    }
                    
                    if ($recursive && $isDir) {
                        $subitems = self::listDir($path, $options);
                        
                        foreach ($subitems as $subitem) {
                            yield $subitem;
                        }
                    }
                }
            }
        });
    }

    static function isAbsolutePath($path) {
        $ret = false;
        $firstChar = isset($path[0]) ? $path[0] : '';
        $secondChar = isset($path[1]) ? $path[1] : '';
        
        if ($firstChar == '/' || $firstChar == '\\' || $secondChar == ':') {
            $ret = true;
        }
        
        return $ret;
    }
    
    static function combinePathes($path1, $path2, $useNativeDirectorySeparator = false) {            
        $ret = '';
        $path1 = self::normalizePath($path1, true);
        $path2 = self::normalizePath($path2, true);
        
        if ($path1 === '' || self::isAbsolutePath($path2)) {
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
            
        if ($useNativeDirectorySeparator) {
            $ret = str_replace('/', DIRECTORY_SEPARATOR, $ret);
        }
        
        return $ret;
    }
    
    private static function pathIncluded($path, $selector) {
        return $selector === null || fnmatch($selector, $path);
    }
}
