<?php

namespace Core\Http;

use Core\Foundation\Traits\Http\canMorphContent;
use Core\Foundation\Traits\Http\httpResponses;
use Core\Foundation\Traits\Http\responseMessages;
use Core\Foundation\Traits\Http\Renderable;
use Core\Http\ResponseComplements\HeaderBag;

class Response
{
    use responseMessages, Renderable, canMorphContent, httpResponses;

    /**
     * Response content
     */
    private $content;

    /**
     * The current http protocol for the response
     * @var string
     */
    private string $httpProtocolVersion;

    /**
     * The corresponding status code text
     * @var string
     */
    private string $statusText;

    /**
     * The response status code
     * @var int
     */
    private int $statusCode = 200;

    /**
     * The response headers
     * @var Core\Http\Complements\HeaderBag
     */
    private HeaderBag $headers;

    /**
     * Response current charset
     */
    const CHARSET = 'UTF-8';

    /**
     * Status codes corresponding texts
     * @var array
     */
    protected array $statusTexts = [
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
        418 => 'I\'m a teapot',                                               // RFC2324
        421 => 'Misdirected Request',                                         // RFC7540
        422 => 'Unprocessable Entity',                                        // RFC4918
        423 => 'Locked',                                                      // RFC4918
        424 => 'Failed Dependency',                                           // RFC4918
        425 => 'Too Early',                                                   // RFC-ietf-httpbis-replay-04
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
    ];

    /**
     * Renderizes a server response
     * @param mixed $content content to be renderized
     * @param int $code response code
     */
    public function __construct($content = null, int $code = 200, array $headers = [])
    {
        $this->headers = new HeaderBag($headers);
        $this->httpProtocolVersion = '1.0';
        if ($this->canSetContent($content)) $this->setContent($content, $code);
    }

    /**
     * If the content is an array, it would be morphed to json
     * @param mixed $content
     * @return void
     */
    public function setContent($content, int $code): void
    {
        $this->content = $content;

        if ($this->shouldBeJson($this->content)) {
            $this->headers->set('Content-Type', 'application/json');
            $this->content = $this->morphToJson($content);
        }

        $this->setFileMimeType();
        $this->setStatusCode($code)->prepare()->sendHeaders();
        $this->render($this->content);
    }

    /**
     * Analyzes the response headers and prepares the response
     * @return self
     */
    public function prepare(): self
    {
        if ($this->statusCode >= 100 && $this->statusCode < 200 || in_array($this->statusCode, [204, 304])) { //informational or empty

            $this->headers->remove('Content-Type');  //Remove content related headers
            $this->headers->remove('Content-Length');

            // prevent PHP from sending the Content-Type header based on default_mimetype
            ini_set('default_mimetype', '');
        }

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset=' . self::CHARSET);
        }

        if (str_contains($this->headers->get('Content-Type'), 'text/') && !str_contains($this->headers->get('Content-Type'), 'charset')) {
            $this->headers->set('Content-Type', $this->headers->get('Content-Type') . '; charset=' . self::CHARSET);
        }

        if ($this->headers->has('Transfer-Encoding')) $this->headers->remove('Content-Length');

        if (\Core\Http\Server::get('SERVER_PROTOCOL') !== 'HTTP:/1.0') {
            $this->setHttpProtocolVersion('1.1');
        }

        /*
        if (!$this->headers->has('Connection')) {
            $this->setConnectionType('keep-alive');
        }
        */

        // Check if we need to send extra expire info headers
        if ('1.0' == $this->getHttpProtocolVersion() && str_contains($this->headers->get('Cache-Control'), 'no-cache')) {
            $this->headers->set('pragma', 'no-cache');
            $this->headers->set('expires', -1);
        }

        return $this;
    }

    /**
     * Sets the status code for the current response
     * @param int $code
     * @return void
     */
    protected function setStatusCode(int $code): self
    {
        if (\in_array($code, array_keys($this->statusTexts))) {
            $this->statusCode = $code;
            $this->statusText = $this->statusTexts[$code];
        }

        return $this;
    }

    /**
     * Sends the response headers 
     * @return void
     */
    protected function sendHeaders(): void
    {
        if (!headers_sent()) {

            foreach ($this->headers as $header => $value) {
                $this->setHeader($header, $value, true, $this->statusCode);
            }

            header(sprintf('HTTP/%s %s %s', $this->httpProtocolVersion, $this->statusCode, $this->statusText), true, $this->statusCode);
        }
    }

    /**
     * In case that the content is an stored file, the response Content-Type will be adapted the current file
     * @return void
     */
    protected function setFileMimeType(): void
    {
        if ($this->content instanceof \Core\Http\Complements\StoredFile) {
            $this->headers->set('Content-Type', $this->content->type());
            $this->headers->set('Content-Length', $this->content->size());
        }
    }

    /**
     * Sets the Connection header
     * @param int $timeout sets the timeout for a keep-alive connection
     * @param int $max sets a limit of requests for a keep-alive connection
     * @return void
     */
    protected function setConnectionType(string $type, int $timeout = 5, int $max = 99): void
    {
        if (\in_array($type, ['keep-alive', 'close'])) {

            $this->headers->set('Connection', $type);

            if ($type === 'keep-alive') $this->headers->set(
                'keep-alive',
                sprintf('timeout=%d, max=%d', $timeout, $max)
            );
        }
    }

    /**
     * Sets cache to private
     * @return self
     */
    protected function setCacheControlPrivate(): self
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');

        return $this;
    }

    /**
     * Sets the cache to public
     * @return self
     */
    protected function setCacheControlPublic(): self
    {
        $this->headers->removeCacheControlDirective('private');
        $this->headers->addCacheControlDirective('public');

        return $this;
    }
    /**
     * Sets an immutable cache
     * @return self
     */
    protected function setCacheControlInmutable(bool $immutable = true): self
    {
        $immutable
            ? $this->headers->addCacheControlDirective('immutable')
            : $this->headers->removeCacheControlDirective('immutable');

        return $this;
    }

    /**
     * Determines wheter the response is cacheable
     * @return bool
     * @see https://developer.mozilla.org/es/docs/Web/HTTP/Headers/Cache-Control
     */
    protected function isCacheable(): bool
    {
        if (!\in_array($this->statusCode, [301, 302, 307, 308, 410])) return false;

        if ($this->headers->hasCacheControlDirective('no-store')) return false;

        return $this->isValidateable();
    }

    /**
     * Determines wheter the reponse is validateble
     * @return bool
     */
    protected function isValidateable(): bool
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }

    /**
     * Sets the current HTTP protocol
     * @return void
     */
    protected function setHttpProtocolVersion(string $version): void
    {
        $this->httpProtocolVersion = $version;
    }

    /**
     * Returns the current HTTP protocol
     * @return string
     */
    protected function getHttpProtocolVersion(): string
    {
        return $this->httpProtocolVersion;
    }
}
