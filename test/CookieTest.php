<?php
declare(strict_types=1);

namespace Plaisio\Cookie\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Cookie\Cookie;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Test cases for class Cookie.
 */
class CookieTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test for constructor.
   */
  public function testConstructor(): void
  {
    $cookie = new Cookie();
    assertSame('', $cookie->name);
    assertSame('', $cookie->value);
    assertSame(0, $cookie->expires);
    assertSame('/', $cookie->path);
    assertSame('', $cookie->domain);
    assertTrue($cookie->secure);
    assertTrue($cookie->httpOnly);
    assertSame('Lax', $cookie->sameSite);

    $cookie = new Cookie(['name'     => 'name',
                          'value'    => 'value',
                          'expires'  => 123456,
                          'path'     => '/api',
                          'domain'   => 'www.setbased.nl',
                          'secure'   => false,
                          'httpOnly' => false,
                          'sameSite' => Cookie::SAME_SITE_STRICT]);
    assertSame('name', $cookie->name);
    assertSame('value', $cookie->value);
    assertSame(123456, $cookie->expires);
    assertSame('/api', $cookie->path);
    assertSame('www.setbased.nl', $cookie->domain);
    assertFalse($cookie->secure);
    assertFalse($cookie->httpOnly);
    assertSame('Strict', $cookie->sameSite);

    $this->expectException(\LogicException::class);
    new Cookie(['hello' => 'world']);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
