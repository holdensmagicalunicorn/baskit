<?php
/**
 * PEAR installer task package for Phing
 *
 * @package Baskit
 * @version 1.0.0
 * @author Wes Mason <wes.mason@isotoma.com>
 * @copyright 2011 Isotoma Limited
 * @license http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
 */

require_once "phing/Task.php";

/**
 * PEAR installer task for Phing
 *
 * @package Baskit
 */
class PearInstallTask extends Task {

    /**
     * The name of the package to install
     */
    private $name = null;

    /**
     * Optional version of the package
     */
    private $version = null;

    /**
     * Optional channel for the package
     */
    private $channel = null;

    /**
     * Current working dir, in this case Phing start dir.
     */
    private $cwd = null;

    /**
     * Optional args to pass to pear
     */
    private $args = null;

    /**
     * The setter for the attribute "name"
     */
    public function setName($str) {
        $this->name = $str;
    }

    /**
     * The setter for the attribute "version"
     */
    public function setVersion($str) {
        $this->version = $str;
    }

    /**
     * The setter for the attribute "channel"
     */
    public function setChannel($str) {
        $this->channel = $str;
    }

    /**
     * The setter for the attribute "args"
     */
    public function setArgs($str) {
        $this->args = $str;
    }

    /**
     * Method to install a pear channel if not already present.
     */
    public function installChannel($channel) {
        $cwd = $this->getCwd();
	exec("${cwd}/bin/pear list-channels 2>&1", $output, $ret);
	if ($ret > 0) {
            $this->log(implode(PHP_EOL, $output) . PHP_EOL);
            throw new BuildException('Unable to check PEAR channels.');
	}
        if (preg_match('/\b' . preg_quote($channel) . '\b/', implode("\n", $output)) == 0) {
            exec("${cwd}/bin/pear channel-discover ${channel} 2>&1", $output, $ret);
	    $this->log(implode(PHP_EOL, $output) . PHP_EOL);
	    if ($ret > 0) {
                throw new BuildException('Unable to install PEAR channel.');
	    }
        } else {
            $this->log("${channel} already installed in PEAR, skipping..");
	}
    }

    /**
     * Accessor for $cwd, sets it if not set based on start dir.
     */
    public function getCwd() {
        if ($this->cwd === null) {
            $properties = $this->getProject()->getProperties();
            $this->cwd = $properties['application.startdir'];
        }
        return $this->cwd;
    }

    /**
     * The main entry point method.
     */
    public function main() {
        $name = $this->name;
        if (!$name && $this->channel === null) {
            throw new BuildException('Package name is required.');
        }

        if ($this->version !== null) {
            $name .= '-' . $this->version;
        }

        if ($this->channel !== null) {
	    $this->installChannel($this->channel);
	    if (!$name) {
		return;
	    }
        }

        if ($this->args !== null) {
            $args = $this->args;
        }

        $cwd = $this->getCwd();
        exec("${cwd}/bin/pear install ${args} ${name} 2>&1", $output, $ret);
        array_pop($output);
        $out = implode(PHP_EOL, $output);
	$this->log($out . PHP_EOL);
	if (strpos($out, 'already installed') === false && $ret > 0) {
            throw new BuildException('PEAR install failed.');
	}
    }
}
