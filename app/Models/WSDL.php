<?php

/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.20.
 * Time: 17:00
 */

namespace App\Models;

class WSDL {

  /**
   * @var String $name
   */
  private $name;

  /**
   * @var String $wsdl
   */
  private $wsdl;

  /**
   * @var String
   */
  private $type;

  /**
   * @var Boolean $status
   */
  private $available;

  /**
   * @var \DateTime $lastCheck
   */
  private $lastCheck;

  /**
   * @var \DateTime $lastModification
   */
  private $lastModification;

  public function __construct($wsdl) {
    $this->wsdl = $wsdl;
  }

  /**
   * @return \DateTime
   */
  public function getLastCheck() {
    return $this->lastCheck;
  }

  /**
   * @return \DateTime
   */
  public function getLastModification() {
    return $this->lastModification;
  }

  /**
   * @return String
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return bool
   */
  public function isAvailable() {
    return $this->available;
  }

  /**
   * @param $available
   */
  public function setAvailable($available) {
    $this->available = $available;
  }

  /**
   * @param $lastCheck
   */
  public function setLastCheck($lastCheck) {
    $this->lastCheck = $lastCheck;
  }

  /**
   * @param $lastModification
   */
  public function setLastModification($lastModification) {
    $this->lastModification = $lastModification;
  }

  /**
   * @param $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @return String
   */
  public function getWsdl() {
    return $this->wsdl;
  }

  /**
   * @param $type
   */
  public function setType($type) {
    $this->type = $type;
  }

  /**
   * @return String
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Generates a filename based on the name and type.
   *
   * @return string
   */
  public function generateFileName() {
    return ($this->getName().".".$this->getType().".xml");
  }
}