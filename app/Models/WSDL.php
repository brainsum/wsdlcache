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
   * @var Integer $id
   */
  private $id;

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
   * @var String
   */
  private $userName;

  /**
   * @var String
   */
  private $password;

  /**
   * @var String
   */
  private $filename;

  /**
   * @var Integer
   */
  private $curlSslVersion;

  /**
   * @var Boolean $status
   */
  private $available;

  /**
   * @var Integer
   */
  private $statusCode;

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

  public function setId($id) {
    $this->id = $id;
  }

  public function getId() {
    return $this->id;
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
   * Returns the url with ?WSDL appended.
   *
   * @return String
   */
  public function getWsdl($appended = FALSE) {
    if($appended) {
      return ($this->wsdl . "?WSDL");
    }

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

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function getUserName() {
    return $this->userName;
  }

  public function setUserName($userName) {
    $this->userName = $userName;
  }

  public function getFilename() {
    if (empty($this->filename)) {
      $this->generateFileName();
    }

    return $this->filename;
  }

  public function setFilename($filename) {
    $this->filename = $filename;
  }

  public function getCurlSslVersion() {
    return $this->curlSslVersion;
  }

  public function setCurlSslVersion($curlSslVersion) {
    $this->curlSslVersion = $curlSslVersion;
  }

  public function getStatusCode() {
    return $this->statusCode;
  }

  public function setStatusCode($statusCode) {
    $this->statusCode = $statusCode;

    $this->available = ((int) $this->statusCode < 400) ? true : false;
  }

  /**
   * Generates a filename based on the name and type.
   *
   * @return string
   */
  public function generateFileName() {
    $this->filename = ($this->getName().".".$this->getType().".xml");

    return $this->filename;
  }

  public function combinedUserPass($encoded = FALSE) {
    if ($encoded) {
      return base64_encode($this->getUserName() . ":" . $this->getPassword());
    }

    return ($this->getUserName() . ":" . $this->getPassword());
  }
}