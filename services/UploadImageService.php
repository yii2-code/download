<?php
/**
 * Created by PhpStorm.
 * User: cheremhovo
 * Date: 14.10.17
 * Time: 16:15
 */

namespace cheremhovo\services;

use cheremhovo\fileSystem\fileSystem\File;
use cheremhovo\fileSystem\fileSystem\Path;
use DomainException;
use Imagine\Image\ManipulatorInterface;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;
use yii\web\UploadedFile;

/**
 * Class UploadImageService
 * @package cheremhovo\services
 */
class UploadImageService
{
    /**
     * @var Path
     */
    private $path;
    /**
     * @var array
     */
    private $thumbs;

    /**
     * UploadImageService constructor.
     * @param Path $path
     * @param array $thumbs
     */
    public function __construct(Path $path, array $thumbs)
    {
        $this->path = $path;
        $this->thumbs = $thumbs;
    }


    /**
     * @param string $name
     */
    public function run(string $name)
    {
        $file = UploadedFile::getInstanceByName($name);
        if (is_null($file)) {
            $this->domainException('Нет файла');
        }
        $name = (new File($file->name))->generateName();
        $this->path->setFile(new File($name));

        if (!$file->saveAs($this->path->getPathFile())) {
            $this->domainException('Ошибка сохранение файла');
        }

        if (!$this->path->isExist()) {
            $this->domainException('Нет файла');
        }

        $this->createThumbs();
    }


    /**
     *
     */
    protected function createThumbs(): void
    {
        if (is_readable($this->path->getPathFile())) {
            foreach ($this->thumbs as $thumb => $config) {
                $thumbPath = $this->path->getDirectory() . '/' . $this->path->getFile()->getThumbName($thumb);
                if (!is_file($thumbPath)) {
                    $this->generateImageThumb($config, $thumbPath);
                }
            }
        }
    }

    /**
     * @param array $config
     * @param string $thumbPath
     */
    protected function generateImageThumb($config, $thumbPath): void
    {
        $width = ArrayHelper::getValue($config, 'width');
        $height = ArrayHelper::getValue($config, 'height');
        $quality = ArrayHelper::getValue($config, 'quality', 100);
        $mode = ArrayHelper::getValue($config, 'mode', ManipulatorInterface::THUMBNAIL_OUTBOUND);

        if (!$width || !$height) {
            $image = Image::getImagine()->open($this->path->getPathFile());
            $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
            if ($width) {
                $height = ceil($width / $ratio);
            } else {
                $width = ceil($height * $ratio);
            }
        }
        Image::thumbnail($this->path->getPathFile(), $width, $height, $mode)->save($thumbPath, ['quality' => $quality]);
    }

    /**
     * @param string $message
     * @throws DomainException
     */
    private function domainException(string $message): void
    {
        throw new DomainException($message);
    }
}