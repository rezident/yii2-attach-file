<?php


namespace rezident\attachfile\collections;


use Generator;
use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\models\AttachedFile;

class AttachedFilesCollection
{
    /**
     * @var AttachedFile[]
     */
    public $attachedFiles;

    /**
     * @var AttachFileBehavior
     */
    private $behavior;

    public function __construct(AttachFileBehavior $behavior)
    {
        $this->behavior = $behavior;
    }

    /**
     * Resets the is_main flag in all of the attached files
     *
     * @param bool $save Whether the files need to save
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function resetIsMainInAllFiles($save = true)
    {
        $this->initialize();
        foreach ($this->attachedFiles as $attachedFile) {
            $attachedFile->is_main = false;
        }

        if($save) {
            $this->save();
        }
    }

    /**
     * Adds attached file to the model
     *
     * @param AttachedFile $file
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function add(AttachedFile $file)
    {
        $this->initialize();
        $this->attachedFiles[] = $file;
    }

    /**
     * Saves the attached files to the database
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function save()
    {
        $this->initialize();
        $isMainExists = false;
        foreach ($this->attachedFiles as $position => $attachedFile) {
            $attachedFile->position = $position + 1;
            if($attachedFile->is_main) {
                if($isMainExists) {
                    $attachedFile->is_main = false;
                } else {
                    $isMainExists = true;
                }
            }

            $attachedFile->save();
        }
    }

    /**
     * Returns the count of the attached files
     *
     * @return int
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function count()
    {
        $this->initialize();
        return count($this->attachedFiles);
    }

    /**
     * Moves an attached file
     *
     * @param int $from Position from
     * @param int $to Position to
     * @return bool true if success
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function move($from, $to)
    {
        $this->initialize();
        if($this->inRange($from) && $this->inRange($to)) {
            $moveFile = $this->attachedFiles[$from];
            $newAttachedFiles = [];
            foreach ($this->attachedFiles as $index => $file) {
                if($index == $from) {
                    continue;
                }

                if($index == $to) {
                    $newAttachedFiles[] = $moveFile;
                }

                $newAttachedFiles[] = $file;
            }

            $this->attachedFiles = $newAttachedFiles;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Moves an attached file to begin of the list
     *
     * @param int $from
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function moveToBegin($from)
    {
        $this->initialize();
        return $this->move($from, 0);
    }

    /**
     * Moves an attached file to end of the list
     *
     * @param int $from
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function moveToEnd($from)
    {
        $this->initialize();
        return $this->move($from, $this->count() - 1);
    }

    /**
     * Returns an attached file by position
     *
     * @param int $position Position
     *
     * @return null|AttachedFile Requested file or null if position doesn't in range
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function get($position)
    {
        $this->initialize();
        if($this->inRange($position)) {
            return $this->attachedFiles[$position];
        }

        return null;
    }

    /**
     * Returns the main file
     *
     * @return null|AttachedFile
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getMain()
    {
        $this->initialize();
        foreach ($this->attachedFiles as $attachedFile) {
            if($attachedFile->is_main) {
                return $attachedFile;
            }

        }

        return null;
    }

    /**
     * Marks a file as main
     *
     * @param int $position
     * @return bool
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function setMain($position)
    {
        $this->initialize();
        if($this->inRange($position)) {
            $this->resetIsMainInAllFiles(false);
            $this->attachedFiles[$position]->is_main = true;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Returns an attached file by its name
     *
     * @param string $name
     *
     * @return null|AttachedFile
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getByName($name)
    {
        $this->initialize();
        $name = mb_strtolower($name);
        foreach ($this->attachedFiles as $attachedFile) {
            if(mb_strtolower($attachedFile->name) == $name) {
                return $attachedFile;
            }

        }

        return null;
    }

    /**
     * Returns the generator for iterating the files
     *
     * @return Generator|AttachedFile[]
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getGenerator()
    {
        $this->initialize();
        foreach ($this->attachedFiles as $attachedFile) {
            yield $attachedFile;
        }
    }

    /**
     * Returns the first file
     *
     * @return null|AttachedFile
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function first()
    {
        $this->initialize();
        return $this->get(0);
    }

    /**
     * Returns the last file
     *
     * @return null|AttachedFile
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function last()
    {
        $this->initialize();
        return $this->get($this->count() - 1);
    }

    /**
     * Deletes an attached file
     *
     * @param AttachedFile $attachedFile
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function delete(AttachedFile $attachedFile)
    {
        $this->initialize();
        $position = array_search($attachedFile, $this->attachedFiles, true);
        if($position !== false) {
            return $this->deleteByPosition($position);
        }

        return false;
    }

    /**
     * Deletes an attached file by its position
     *
     * @param int $position
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function deleteByPosition($position)
    {
        $this->initialize();
        if($this->inRange($position)) {
            $this->attachedFiles[$position]->delete();
            array_splice($this->attachedFiles, $position, 1);
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Deletes all of the attached files
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function deleteAll()
    {
        $this->initialize();
        foreach ($this->attachedFiles as $attachedFile) {
            $attachedFile->delete();
        }

        $this->attachedFiles = [];
    }

    /**
     * @param int $position
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function inRange($position)
    {
        return $position >= 0 && $position < $this->count();
    }

    /**
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function initialize()
    {
        if(isset($this->attachedFiles) == false) {
            $this->fetch();
        }
    }

    /**
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function fetch()
    {
        $this->attachedFiles = $this->behavior->owner->attachedFiles;
    }

}