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

  public $client;
  public $service;
  public $usersession;

  public function __construct($host, $user, $pass) {
    $this->host = $host;
    $this->user = $user;
    $this->pass = $pass;
    return $this->bind();
  }

  private function bind() {
    // Try to gain a successful SOAP bind to the vCenter Server
    try {
      $this->client = @new \SoapClient(
        sprintf("https://%s/sdk/vimService.wsdl", $this->host),
        array(
          'login' => $this->user,
          'password' => $this->pass,
          'trace' => 1,
          'location' => sprintf("https://%s/sdk/", $this->host),
          'exceptions' => True
        )
      );
    } catch (\SoapFault $e) {
      throw new \Exception('Failed to connect to the vCenter server.');
    }

    // Setup the SOAP service instance
    $msg = array();
    $msg['_this'] = new \Soapvar ("ServiceInstance", XSD_STRING, "ServiceInstance");
    $result = $this->client('RetrieveServiceContent', $msg);
    $this->service = $result->returnval;

    // Login to the vCenter Server
    $msg = array();
    $msg['_this'] = $this->service('sessionManager');
    $msg['userName'] = $this->user;
    $msg['password'] = $this->pass;
    try{
      $result = $this->client('login', $msg);
      $this->usersession = $result->returnval;
    } catch (\SoapFault $e) {
      throw new \Exception('Failed to login to the vCenter server.');
    }
    return $result;
  }

  public function client($method, $message) {
    return $this->client->$method($message);
  }

  public function service($service) {
    return $this->service->$service;
  }
}
