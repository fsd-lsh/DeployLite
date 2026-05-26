<?php

include_once './common.php';

class cc
{

    private $url;
    private $port;
    private $max;
    private $time = 1;

    public function __construct() {
        error_reporting(E_ALL);  //提示错误信息
        set_time_limit(0);         //设定一个程式所允许执行的秒数   0 是无限循环
        ob_implicit_flush();                //刷新输出缓冲
        parseArgv($this->url, $this->port, $this->max);
    }

    public function run() {
        cli_echo('CC running', __CLASS__);
        while ( $this->time <= $this->max )  {
            if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
                cli_echo('e1', __CLASS__);
            }
            if (socket_bind($sock, $this->url, $this->port) === false) {
                cli_echo('e2', __CLASS__);
            }
            if (socket_listen($sock, 5) === false) {
                cli_echo('e3', __CLASS__);
            }
            $msg = "HTTP/1.1 GET /\r\nHost:"+$_GET['site']+"\r\nConnection: Keep-Alive\r\n";
            socket_write($msg);
            socket_close($sock);
            $this->time++;
        }
        cli_echo('CC close: ' . $this->time, __CLASS__);
    }
}
(new cc())->run();