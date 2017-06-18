<?php
#made by abolfazl ziaratban (c)
#license GPL

class My_zip extends ZipArchive
{
    public function message($code)
    {
        switch ($code)
        {
            case 0:
            return 'No error';

            case 1:
            return 'Multi-disk zip archives not supported';

            case 2:
            return 'Renaming temporary file failed';

            case 3:
            return 'Closing zip archive failed';

            case 4:
            return 'Seek error';

            case 5:
            return 'Read error';

            case 6:
            return 'Write error';

            case 7:
            return 'CRC error';

            case 8:
            return 'Containing zip archive was closed';

            case 9:
            return 'No such file';

            case 10:
            return 'File already exists';

            case 11:
            return 'Can\'t open file';

            case 12:
            return 'Failure to create temporary file';

            case 13:
            return 'Zlib error';

            case 14:
            return 'Malloc failure';

            case 15:
            return 'Entry has been changed';

            case 16:
            return 'Compression method not supported';

            case 17:
            return 'Premature EOF';

            case 18:
            return 'Invalid argument';

            case 19:
            return 'Not a zip archive';

            case 20:
            return 'Internal error';

            case 21:
            return 'Zip archive inconsistent';

            case 22:
            return 'Can\'t remove file';

            case 23:
            return 'Entry has been deleted';

            default:
            return 'An unknown error has occurred('.intval($code).')';
        }                
    }

    public function isDir($path)
    {
        return substr($path,-1) == '/';
    }

    public function getTree()
    {
        $Tree = array();
        $pathArray = array();
        for($i=0; $i<$this->numFiles; $i++)
        {
            $path = $this->getNameIndex($i);
            $pathBySlash = array_values(explode('/',$path));
            $c = count($pathBySlash);
            $temp = &$Tree;
            for($j=0; $j<$c-1; $j++)
                if(isset($temp[$pathBySlash[$j]]))
                    $temp = &$temp[$pathBySlash[$j]];
                else
                {
                    $temp[$pathBySlash[$j]] = array();
                    $temp = &$temp[$pathBySlash[$j]];
                }
                if($this->isDir($path))
                    $temp[$pathBySlash[$c-1]] = array();
                else
                    $temp[$i] = $pathBySlash[$c-1];
        }
        return $Tree;
    }
}