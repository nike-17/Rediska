<?php

class Rediska_Profiler extends Rediska_Options implements IteratorAggregate, Countable
{
    protected $_profiles = array();

    protected $_currentProfile;

    protected $_totalElapsedTime = 0;

    protected $_options = array(
        'enable' => true
    );

    public function start()
    {
        if (!$this->getOption('enable')) {
            return false;
        }

        if ($this->_currentProfile) {
            throw new Rediska_Profiler_Exception('Already started.');
        }

        $this->_currentProfile = new Rediska_Profiler_Profile();

        $this->_profiles[] = $this->_currentProfile;

        return $this->_currentProfile;
    }

    public function stop($profiledContext)
    {
        if (!$this->getOption('enable')) {
            return false;
        }

        if (!$this->_currentProfile) {
            throw new Rediska_Profiler_Exception('Start profiler before end.');
        }

        if ($this->_currentProfile->hasStopped()) {
            throw new Rediska_Profiler_Exception('Already stoped.');
        }

        $this->_currentProfile->stop($profiledContext);

        $this->_totalElapsedTime += $this->_currentProfile->getElapsedTime();

        $this->_currentProfile = null;

        return end($this->_profiles);
    }

    public function reset()
    {
        $this->_profiles         = array();
        $this->_currentProfile   = null;
        $this->_totalElapsedTime = 0;

        return $this;
    }

    public function getProfiles()
    {
        return $this->_profiles;
    }

    public function getLastProfile()
    {
        return end($this->_profiles);
    }

    public function getTotalElapsedTime($zero = 0)
    {
        if ($zero) {
            return sprintf("%.{$zero}f", $this->_totalElapsedTime);
        } else {
            return $this->_totalElapsedTime;
        }
    }

    public function getIterator()
    {
        return new ArrayObject($this->_profiles);
    }

    public function count()
    {
        return count($this->_profiles);
    }
}