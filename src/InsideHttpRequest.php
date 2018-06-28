<?php


namespace HuanL\Extend;

/**
 * 一个用于测试模拟发送http请求的类
 * Class InsideHttpRequest
 * @package HuanL\Extend
 */
class InsideHttpRequest {

    /**
     * 启动操作
     * @var mixed
     */
    protected $startAction;

    /**
     * 返回内容
     * @var string
     */
    protected $responseContent = '';

    /**
     * 返回头
     * @var array
     */
    public static $responseHeader = [];

    public function __construct($startAction = '') {
        $this->startAction = $startAction;
        $_SERVER['SCRIPT_NAME'] = '';
        $_SERVER['HTTP_HOST'] = '127.0.0.1';
    }

    /**
     * 设置启动操作
     * @param string $startAction
     * @return $this
     */
    public function setStartAction($startAction) {
        $this->startAction = $startAction;
        return $this;
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
        $_SERVER['HTTP_HOST'] = $domain;
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
        ob_start();
        if (is_string($this->startAction)) {
            require $this->startAction;
        } else if ($this->startAction instanceof \Closure) {
            call_user_func($this->startAction);
        }
        $this->responseContent = ob_get_clean();
        return $this;
    }

    /**
     * 获取请求返回内容
     * @return mixed
     */
    public function content() {
        //根据contentType返回
        $ret = $this->responseContent;
        if (strpos($this->contentType(), 'json')) {
            $ret = json_decode($ret, true);
        }
        return $ret;
    }

    /**
     * 返回json数组对象
     * @return array
     */
    public function json() {
        return json_decode($this->responseContent, true);
    }

    public function contentType($type = '') {
        if (empty($type)) {
            //为空取content
            foreach ($this->header() as $key => $value) {
                if ($key == 'CONTENT_TYPE')
                    return $value;
            }
            return '';
        } else {
            $_SERVER['CONTENT_TYPE'] = $type;
        }
    }

    /**
     * 获取返回code
     * @return int
     */
    public function response_code() {
        return http_response_code();
    }

    /**
     * 获取/设置 头
     * @param array $header
     * @return $this|array
     */
    public function header($header = []) {
        if (count($header) > 0) {
            //参数不是空的,就是请求的header
            //TODO:有特殊情况需要处理
            $whitelist = ['CONTENT_TYPE'];
            foreach ($header as $key => $value) {
                $key = str_replace('-', '_', strtoupper($key));
                if (!in_array($key, $whitelist)) {
                    $key = 'HTTP_' . $key;
                }
                $_SERVER[$key] = $value;
            }
            return $this;
        } else {
            return array_merge(static::$responseHeader, headers_list());
        }
    }

    /**
     * 用于替换内置的header函数
     * @param $header
     * @param bool $replace
     * @param int $code
     */
    public static function addHeader($header, $replace = true, $code = 200) {
        $key = strtoupper(substr($header, 0, strpos($header, ':')));
        foreach (static::$responseHeader as $k => $value) {
            $tmpKey = strtoupper(substr($value, 0, strpos($value, ':')));
            if ($tmpKey == $key) {
                static::$responseHeader[$k] = $header;
                return;
            }
        }
        static::$responseHeader[] = $header;
    }
}
