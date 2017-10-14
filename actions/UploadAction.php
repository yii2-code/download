<?php
/**
 * Created by PhpStorm.
 * User: cheremhovo
 * Date: 14.10.17
 * Time: 14:12
 */

declare(strict_types=1);

namespace cheremhovo\fileSystem\actions;

use cheremhovo\fileSystem\fileSystem\Path;
use cheremhovo\fileSystem\services\UploadImageService;
use DomainException;
use Yii;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\web\Response;

class UploadAction extends Action
{
    /** @var Path */
    public $path;

    /** @var array */
    public $thumbs = [];

    /**
     * @var
     */
    public $name;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!($this->path instanceof Path)) {
            throw new InvalidConfigException(static::class . '::path must be set');
        }
        if (!is_array($this->thumbs)) {
            throw new InvalidConfigException(static::class . '::array must be set');
        }
        if (!is_string($this->name) || empty($this->name)) {
            throw new InvalidConfigException(static::class . '::name must be set');
        }
    }


    /**
     * @return array
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $service = new UploadImageService(
                $this->path,
                $this->thumbs
            );
            $service->run($this->name);
        } catch (DomainException $exception) {
            Yii::$app->errorHandler->logException($exception);
            return ['error', $exception->getMessage()];
        }
        return [];
    }
}