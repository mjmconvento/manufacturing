<?php

namespace Catalyst\MediaBundle\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageEngineInterface
{
    public function addFile(UploadedFile $file);
}
