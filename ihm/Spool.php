<?php

/**
 * Description of Spool
 *
 * @author j.durand
 */
class Spool {

    private $status;
    private $id;
    private $name;
    private $dateTime;
    private $size;
    private $device;
    const READY = 0;
    const HELD = 1;
    const ERROR = 2;
    const PROCESSING = 3;

    public function __construct($name, $status, $id, $dateTime, $size) {
        $this->name = $name;
        $this->status = $status;
        $this->id = $id;
        $this->dateTime = $dateTime;
        $this->size = $size;
    }

    public function getStatus() {
        return $this->status;
    }
    
    public function getStatusAsString() {
        if ($this->getStatus() == Spool::READY) {
            return "ready";
        } else if ($this->getStatus() == Spool::HELD) {
            return "held";
        } else if ($this->getStatus() == Spool::PROCESSING) {
            return "processing";
        } else {
            return "error";
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getDateTime() {
        return $this->dateTime;
    }

    public function getSize() {
        return $this->size;
    }
    
    public function getDevice() {
        return $this->device;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setDateTime($dateTime) {
        $this->dateTime = $dateTime;
    }

    public function setSize($size) {
        $this->size = $size;
    }
    
    public function setDevice($device) {
        $this->device = $device;
    }
}

?>
