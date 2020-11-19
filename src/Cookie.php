<?php
declare(strict_types=1);

namespace Plaisio\Cookie;

/**
 * Class for representing cookies.
 */
class Cookie
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * SameSite policy Lax will prevent the cookie from being sent by the browser in all cross-site browsing context
   * during CSRF-prone request methods (e.g. POST, PUT, PATCH etc). E.g. a POST request from https://otherdomain.com to
   * https://yourdomain.com will not include the cookie, however a GET request will. When a user follows a link from
   * https://otherdomain.com to https://yourdomain.com it will include the cookie.
   *
   * @see $sameSite
   */
  const SAME_SITE_LAX = 'Lax';

  /**
   * SameSite policy Strict will prevent the cookie from being sent by the browser in all cross-site browsing context
   * regardless of the request method and even when following a regular link. E.g. a GET request from
   * https://otherdomain.com to https://yourdomain.com or a user following a link from
   * https://otherdomain.com to https://yourdomain.com will not include the cookie.
   *
   * @see $sameSite
   */
  const SAME_SITE_STRICT = 'Strict';

  /**
   * The domain of the cookie.
   *
   * @var string
   */
  public $domain = '';

  /**
   * The timestamp at which the cookie expires. Defaults to 0, meaning "until the browser is closed". If NULL the cookie
   * will not be send to the user agent.
   *
   * @var ?int
   */
  public $expire = 0;

  /**
   * When TRUE the cookie will be made accessible only through the HTTP protocol. This means that the cookie won't be
   * accessible by scripting languages, such as JavaScript. It has been suggested that this setting can effectively
   * help to reduce identity theft through XSS attacks (although it is not supported by all browsers), but that claim
   * is often disputed.
   *
   * @var bool
   */
  public $httpOnly = true;

  /**
   * The name of the cookie.
   *
   * @var string
   */
  public $name;

  /**
   * The path on the server in which the cookie will be available on.
   *
   * @var string
   */
  public $path = '/';

  /**
   *
   * The PHP default value is NULL, however our default value is 'Lax'.
   *
   * @var ?string
   */
  public $sameSite = self::SAME_SITE_LAX;

  /**
   * Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client. When set to
   * TRUE, the cookie will only be set if a secure connection exists. On the server-side, it's on the programmer to
   * send this kind of cookie only on secure connection.
   *
   * The PHP default value is false, however our default value is true.
   *
   * @var bool
   */
  public $secure = true;

  /**
   * The value of the cookie.
   *
   * @var string
   */
  public $value = '';

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Object constructor.
   *
   * @param array $properties The properties of the cookie.
   */
  public function __construct(array $properties = [])
  {
    foreach ($properties as $name => $value)
    {
      if (property_exists($this, $name))
      {
        $this->$name = $value;
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
