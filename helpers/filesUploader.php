<?php

interface FileUploader {
    public function setNext(FileUploader $handler): FileUploader;

    public function handle(array $image, string $targetDir): string;
}

abstract class AbstractFileUploader implements FileUploader {
    private ?FileUploader $nextHandler;

    public function setNext(FileUploader $handler): FileUploader {
        $this->nextHandler = $handler;
        return $handler;
    }

    public function handle(array $image, string $targetDir): string {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($image, $targetDir);
        }

        return '';
    }

    protected function next(array $image, string $targetDir): string {
        if ($this->nextHandler) {
            return $this->nextHandler->handle($image, $targetDir);
        }

        return '';
    }
}

class ExtensionValidator extends AbstractFileUploader {
    public function handle(array $image, string $targetDir): string {
        $target_file = $targetDir . basename($image["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'Invalid file extension';
        }

        return $this->next($image, $targetDir);
    }
}

class SizeValidator extends AbstractFileUploader {
    public function handle(array $image, string $targetDir): string {
        if ($image["size"] > 2 * 1024 * 1024) {
            return 'File is too large (max 2MB)';
        }

        return $this->next($image, $targetDir);
    }
}

class FileMover extends AbstractFileUploader {
    public function handle(array $image, string $targetDir): string {
        $newFileName = uniqid() . '_' . basename($image["name"]);
        $newDir = "uploads/" . $targetDir;


        $this->createTheDirIfNotExists($newDir);

        $newTargetFile = $newDir . $newFileName;

        if (move_uploaded_file($image["tmp_name"], $newTargetFile)) {
            return $newFileName;
        } else {
            return 'Error moving file';
        }
    }


    private function createTheDirIfNotExists(string $newTargetFile): void {
        if (!file_exists($newTargetFile)) {
            mkdir($newTargetFile, 0777, true);
        }
    }
}


