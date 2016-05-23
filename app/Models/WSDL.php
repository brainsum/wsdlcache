<?php

/**
 * Created by PhpStorm.
 * User: mhavelant
 * Date: 2016.05.20.
 * Time: 17:00
 */

namespace App\Models;

use Artisaninweb\SoapWrapper\Extension\SoapService;

class WSDL extends SoapService {

  /**
   * @var String $name
   */
  private $name;

  /**
   * @var String $wsdl
   */
  private $wsdl;

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

  /**
   * @var boolean
   */
  private $trace = true;

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
   * @param $wsdl
   */
  public function setWsdl($wsdl) {
    $this->wsdl = $wsdl;
  }

  /**
   * @return bool
   */
  public function hasTrace() {
    return $this->trace;
  }

  /**
   * @param $trace
   */
  public function setTrace($trace) {
    $this->trace = $trace;
  }

  /**
   * Get all the available functions
   *
   * @return mixed
   */
  public function functions()
  {
    return $this->getFunctions();
  }

}