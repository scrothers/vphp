<?PHP

use vPHP\connect;

class ConnectTest extends \PHPUnit_Framework_TestCase
{
  public function testFailedLogin() {
    global $testcfg;

    $vcenter = new connect(
      $testcfg['host'],
      $testcfg['user'],
      'badpassword'
    );
    $this->assertException($vcenter);
  }
}
