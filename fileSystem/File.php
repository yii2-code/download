<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.09.2017
 * Time: 15:20
 */

declare(strict_types=1);

namespace cheremhovo\fileSystem\fileSystem;

class File
{

    /**
     * @var string
     */
    private $name;

    /**
     * File constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function generateName(): string
    {
        return hash('crc32', pathinfo($this->name, PATHINFO_FILENAME)) . '-' . time() . '.' . pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * @param string $thumb
     * @return string
     */
    public function getThumbName(string $thumb): string
    {
        return $thumb . '-' . $this->name;
    }
}