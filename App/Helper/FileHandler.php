<?php

namespace App\Helper;

use App\Application\Contracts\FileHandlerInterface;

class FileHandler implements FileHandlerInterface
{

    const READ_ONLY_MODE = 'r';

    const WRITE_ONLY_MODE = 'w';

    const READ_WRITE_MODE = 'w+';

    const EDIT_ONLY_MODE = 'a';

    const READ_EDIT_MODE = 'a+';

    const WRITE_ONLY_UNIQUE_MODE = 'x';

    const READ_WRITE_UNIQUE_MODE = 'x';

    /**
     * the single instance of the class
     */
    private static $instance = null;

    /**
     * full source of the file with type
     */
    private $source = null;

    /**
     * full source of the file with type
     */
    private $old_source = null;

    /**
     * type of the file
     * .txt .pdf .ppt .etc
     */
    private $type = null;

    /**
     * how to handle the file
    r	Open a file for read only. File pointer starts at the beginning of the file
    w	Open a file for write only. Erases the contents of the file or creates a new file if it doesn't exist. File pointer starts at the beginning of the file
    a	Open a file for write only. The existing data in file is preserved. File pointer starts at the end of the file. Creates a new file if the file doesn't exist
    x	Creates a new file for write only. Returns FALSE and an error if file already exists
    r+	Open a file for read/write. File pointer starts at the beginning of the file
    w+	Open a file for read/write. Erases the contents of the file or creates a new file if it doesn't exist. File pointer starts at the beginning of the file
    a+	Open a file for read/write. The existing data in file is preserved. File pointer starts at the end of the file. Creates a new file if the file doesn't exist
    x+	Creates a new file for read/write. Returns FALSE and an error if file already exists
     */
    private $mode = self::READ_WRITE_MODE;

    private $old_mode = self::READ_EDIT_MODE;

    private $file = null;


    /**
     * initialize the class
     *
     * @param  string $src
     * @param  string $ext
     * @param  string $mode
     *
     * @return void
     */
    private function __construct(string $src = 'test', string $type = '.txt', string $mode = self::READ_WRITE_MODE)
    {

        if (strpos($src, '.') !== false) {

            $this->source = $src;
        } else {

            $this->source = $src  . $type;
        }
        $this->old_source = $this->source;
        $this->type = $type;
        $this->mode = $mode;
    }

    /**
     * returns the single instance of the class
     *
     * @param  string $src
     * @param  string $type
     * @param  string $mode
     *
     * @return void
     */
    public static function singleton(string $src = 'test', string $type = '.txt', string $mode = self::READ_WRITE_MODE)
    {

        if (is_null(self::$instance))
            self::$instance = new FileHandler($src, $type, $mode);

        return self::$instance;
    }

    /**
     * edit source of the file
     *
     * @param  string $source
     *
     * @return void
     */
    public function setSource(string $source)
    {
        $this->old_source = $this->source;
        $this->source = $source;

        return $this;
    }

    /**
     * edit extension of the file starts with .
     *
     * @param  mixed $type
     *
     * @return void
     */
    public function setType(string $type)
    {

        $this->type = $type;

        if ($str = strpos($this->source, '.') !== false) {

            $this->source = substr($this->source, $str, strlen($this->source));
        }

        $this->source .= $type;

        return $this;
    }

    /**
     * file management flexibility
     *
     * @param  mixed $mode
     *
     * @return void
     */
    public function setMode(string $mode)
    {

        $this->mode = $mode;

        return $this;
    }

    /**
     * destroy the instance
     *
     * @return void
     */
    public function destroy()
    {

        if (isset($this->file))
            fclose($this->file);

        $this->instance = null;

        return true;
    }

    /**
     * initialize the file
     *
     * @return void
     */
    public function init($force = false)
    {

        // check if the file is already set
        if ($this->file === null || $this->old_source != $this->source || $force || $this->old_mode != $this->mode) {

            if ($this->file !== null) {

                $this->close();
            }

            $this->old_source = $this->source;

            $this->file = fopen($this->source, $this->mode);
        }

        return $this;
    }

    public function write($content)
    {

        $this->init();
        fwrite($this->file, $content);

        return $this;
    }

    /**
     * outputs the variable info (for developers)
     *
     * @param  mixed $content
     *
     * @return FileHandler
     */
    public function varDump($content)
    {

        return $this->write(print_r($content, true));
    }

    /**
     * completely close the file
     *
     * @return boolean
     */
    public function close()
    {

        fclose($this->file);

        return $this;
    }

    /**
     * get file contents
     *
     * @return void
     */
    public function read()
    {

        $this->setMode(self::READ_ONLY_MODE);

        $this->init(true);

        return fread($this->file, filesize($this->source));
    }

    /**
     * append text to a file
     *
     * @param  string $content
     *
     * @return void
     */
    public function edit($content)
    {

        $this->editMode();
        $this->write($content);

        return $this;
    }

    public function editMode()
    {
        $this->mode = self::EDIT_ONLY_MODE;

        return $this;
    }

    /**
     * remove the file based on source
     *
     * @return void
     */
    public function remove()
    {

        unlink($this->source);

        return $this;
    }
}
