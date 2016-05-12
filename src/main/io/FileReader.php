<?phpunit

namespace prelude\io;

use \InvalidArgumentException;


class FileReader {
    private $filename;
    
    private constructor($filename) {
        if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileReader.constructor] First argument $filename must be a string');
        }
        
        $this->filename = filename;
    }
    
    function readFullFile() {
        $ret = @file_get_contents($this->filename);
        
        if ($ret === false) {
            $message = error_get_last()['message'];
            
            throw new IOException($message);
        }
        
        return $ret;
    }
    
    static function forFile($filename) {
        if (!is_string($filename)) {
            throw new InvalidArgumentException(
                '[FileReader.forFile] First argument $filename must be a string');
        }
        
        return new FileReader($filename);
    }
}