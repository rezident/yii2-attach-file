<?php


namespace rezident\attachfile\traits;


use rezident\attachfile\exceptions\FileNotFoundException;
use rezident\attachfile\models\AttachedFile;

trait AttachedFileImportTrait
{
    /**
     *
     * @param $absolutePath
     * @param bool $isMain
     * @param null $name
     * @return AttachedFile
     * @throws FileNotFoundException
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function attachLocalFile($absolutePath, $isMain = false, $name = null)
    {
        if(is_file($absolutePath) == false) {
            throw new FileNotFoundException($absolutePath);
        }

        if($name === null) {
            $name = basename($absolutePath);
        }

        return $this->attachFile($absolutePath, $isMain, $name);

    }

    abstract protected function attachFile($absolutePath, $isMain, $name);

}