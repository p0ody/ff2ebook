<?php
require_once("class.FileManager.php");

class ZipManager
{
    private $zipFile;
    private $fm;

    function __construct()
    {
        $this->zipFile = new ZipArchive();
        $this->fm = new FileManager();
    }

    function __decontruct()
    {
        $this->zipFile->close();
    }

    public function openZip($path)
    {
        $pathinfo = pathinfo($path);
        $this->fm->createDirectory($pathinfo["dirname"]);

        return $this->zipFile->open($path, ZipArchive::CREATE);
    }

    public function addFile($file, $zipDir = false)
    {
        $path = pathinfo($file);

        $this->zipFile->addFile($file, (($zipDir === false) ? "" : $zipDir ."/"). $path["filename"] .".". $path["extension"]);
    }

    public function addAllFileInDir($dir, $zipDir = false)
    {
        $itr = new DirectoryIterator($dir);

        foreach($itr as $file)
        {
            if (!$file->isFile())
                continue;

            $this->addFile($file->getPathName(), $zipDir);
        }

    }

    /*public function addAllFileInDirRecursive($dir)
    {
        $itr = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);

        foreach(new RecursiveIteratorIterator($itr) as $name => $object) /** @var $object SplFileInfo
        {
            $name = str_replace('\\', '/', $name);

            if(in_array(substr($name, strrpos($name, '/') + 1), array('.', '..'))) // Ignore . and .. folders
                continue;

            /*if (realpath($object->getPathname()) == realpath($dir))
                continue;*/

            /*if ($object->isDir())
                $this->zipFile->addEmptyDir($this->getZipDir($dir, $name));


            if ($object->isFile())
            {
                $this->addFile($name, $this->getZipDir($dir, $name));
            }
                //$this->zipFile->addFromString(str_replace($dir . '/', '', $name), file_get_contents($name));
        }


    }*/

    private function getZipDir($local, $zip)
    {
        return substr(realpath($zip), strlen(realpath($local)) + 1);
    }

    public function close()
    {
        $this->zipFile->close();
    }

    public function extract($dest)
    {
        $this->zipFile->extractTo($dest);
    }
}