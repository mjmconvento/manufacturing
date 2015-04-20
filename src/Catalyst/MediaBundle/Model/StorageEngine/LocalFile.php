<?php

namespace Catalyst\MediaBundle\Model\StorageEngine;

use Catalyst\MediaBundle\Model\StorageEngineInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LocalFile implements StorageEngineInterface
{
    protected $base_dir;
    protected $base_url;
    protected $directory;

    public function __construct($base_dir, $base_url)
    {
        $this->base_dir = $base_dir;
        $this->base_url = $base_url;

        // indexed directory
        $this->directory = '';
    }

    public function addFile(UploadedFile $file)
    {
        // generate filename
        $filename = $this->generateFilename($file);
        $fullpath = $this->base_dir . DIRECTORY_SEPARATOR . $this->directory;

        // make directory
        $this->makeDirectory($fullpath);

        // move file
        $file->move($fullpath, $filename);

        // result data
        $res = [
            'full_path' => $fullpath . DIRECTORY_SEPARATOR . $filename,
            'url' => $this->base_url . '/' . $this->directory . '/' . $filename,
            'filename' => $filename,
        ];

        return $res;
    }

    protected function makeDirectory($dir)
    {
        // first check if it already exists
        if (file_exists($dir))
            return false;

        if (! mkdir($dir, 0755, true))
            throw new Exception('Could not create directory for upload.');

        return true;
    }

    protected function generateDirectory($id)
    {
        // single level
        $this->directory = substr($id, -2) . DIRECTORY_SEPARATOR . substr($id, -4, 2);

        return $this->directory;
    }

    protected function generateFilename(UploadedFile $file)
    {
        // generate unique id
        $id = uniqid();

        // generate relative directory
        $this->generateDirectory($id);

        // get extension
        $ext = $file->getClientOriginalExtension();

        return $id . '.' . $ext;
    }
}
