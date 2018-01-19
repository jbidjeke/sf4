<?php
namespace App\EventSubscriber;


use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Entity\Image;
use App\Entity\Post;
use App\Service\FileUploader;

/**
 *
 * @author Jean eric et cat
 *        
 */
class ImageUploadListener
{

    private $uploader;
    
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * PostPersist
     * 
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        // upload only works for Image entities
        if (!$entity instanceof Post) {
            return;
        }
        
        $image = $entity->getAdvert()->getImage();
        
        $this->uploadFile($image);
    }
    
    
    
    /**
     * PrePersist
     * 
     * @param PreUpdateEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        
        $entity = $args->getEntity();
        
        // upload only works for Image entities
        if (!$entity instanceof Post) {
            return;
        }
        
        $image = $entity->getAdvert()->getImage();
        
        $file = $image->getFile();
        // Si jamais il n'y a pas de fichier (champ facultatif)
        if (null === $file) {
            return;
        }
        
        // on crée le dossier image s'il n'existe pas
        $this->makeDirIfNotExist($this->uploader->getTargetDir());
        
        // Le nom du fichier est son id, on doit juste stocker également son extension
        $image->setUrl ($file->guessExtension());
        
        // Et on genère l'attribut alt de la balise <img>, à la valeur du nom du fichier sur le PC de l'internaute
        $image->setAlt ($file->getClientOriginalName());
        
    }

    /**
     * Upload file
     * 
     * @param Object $entity
     */
    private function uploadFile($entity): void
    {
        
        $id = $entity->getId();
        $file = $entity->getFile();
        
        // only upload new files
        if ($file instanceof UploadedFile) {
            $this->uploader->delFile($entity, $file, $id);
            $fileName = $this->uploader->upload($file, $id);
            //$entity->setFile($fileName);
        }
    }
    
    /**
     * PreRemove
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        // On sauvegarde temporairement le nom du fichier, car il dépend de l'id
        $entity->setTempFilename($this->uploader->getTargetDir().'/'.$entity->getId().'.'.$entity->getUrl());
    }
    
    /**
     * PostRemove
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        // En PostRemove, on n'a pas accusé  l'id, on utilise notre nom sauvegardé
        if (file_exists($entity->getTempFilename())) {
            // On supprime le fichier
            unlink($entity->getTempFilename());
        }
    }
    
    /**
     * Make directory if not exist
     * @param string $dir
     */
    public function makeDirIfNotExist(string $dir): void
    {
        if (!is_dir($dir))
            mkdir($dir, 0777, true);
    
    }
}

?>