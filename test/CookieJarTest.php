<?php
declare(strict_types=1);

namespace Plaisio\Cookie\Test;

use PHPUnit\Framework\TestCase;
use Plaisio\Cookie\Cookie;
use Plaisio\Cookie\CookieJar;
use SetBased\Exception\FallenException;

/**
 * Test cases for class CookieJar.
 */
class CookieJarTest extends TestCase
{
  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test add().
   */
  public function testAdd(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    $cookies->add(new Cookie(['name'  => 'name4',
                              'value' => 'value4']));
    self::assertSame(4, count($cookies));
    self::assertSame('value4', $cookies->getValue('name4'));

    $cookies->add(new Cookie(['name'  => 'name2',
                              'value' => 'value2.1']));
    self::assertSame(4, count($cookies));
    self::assertSame('value2.1', $cookies->getValue('name2'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constructor with cookies.
   */
  public function testConstructor1(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    self::assertSame(3, count($cookies));
    self::assertSame('value1', $cookies->getValue('name1'));
    self::assertSame('value2', $cookies->getValue('name2'));
    self::assertSame('value3', $cookies->getValue('name3'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test constructor with illegal value.
   */
  public function testConstructor2(): void
  {
    $this->expectException(FallenException::class);

    new CookieJar([new Cookie(['name'  => 'name1',
                               'value' => 'value1']),
                   new Cookie(['name'  => 'name2',
                               'value' => 'value2']),
                   new Cookie(['name'  => 'name3',
                               'value' => 'value3']),
                   $this]);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test get().
   */
  public function testGet(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    $cookie2 = $cookies->get('name2');
    self::assertSame('value2', $cookie2->value);

    $cookie0 = $cookies->get('name0');
    self::assertNull($cookie0);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test method getIterator().
   */
  public function testGetIterator(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    $count = 0;
    foreach ($cookies as $name => $cookie)
    {
      $count++;
      self::assertSame($name, 'name'.$count);
      self::assertSame($name, $cookie->name);
    }
    self::assertSame(3, $count);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test has().
   */
  public function testHas(): void
  {
    $cookies = new CookieJar([['name' => 'name1'],
                              ['name'    => 'name2',
                               'value'   => 'value2'],
                              ['name'    => 'name3',
                               'value'   => 'value3',
                               'expires' => 0],
                              ['name'    => 'name4',
                               'value'   => 'value4',
                               'expires' => time() - 100],
                              ['name'    => 'name5',
                               'value'   => 'value5',
                               'expires' => time() + 365 * 24 * 60 * 560]]);

    self::assertFalse($cookies->has('name0'));
    self::assertFalse($cookies->has('name1'));
    self::assertTrue($cookies->has('name2'));
    self::assertTrue($cookies->has('name3'));
    self::assertFalse($cookies->has('name4'));
    self::assertTrue($cookies->has('name5'));
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test offset* functions.
   */
  public function testOffset(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    self::assertTrue(isset($cookies['name1']));
    self::assertFalse(isset($cookies['name0']));

    self::assertInstanceOf(Cookie::class, $cookies['name1']);
    self::assertNull($cookies['name0'] ?? null);

    $cookies['name0'] = new Cookie(['name' => 'name0', 'value' => 'value0']);
    self::assertInstanceOf(Cookie::class, $cookies['name0']);

    unset($cookies['name1']);
    self::assertNull($cookies['name1'] ?? null);
  }

  //--------------------------------------------------------------------------------------------------------------------
  /**
   * Test method remove().
   */
  public function testRemove(): void
  {
    $cookies = new CookieJar([new Cookie(['name'  => 'name1',
                                          'value' => 'value1']),
                              new Cookie(['name'  => 'name2',
                                          'value' => 'value2']),
                              new Cookie(['name'  => 'name3',
                                          'value' => 'value3'])]);

    $cookies->remove('name2', false);
    self::assertNull($cookies->get('name2'));

    $cookies->remove('name1', true);
    self::assertNotNull($cookies->get('name1'));
    self::assertSame(1, $cookies['name1']->expires);

    $cookies->remove('name1', true);
    self::assertNotNull($cookies->get('name1'));
    self::assertSame(1, $cookies['name1']->expires);

    $cookies->remove('name0', true);
    self::assertNotNull($cookies->get('name0'));
    self::assertSame(1, $cookies['name0']->expires);
  }

  //--------------------------------------------------------------------------------------------------------------------
}

//----------------------------------------------------------------------------------------------------------------------
