<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *
 * @author Jean eric et cat
 *        
 */
class FileUploader
{

    private $targetDir;

    /**
     * Init target Directory
     * @param string $targetDir
     */
    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }
    
    
    /**
     * Upload file
     * @param  UploadedFile $file
     * @param  Int $id
     * @return String
     */
    public function upload(UploadedFile $file, int $id): string
    {
        $fileName = $id.'.'.$file->guessExtension();
        $file->move($this->getTargetDir(), $fileName);

        return $fileName;
    }
    
    /**
     * Delete file before upload if not exist
     * 
     * @param Object $entity
     * @param UploadedFile $file
     * @param int $id
     * @return void
     */
    public function delFile($entity, UploadedFile $file, int $id): void
    {
        $fileName = $id.'.'.$file->guessExtension();
    
        // Si on avait un ancien fichier, on le supprime
        if (null !== $entity->getTempFilename()) {
            $oldFile = $this->getTargetDir().'/'.$id.'.'.$fileName;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

    }
    
    /**
     * Get target directory
     * @return string
     */
    public function getTargetDir(): string
    {
        return $this->targetDir;
    }
    
   
}

?>