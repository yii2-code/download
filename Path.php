<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.09.2017
 * Time: 13:31
 */

declare(strict_types=1);

namespace cheremhovo\fileSystem;

use Yii;
use yii\helpers\FileHelper;

/**
 * Class Path
 * @package cheremhovo\fileSystem
 */
class Path
{

    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $baseDirectory;

    /**
     * @var File
     */
    private $file;


    /**
     * Path constructor.
     * @param string $directory
     * @param string $baseDirectory
     * @param File|null $file
     */
    public function __construct(string $directory, string $baseDirectory, File $file = null)
    {
        $this->directory = Yii::getAlias($directory);
        FileHelper::createDirectory($this->directory, 0777);
        $this->baseDirectory = Yii::getAlias($baseDirectory);
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }


    /**
     * @param File $file
     */
    public function setFile(File $file): void
    {
        $this->file = $file;
    }


    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }


    /**
     * @return string
     */
    public function getUrlFile(): string
    {
        $url = str_replace($this->baseDirectory, '', $this->getPathFile());
        return $url;
    }


    /**
     * @return string
     */
    public function getPathFile()
    {
        return $this->directory . '/' . $this->file->getName();
    }

    /**
     * @param Path $path
     */
    public function copyTo(Path $path): void
    {
        copy($this->getPathFile(), $path->getPathFile());
    }


    /**
     * @return bool
     */
    public function isExist(): bool
    {
        if (!file_exists($this->getPathFile())) {
            return false;
        }
        if (!is_file($this->getPathFile())) {
            return false;
        }
        return true;
    }
}