<?php
// src/OC/PlatformBundle/Entity/Itineraire

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
// use Symfony\Component\Validation\Constraints AS Assert;

/**
 * @ORM\Entity(repositoryClass="App\Entity\ItineraireRepository")
 */
class Itineraire
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;
  
  /**
   * @ORM\Column(name="date", type="string", length=20)
   */
  private $date;
  
  /**
   * @ORM\Column(name="departure", type="string", length=255)
   */
  private $departure;

  /**
   * @ORM\Column(name="arrival", type="string", length=255)
   */
  private $arrival;
  
  /**
   * @ORM\Column(name="time", type="string", length=10)
   */
  private $time;
  
  public function __construct()
  {
      $this->date        = null;
      $this->departure   = null;
      $this->arrival     = null;
      $this->time        = null;
  }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Itineraire
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set departure
     *
     * @param string $departure
     * @return Itineraire
     */
    public function setDeparture($departure)
    {
        $this->departure = $departure;
    
        return $this;
    }

    /**
     * Get departure
     *
     * @return string 
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * Set arrival
     *
     * @param string $arrival
     * @return Itineraire
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;
    
        return $this;
    }

    /**
     * Get arrival
     *
     * @return string 
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * Set time
     *
     * @param string $time
     * @return Itineraire
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
    }
}
