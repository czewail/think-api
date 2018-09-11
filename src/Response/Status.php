<?php
namespace Zewail\Api\Response;

use Zewail\Api\Exceptions\ResponseException;
use Zewail\Api\Http\Response;

class Status {

    public static $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',            // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',          // RFC4918
        208 => 'Already Reported',      // RFC5842
        226 => 'IM Used',               // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',    // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I am a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Unordered Collection',   // RFC2817
        426 => 'Upgrade Required',                                            // RFC2817
        428 => 'Precondition Required',                                       // RFC6585
        429 => 'Too Many Requests',                                           // RFC6585
        431 => 'Request Header Fields Too Large',                             // RFC6585
        451 => 'Unavailable For Legal Reasons',                               // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',                                     // RFC2295
        507 => 'Insufficient Storage',                                        // RFC4918
        508 => 'Loop Detected',                                               // RFC5842
        510 => 'Not Extended',                                                // RFC2774
        511 => 'Network Authentication Required',                             // RFC6585
    );

    protected $methods = [];

    public function __construct()
    {
        $this->populateCodeMethods();
    }

    /**
     * 转换 http 状态为方法调用
     */
    private function populateCodeMethods() {
        foreach (self::$statusTexts as $code => $text) {
            $name = $this->toIdentifier($text);
            if ($code >= 400) {
                $this->methods[$name] = function($message = '') use ($code, $text) {
                    if (!$message) $message = $text;
                    throw new ResponseException($code, $message);
                };
            } else {
                $this->methods[$name] = function($message = '') use ($code, $text) {
                    if (!$message) $message = $text;
                    $response = new Response($message);
                    $response->setCode($code);
                    return $response;
                };
            }
        }
    }

    /**
     * 转换 http 状态为方法名
     *
     * @param $str
     * @return mixed
     */
    private function toIdentifier($str) {
        $res = [];
        $strArr = explode(' ', $str);
        foreach ($strArr as $item) {
            array_push($res, ucwords($item));
        }
        $res = implode('', $res);
        return str_replace('/[^ _0-9a-z]/gi', '', $res);
    }

    /**
     * 调用动态方法
     *
     * @param $methodName
     * @param array $args
     * @return mixed
     */
    public function __call($methodName, array $args) {
        if (isset($this->methods[$methodName])) {
            return call_user_func_array($this->methods[$methodName], $args);
        }
    }
}