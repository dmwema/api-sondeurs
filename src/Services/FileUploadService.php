<?php

namespace App\Services;

use Symfony\Component\HttpKernel\KernelInterface;

readonly class FileUploadService
{
    public function __construct(
        private KernelInterface $kernel,
    ) {}
    public function uploadFile ($image, String $path = '/lessons/images'): false | String {
        if (!$image) {
            return false;
        }
        $fileName = uniqid('', true) . '.' . $image->guessExtension();
        $destinationPath = $this->kernel->getProjectDir() . '/public' . $path;
        $image->move($destinationPath, $fileName);
        return $path . '/' . $fileName;
    }
}