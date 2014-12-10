<?PHP

namespace vPHP;

/**
 * vPHP Connector
 *
 * It establishes a connection to a vCenter server
 * and authenticates for API access.
 */
class connect
{
  private $host;
  private $user;
  private $pass;
  private $client;

  public function __construct($host, $user, $pass) {
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;
    $this->bind();
  }

  private function bind() {
    // Try to gain a successful SOAP bind to the vCenter Server
    try {
      $this->client = new SoapClient(
        sprintf("https://%s/sdk/vimService.wsdl", $this->host),
        array(
          'login' => $this->user,
          'password' => $this->pass,
          'trace' => 1,
          'location' => sprintf("https://%s/sdk/", $this->host)
        )
      );
    } catch (Exception $e) {
      die(sprintf("Error: Failed to connect to vCenter, error: %s", $e-getMessage()));
    }

    // Setup the SOAP service instance
    $msg = array();
    $msg['_this'] = new Soapvar ("ServiceInstance", XSD_STRING, "ServiceInstance");
    $result = $this->client->RetrieveServiceContent($msg);

    // Login to the vCenter Server
    $msg = array();
    $msg['_this'] = $result->returnval->sessionManager;
    $msg['userName'] = $this->user;
    $msg['password'] = $this->pass;
    $result = $this->client->login($msg);
    return $result;
  }
}
