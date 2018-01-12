<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Entity\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Advert
{

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="price", type="decimal", scale=2)
     */
    private $price;

    /**
     * @ORM\Column(name="follow", type="boolean")
     */
    private $follow = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid()
     */
    private $image;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Itineraire", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $itineraire;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Geolocate", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $geolocate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", cascade={"persist"})
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Application", mappedBy="advert")
     */
    private $applications;
    
    
    
    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="nb_applications", type="integer")
     */
    private $nbApplications = 0;


    public function __construct()
    {
        $this->date = new \Datetime();
        $this->categories = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    /**
     * @Assert\Callback
     */
    public function isContentValid(ExecutionContextInterface $context)
    {
        /*
         * $forbiddenWords = array('échec', 'abandon');
         *
         * // On vérifie que le contenu ne contient pas l'un des mots
         * if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContent())) {
         * // La règle est violée, on définit l'erreur
         * $context
         * ->buildViolation('Contenu invalide car il contient un mot interdit.') // message
         * ->atPath('content') // attribut de l'objet qui est violé
         * ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
         * ;
         * }
         */
    }

    /**
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param Image $image            
     * @return Advert
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     *
     * @return Image
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
        return $this;
    }

    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    public function getCategories()
    {
        return $this->categories;
    }

    /**
     *
     * @param Application $application            
     * @return Advert
     */
    public function addApplication(Application $application)
    {
        $this->applications[] = $application;
        
        // On lie l'annonce � la candidature
        $application->setAdvert($this);
        
        return $this;
    }

    /**
     *
     * @param Application $application            
     */
    public function removeApplication(Application $application)
    {
        $this->applications->removeElement($application);
        
        // Et si notre relation �tait facultative (nullable=true, ce qui n'est pas notre cas ici attention) :
        // $application->setAdvert(null);
    }

    /**
     *
     * @return ArrayCollection
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \Datetime());
    }

    public function setUpdatedAt(\Datetime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function increaseApplication()
    {
        $this->nbApplications ++;
    }

    public function decreaseApplication()
    {
        $this->nbApplications --;
    }

    /**
     * Set price
     *
     * @param float $price            
     * @return Advert
     */
    public function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice():?float
    {
        return $this->price;
    }

    /**
     * Set nbApplications
     *
     * @param integer $nbApplications            
     * @return Advert
     */
    public function setNbApplications($nbApplications)
    {
        $this->nbApplications = $nbApplications;
        
        return $this;
    }

    /**
     * Get nbApplications
     *
     * @return integer
     */
    public function getNbApplications()
    {
        return $this->nbApplications;
    }


    

    /**
     * Set geolocate
     *
     * @param \App\Entity\Geolocate $geolocate            
     * @return Advert
     */
    public function setGeolocate(\App\Entity\Geolocate $geolocate = null)
    {
        $this->geolocate = $geolocate;
        
        return $this;
    }

    /**
     * Get geolocate
     *
     * @return \App\Entity\Geolocate
     */
    public function getGeolocate(): \App\Entity\Geolocate
    {
        return $this->geolocate;
    }

    /**
     * Set itineraire
     *
     * @param \App\Entity\Itineraire $itineraire            
     * @return Advert
     */
    public function setItineraire(\App\Entity\Itineraire $itineraire = null)
    {
        $this->itineraire = $itineraire;
        
        return $this;
    }

    /**
     * Get itineraire
     *
     * @return \App\Entity\Itineraire
     */
    public function getItineraire(): \App\Entity\Itineraire
    {
        return $this->itineraire;
    }

    /**
     * Set follow
     *
     * @param boolean $follow            
     * @return Advert
     */
    public function setFollow($follow)
    {
        $this->follow = $follow;
        
        return $this;
    }

    /**
     * Get follow
     *
     * @return boolean
     */
    public function getFollow()
    {
        return $this->follow;
    }
}
