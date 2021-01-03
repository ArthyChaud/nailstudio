<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function upload(UploadedFile $file)
    {
        $fileName = uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $exception) {
            throw $exception;
        }

        return $fileName;
    }

    public function setTargetDirectory(string $targetDirectory)
    {
        return $this->targetDirectory = $targetDirectory;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getType(string $path) {
        return explode('.', $path)[1];
    }
}