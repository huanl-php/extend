<?php


namespace HuanL\Extend;

/**
 * 一个用于测试模拟发送http请求的类
 * Class InsideHttpRequest
 * @package HuanL\Extend
 */
class InsideHttpRequest {

    /**
     * 启动的引导文件路径
     * @var string
     */
    protected $start_file = '';

    public function __construct(string $start_file) {
        $this->start_file = $start_file;
    }

    public function get() {

    }

    public function method($method, $uri) {
        $_SERVER['HTTP_ACCEPT']='POST';

    }
}