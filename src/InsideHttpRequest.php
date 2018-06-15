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

    public function __construct(string $start_file = '') {
        $this->start_file = $start_file;
        $_SERVER['SCRIPT_NAME'] = '';
    }

    /**
     * get请求
     * @param string $uri
     * @param array $param
     * @return InsideHttpRequest
     */
    public function get($uri = '/', $param = []) {
        return $this->method('GET', $uri, $param);
    }

    /**
     * post请求
     * @param string $uri
     * @param array $param
     * @return InsideHttpRequest
     */
    public function post($uri = '/', $param = []) {
        return $this->method('POST', $uri, $param);
    }

    /**
     * put请求
     * @param string $uri
     * @param array $param
     * @return InsideHttpRequest
     */
    public function put($uri = '/', $param = []) {
        return $this->method('PUT', $uri, $param);
    }

    /**
     * delete请求
     * @param string $uri
     * @param array $param
     * @return InsideHttpRequest
     */
    public function delete($uri = '/', $param = []) {
        return $this->method('DELETE', $uri, $param);
    }

    /**
     * 自定义请求方法和uri
     * @param $method
     * @param string $uri
     * @return $this
     */
    public function method($method, $uri = '/', $param = []) {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        if (is_array($param)) {
            $_GET = $param;
            $queryString = '';
            foreach ($param as $key => $value) {
                $queryString .= "$key=$value&";
            }
            $_SERVER['QUERY_STRING'] = $queryString;
        }
        return $this;
    }

    /**
     * http
     * @return $this
     */
    public function http() {
        $_SERVER['REQUEST_SCHEME'] = 'http';
        return $this;
    }

    /**
     * https
     * @return $this
     */
    public function https() {
        $_SERVER['REQUEST_SCHEME'] = 'https';
        return $this;
    }

    /**
     * 域名
     * @param string $domain
     * @return $this
     */
    public function domain(string $domain) {
        $_SERVER['SERVER_NAME'] = $domain;
        return $this;
    }

    /**
     * 设置请求数据
     * @param $data
     * @return $this
     */
    public function data($data) {
        $dataString = '';
        if (is_array($data)) {
            $_POST = $data;
            foreach ($data as $key => $value) {
                $dataString .= "$key=$value&";
            }
        }
        return $this;
    }

    /**
     * 发送请求
     * @return $this
     */
    public function request() {
        require_once $this->start_file;
        return $this;
    }
}