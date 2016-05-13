<?php

            'recursive' => true,
            'types' => ['f'],
            'listFilenames' => true,
            'fileSelector' => '*.php',

class PathScanner {
    private $recursive;
    private $listFilenames;
    private $fileIncludes;
    private $fileExcludes;
    private $dirIncludes;
    private $dirExcludes;
    private $linkIncludes;
    private $linkExcludes;

    function __construct() {
        $this->recursive = false;
        $this->listFilenames = false;
        $this->fileIncludes = false;
        $this->fileExcludes = false;
        $this->dirIncludes = false;
        $this->dirExcludes = false;
        $this->linkIncludes = false;
        $this->linkExcludes = false;
     }
    
    function recursive($recursive = true) {
        if (!is_bool($recursive)) {
            throw new IllegalArgumentException(
                '[PathScanner#recursive] First argument $recursive must be boolean');
        }
        
        $ret = $this->clone();
        $ret->recursive = $recursive;
        return $ret;
    }

    function listFilenames($listFilenames = true) {
        if (!is_bool($listFilenames)) {
            throw new IllegalArgumentException(
                '[PathScanner#listFilenames] First argument $listFilenames must be boolean');
        }
        
        $ret = $this->clone();
        $ret->listFilesnames = $listFilesnames;
        return $ret;
    }


    function includeFiles($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#includeFiles] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->includeFiles = $select;
        return $ret;
    }

    function excludeFiles($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#excludeFiles] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->excludeFiles = $select;
        return $ret;
    }

    function includeDirs($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#includeDirs] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->includeDirs = $select;
        return $ret;
    }

    function excludeDirs($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#excludeDirs] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->excludeDirs = $select;
        return $ret;
    }

    function includeLinks($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#includeLinks] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->includeDirs = $select;
        return $ret;
    }

    function excludeLinks($select = true) {
        if (!is_bool($select) && !is_callable($select) && !is_string($select)) {
            throw new IllegalArgumentException(
                '[PathScanner#excludeLinks] First argument $select must '
                . 'either be boolean or a function or a string');
        }

        $ret = $this->clone();
        $ret->excludeLinks = $select;
        return $ret;
    }

    function autoTrim($autoTrim) {
         if (!is_bool($autoTrim)) {
            throw new IllegalArgumentException(
                '[PathScanner#recursive] First argument $autoTrim must be boolean');
        }

        $ret = $this->clone();
        $ret->autoTrim = $autoTrim;
        return $ret;
    }

    function scan($dir) {
         if (!is_string($dir) && !($dir instanceof File)) {
            throw new IllegalArgumentException(
                '[PathScanner#scan] First argument $dir must be a string or a File object');
        }
        
    }
    
    private clone() {
        $ret = new PathScanner();

        $ret->recursive = $this->recursive;
        $ret->listFileNames = $this->listFileNames
        $ret->fileIncludes = $this->fileIncludes;
        $ret->fileExcludes = $this->fileExcludes;
        $ret->dirIncludes = $this->dirIncludes;
        $ret->dirExcludes = $this->dirExcludes;
        $ret->linkIncludes = $this->linkIncludes;
        $ret->linkExcludes = $this->linkExcludes;
        
        return ret;
    }
}