<?php

namespace Catalyst\MediaBundle\Entity\Storage;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_storage_localfile")
 */
class LocalFile
{
    protected $id;

    /** @ORM\Column(type="string", length=200 nullable=false) */
    protected $full_path;

    public function setFullPath($fp)
    {
        $this->full_path = $fp;
        return $this;
    }

    public function getFullPath()
    {
        return $this->full_path;
    }
}
