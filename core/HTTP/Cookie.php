<?php

namespace Core\Http;

use Core\Http\Complements\BaseCookie;

class Cookie extends BaseCookie //https://developer.mozilla.org/es/docs/Web/HTTP/Cookies
{

    public string $name;
    protected ?string $value;
    protected string $path;
    protected ?string $domain;
    protected ?string $sameSite;

    protected bool $secure;
    protected bool $httpOnly;
    protected bool $raw;

    protected $expire;

    private const RESERVED_CHARS = [
        '=', ',', ';',
        ' ', "\t", "\r",
        "\n", "\v", "\f"
    ];

    private const RESERVED_CHARS_REPLACEMENT = [
        '%3D', '%2C', '%3B',
        '%20', '%09', '%0D',
        '%0A', '%0B', '%0C'
    ];

    private array $sameSiteTerms = [
        'lax',
        'strict',
        'none'
    ];

    /**
     * @param string $name cookie name
     * @param string $value cookie value
     * @param int|string $expire cookie expiration date
     * @param string $path indicates a URL path that must exist in the requested URL to send the header. The% x2F character ("/") is considered a directory separator, and subdirectories will match as well
     * @param string $domain the Domain and Path directives define the scope of the cookie: to which URLs the cookies should be sent
     * @param bool $secure a secure cookie is only sent to the server with an encrypted request over the HTTPS protocol.
     * @param bool $httpOnly to prevent cross-site scripting (XSS) attacks, HttpOnly cookies are inaccessible from the Document.cookie Javascript API; They are only sent to the server. 
     * @param bool $raw send a cookie without url encoding
     * @param string $sameSite for more info see https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie/SameSite
     */
    public function __construct(string $name, ?string $value, $expire = 0, string $path = '/', ?string $domain = null, bool $secure = false, bool $httpOnly = false, bool $raw = false, ?string $sameSite = 'lax')
    {
        $this->name = $name;
        $this->value = $value;
        $this->expire = $this->expiresTimeStamp($expire);
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
        $this->raw = $raw;
        $this->sameSite = $sameSite;
    }

    public function __toString(): string
    {
        if ($this->raw) $cookie = $this->name;

        else $cookie = str_replace(self::RESERVED_CHARS, self::RESERVED_CHARS_REPLACEMENT, $this->name); //Replacing reserved chars

        $cookie .= "=";

        if ($this->value === null) {
            $cookie .= 'deleted; expires=' . gmdate('D, d-M-Y H:i:s T', time() - 31536001) . '; Max-Age=0';
        } else {

            $cookie .= $this->raw ? $this->value : rawurlencode($this->value);

            $this->expire === 0 ?: $cookie .= '; expires=' . gmdate('D, d-M-Y H:i:s T', $this->expire) . "; Max-Age={$this->getMaxAge()}";
        }

        $this->setOptionalDetails($cookie);

        return $cookie;
    }

    protected function setOptionalDetails(string &$str): void
    {

        $str .= "; path=$this->path";

        is_null($this->domain) ?: $str .= "; domain=$this->domain";

        if ($this->secure) $str .= '; secure';

        if ($this->httpOnly) $str .= '; httponly';

        $this->evaluateSameSite($str);
    }

    protected function expiresTimeStamp($expire = 0): int
    {
        if ($expire instanceof \DateTimeInterface) $expire = $expire->format('U');

        if (!is_numeric($expire)) $expire = strtotime($expire);

        return $expire > 0 && false !== $expire ? (int) $expire : 0;
    }

    protected function getMaxAge(): int
    {
        $maxAge = $this->expire - time();

        return 0 >= $maxAge ? 0 : $maxAge;
    }

    protected function evaluateSameSite(&$str): void
    {
        if (in_array($this->sameSite, $this->sameSiteTerms)) {

            $str .= "; samesite=$this->sameSite";

            if ($this->sameSite == 'none' && !str_contains($str, 'secure')) {
                $str .= '; secure';
            }
        }
    }
}
