<?php
declare(strict_types=1);

namespace Plaisio\Cookie;

use SetBased\Exception\FallenException;

/**
 * The cookie jar, class for handling cookies to must send to the user agent.
 */
class CookieJar implements \IteratorAggregate, \ArrayAccess, \Countable
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * The cookies in this jar.
   *
   * @var Cookie[]
   */
  private array $cookies = [];

  //--------------------------------------------------------------------------------------------------------------------

  /**
   * Object constructor.
   *
   * @param Cookie[] $cookies The cookies.
   */
  public function __construct(array $cookies = [])
  {
    foreach ($cookies as $key => $value)
    {
      switch (true)
      {
        case is_array($value):
          $this->cookies[$value['name']] = new Cookie($value);
          break;

        case $value instanceof Cookie:
          $cookie                      = clone $value;
          $this->cookies[$value->name] = $cookie;
          break;

        default:
          throw new FallenException('type', gettype($value));
      }
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a cookie to this cookie jar. If there is already a cookie with the same name in this cookie jar, it will be
   * replaced.
   *
   * @param Cookie $cookie The cookie to be added.
   */
  public function add(Cookie $cookie): void
  {
    $this->cookies[$cookie->name] = $cookie;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the number of cookies in this cookie jar.
   *
   * This method is required by the SPL `Countable` interface. It will be implicitly called when you use
   * `count($cookies)`.
   *
   * @return int
   */
  public function count(): int
  {
    return count($this->cookies);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns a cookie specified by its name.
   *
   * @param string $name The name of the cookie.
   *
   * @return Cookie|null
   *
   * @see getValue()
   */
  public function get(string $name): ?Cookie
  {
    return $this->cookies[$name] ?? null;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   *
   * Returns an iterator for traversing the cookies in this cookie jar.
   *
   * This method is required by the SPL interface [[\IteratorAggregate]]. It will be implicitly called when you use
   * `foreach` to traverse this cookie jar.
   *
   * @return \ArrayIterator
   */
  public function getIterator(): \ArrayIterator
  {
    return new \ArrayIterator($this->cookies);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the value of a cookie.
   *
   * @param string      $name    The name of the cookie.
   * @param string|null $default The value that should be returned when the cookie does not exist.
   *
   * @return string|null
   */
  public function getValue(string $name, ?string $default = null): ?string
  {
    return $this->cookies[$name]->value ?? $default;
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether there is a cookie with the specified name.
   *
   * Note that if a cookie is marked for deletion from the user agent, this method will return false.
   *
   * @param string $name The name of the cookie.
   *
   * @return bool
   *
   * @see remove()
   */
  public function has(string $name): bool
  {
    return (isset($this->cookies[$name]) &&
      $this->cookies[$name]->value!=='' &&
      ($this->cookies[$name]->expires===null ||
        $this->cookies[$name]->expires===0 ||
        $this->cookies[$name]->expires>=time()));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns whether there is a cookie with the specified name.
   *
   * @param mixed $offset The name of the cookie.
   *
   * This method is required by the SPL interface [[\ArrayAccess]]. It is implicitly called when you use something like
   * `isset(cookies[$name])`.
   *
   * @return bool
   */
  public function offsetExists(mixed $offset): bool
  {
    return $this->has($offset);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Returns the cookie with the specified name.
   *
   * This method is required by the SPL interface [[\ArrayAccess]]. It is implicitly called when you use something like
   * `$cookie = $cookies[$name];`. This is equivalent to [[get()]].
   *
   * @param mixed $offset The name of the cookie.
   *
   * @return Cookie|null
   */
  public function offsetGet(mixed $offset): ?Cookie
  {
    return $this->get($offset);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Adds a cookie to this cookie jar.
   *
   * This method is required by the SPL interface [[\ArrayAccess]]. It is implicitly called when you use something like
   * `$cookies[$name] = $cookie;`. This is equivalent to [[add()]].
   *
   * @param mixed $offset The name of the cookie.
   * @param mixed $value  The cookie to be added.
   */
  public function offsetSet(mixed $offset, mixed $value): void
  {
    $this->add($value);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes the named cookie.
   *
   * This method is required by the SPL interface [[\ArrayAccess]].It is implicitly called when you use something like
   * `unset($cookies[$name])`. This is equivalent to [[remove()]].
   *
   * @param mixed $offset The name of the cookie.
   */
  public function offsetUnset(mixed $offset): void
  {
    $this->remove($offset);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Removes a cookie. If `$removeFromBrowser` is true, the cookie will be removed from the browser. In this case, a
   * cookie with outdated expiry will be added to this cookie jar.
   *
   * @param string $name                The name of the cookie.
   * @param bool   $removeFromUserAgent Whether to remove the cookie from user agent.
   */
  public function remove(string $name, bool $removeFromUserAgent = true): void
  {
    if ($removeFromUserAgent)
    {
      if (!isset($this->cookies[$name]))
      {
        $this->cookies[$name] = new Cookie(['name' => $name]);
      }

      $this->cookies[$name]->expires = 1;
    }
    else
    {
      unset($this->cookies[$name]);
    }
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
