<?php

/**
 * Description of Queue
 *
 * @author j.durand
 */
class Queue {

    private $status;
    private $name;
    private $array_spools;
    const READY = 0;
    const HELD = 1;
    const ERROR = 2;

    public function __construct($name, $status) {
        $this->name = $name;
        $this->status = $status;
        $this->array_spools = array();
    }

    function getStatus() {
        return $this->status;
    }
    
    function getStatusAsString() {
        if ($this->getStatus() == Queue::READY) {
            return "ready";
        } else if ($this->getStatus() == Queue::HELD) {
            return "held";
        } else {
            return "error";
        }
    }
    function getName() {
        return $this->name;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setName($name) {
        $this->name = $name;
    }

    function getSpools() {
        return $this->array_spools;
    }

    function addSpool($spool) {
        $this->array_spools[] = $spool;
    }
    
    function getSpoolsByQueue(){
	return $this->array_spools;
    }
}

?>
