<?php
/**
 *
 * A PHP class to interface with gearmand's admin interface
 * This is a programable alternative to telneting to the
 * gearmand process (default port TCP 4730) and issuing the
 * commands 'status' and 'workers'
 * It is assumed that you have gearmand installed and running
 * as well, that you have the gearman pecl module install and enabled in
 * php.ini
 *
 * @license: GPLv3
 * @author: Jonathan Cutrer
 * @website: http://www.pronique.com
 *
 * If you find this tool useful consider linking to http:/www.pronique.com/
 * Or donate $5 via paypal, http://www.pronique.com/donate
 *
 *
 * Reference: http://gearman.org/index.php?id=protocol
 *
 */
 
class gearmandAdmin {
 
  private $socketHandle;
  private $host = '127.0.0.1';
  private $port = '4730';
  private $timeout = '5';
  public $gearmandstatus = false;
  public $errorno = null;
  public $errormsg = null;
   
  function __construct( $host=null, $port=null, $timeout=null ) {
        if ( $host ) { $this->host = $host;} 
        if ( $port ) { $this->port = $port;} 
        if ( $timeout ) { $this->timeout = $timeout;} 
        $this->connect();
  }
 
 
  /**
   * returns array of status
   *
   */
  function getStatus() {
    if(!$this->socketHandle) return false;
    $response = $this->getStatusRaw();
    //TODO build $response in a structured array
    $count = 0;
    $lines = explode("\n", $response );
    foreach( $lines as $line ) {
        if ( $line =='.' ) { break; }
        $parts = explode("\t", $line);
        $status[$count]['function'] = $parts[0];
        $status[$count]['total'] = $parts[1];
        $status[$count]['running'] = $parts[2];
        $status[$count]['workers'] = $parts[3];
        $count++;
    }
    return $status;
 
  }
 
 
  /**
   * send the 'status' command to the server and return the raw response
   *
   */
  function getStatusRaw() {
     
    return $this->send('status');
   
  }
 
 
  /**
   * return array of workers
   *
   */
  function getWorkers() {
 
    $response = $this->getWorkersRaw();
    $lines = explode("\n", $response );
    //TODO build $response in a structured array
    $count = 0;
    foreach( $lines as $line ) {
        if ( $line =='.' ) { break; }
        $parts = explode(" ", $line);
        $workers[$count]['descriptor'] = $parts[0];
        $workers[$count]['ip'] = $parts[1];
        $workers[$count]['clientid'] = $parts[2];
        $func_marker = false;
        foreach( $parts as $part) {
            if ( $func_marker == true ) {
              $workers[$count]['functions'][] = $part;
            }
          if ($part == ':') { $func_marker = 1; }
        }
        $count++;
    }
    return $workers;
 
  }
 
 
  /**
   * send the 'workers' command to the server and return the raw response
   *
   */
  function getWorkersRaw() {
 
    return $this->send('workers');
 
  }
 
 
  /**
   * connect to gearmand using tcp
   *
   */
  function connect() {
    try {
      
      $this->socketHandle = fsockopen( $this->host, $this->port, $errno, $errstr, $this->timeout);
    } catch (Exception $e) {
      //echo "Could not connect to gearmand server\n";
      //echo "$errstr ($errno)\n";
      $this->errorno = $errno;
      $this->errormsg = $errstr($errno);
    }
  }
 
  /**
   * disconnect from gearmand
   *
   */
  function disconnect() { 
    if($this->socketHandle) fclose( $this->socketHandle );
  }
  
  /**
   * send a command to the socket 
   *
   */
  function send( $cmd ) {
    $data = '';
    fwrite($this->socketHandle, $cmd . "\r\n");
    while (!feof($this->socketHandle)) {
         $data .= fgets($this->socketHandle, 1024);
         if ( preg_match("/\n\.$/i", $data ) ) { break; }
    }
    return $data;
  }
}
?>
