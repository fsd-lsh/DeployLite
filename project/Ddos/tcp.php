<?php

include_once './common.php';

class tcp
{
    private $packets = 0;
    private $ip;
    private $port;
    private $exec_time;

    public function __construct() {
        ini_set("display_errors", "Off");
        set_time_limit(0);
        ignore_user_abort(FALSE);
        parseArgv($this->ip, $this->port, $this->exec_time);
    }

    public function run() {
        cli_echo('Tcp running', __CLASS__);
        $time = time();
        $max_time = $time + $this->exec_time;
        while(1) {
            $this->packets++;
            if(time() > $max_time){
                break;
            }
            $fp = fsockopen("tcp://{$this->ip}", $this->port,$errno,$errstr,0);
        }
        cli_echo('Tcp close', __CLASS__);
        cli_echo("Package: {$this->packets}", __CLASS__);
        cli_echo('Flow: ' . round(($this->packets * 65 * 8) / (1024 * 1024),2) . 'Mbps', __CLASS__);
        cli_echo('Byte: ' . $this->packets . '(' . round(($this->packets * 65 * 8) / (1024 * 1024),2) . 'Mbps)', __CLASS__);
        cli_echo('Packets averaging: ' . round($this->packets/$this->exec_time, 2) . ' packets/s', __CLASS__);
    }
}
(new tcp())->run();
