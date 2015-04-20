<?php

namespace Catalyst\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_upload")
 */
class Upload
{
    const STATUS_NEW            = 1;
    const STATUS_LINKED         = 2;

    /**
     * @ORM\ID
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string", length=200, nullable=false) */
    protected $full_path;

    /** @ORM\Column(type="string", length=200, nullable=false) */
    protected $filename;

    /** @ORM\Column(type="string", length=200, nullable=false) */
    protected $url;

    /** @ORM\Column(type="datetime") */
    protected $date_create;

    /** @ORM\Column(type="integer") */
    protected $status;

    public function __construct()
    {
        $this->date_create = new DateTime();
        $this->status = self::STATUS_NEW;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setFullPath($fp)
    {
        $this->full_path = $fp;
        return $this;
    }

    public function getFullPath()
    {
        return $this->full_path;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function setURL($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function deleteFile()
    {
        unlink($this->full_path);
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
