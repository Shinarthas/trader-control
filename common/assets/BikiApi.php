<?php
namespace common\assets;



class BikiApi
{
    protected $base = 'https://openapi.biki.com/'; // /< REST endpoint for the currency exchange
    protected $wapi = 'https://openapi.biki.com/'; // /< REST endpoint for the withdrawals
    protected $stream = 'wss://ws.biki.com/kline-api/ws'; // /< Endpoint for establishing websocket connections
    protected $api_key; // /< API key that you created in the binance website member area
    protected $api_secret; // /< API secret that was given to you when you created the api key
    protected $depthCache = []; // /< Websockets depth cache
    protected $depthQueue = []; // /< Websockets depth queue
    protected $chartQueue = []; // /< Websockets chart queue
    protected $charts = []; // /< Websockets chart data
    protected $curlOpts = []; // /< User defined curl coptions
    protected $info = [
        "timeOffset" => 0,
    ]; // /< Additional connection options
    protected $proxyConf = null; // /< Used for story the proxy configuration
    protected $caOverride = false; // /< set this if you donnot wish to use CA bundle auto download feature
    protected $transfered = 0; // /< This stores the amount of bytes transfered
    protected $requestCount = 0; // /< This stores the amount of API requests
    private $httpDebug = false; // /< If you enable this, curl will output debugging information
    private $subscriptions = []; // /< View all websocket subscriptions
    private $btc_value = 0.00; // /< value of available assets
    private $btc_total = 0.00;

    static $proxy=[];


    public function __construct()
    {
        $param = func_get_args();
        switch (count($param)) {
            case 0:
                $this->setupApiConfigFromFile();
                $this->setupProxyConfigFromFile();
                $this->setupCurlOptsFromFile();
                break;
            case 1:
                $this->setupApiConfigFromFile($param[0]);
                $this->setupProxyConfigFromFile($param[0]);
                $this->setupCurlOptsFromFile($param[0]);
                break;
            case 2:
                $this->api_key = $param[0];
                $this->api_secret = $param[1];
                break;
            default:
                echo 'Please see valid constructors here: https://github.com/jaggedsoft/php-biki-api/blob/master/examples/constructor.php';
        }
    }
    /**
     * If no paramaters are supplied in the constructor, this function will attempt
     * to load the api_key and api_secret from the users home directory in the file
     * ~/jaggedsoft/php-biki-api.json
     *
     * @param $file string file location
     * @return null
     */
    private function setupApiConfigFromFile(string $file = null)
    {
        $file = is_null($file) ? getenv("HOME") . "/.config/jaggedsoft/php-biki-api.json" : $file;

        if (empty($this->api_key) === false || empty($this->api_key) === false) {
            return;
        }
        if (file_exists($file) === false) {
            echo "Unable to load config from: " . $file . PHP_EOL;
            echo "Detected no API KEY or SECRET, all signed requests will fail" . PHP_EOL;
            return;
        }
        $contents = json_decode(file_get_contents($file), true);
        $this->api_key = isset($contents['api-key']) ? $contents['api-key'] : "";
        $this->api_secret = isset($contents['api-secret']) ? $contents['api-secret'] : "";
    }
    /**
     * If no paramaters are supplied in the constructor for the proxy confguration,
     * this function will attempt to load the proxy info from the users home directory
     * ~/jaggedsoft/php-biki-api.json
     *
     * @return null
     */
    private function setupProxyConfigFromFile(string $file = null)
    {
        $file = is_null($file) ? getenv("HOME") . "/.config/jaggedsoft/php-biki-api.json" : $file;

        if (is_null($this->proxyConf) === false) {
            return;
        }
        if (file_exists($file) === false) {
            echo "Unable to load config from: " . $file . PHP_EOL;
            echo "No proxies will be used " . PHP_EOL;
            return;
        }
        $contents = json_decode(file_get_contents($file), true);
        if (isset($contents['proto']) === false) {
            return;
        }
        if (isset($contents['address']) === false) {
            return;
        }
        if (isset($contents['port']) === false) {
            return;
        }
        $this->proxyConf['proto'] = $contents['proto'];
        $this->proxyConf['address'] = $contents['address'];
        $this->proxyConf['port'] = $contents['port'];
        if (isset($contents['user'])) {
            $this->proxyConf['user'] = isset($contents['user']) ? $contents['user'] : "";
        }
        if (isset($contents['pass'])) {
            $this->proxyConf['pass'] = isset($contents['pass']) ? $contents['pass'] : "";
        }
    }
    private function setupCurlOptsFromFile(string $file = null)
    {
        $file = is_null($file) ? getenv("HOME") . "/.config/jaggedsoft/php-biki-api.json" : $file;

        if (count($this->curlOpts) > 0) {
            return;
        }
        if (file_exists($file) === false) {
            echo "Unable to load config from: " . $file . PHP_EOL;
            echo "No curl options will be set" . PHP_EOL;
            return;
        }
        $contents = json_decode(file_get_contents($file), true);
        $this->curlOpts = isset($contents['curlOpts']) && is_array($contents['curlOpts']) ? $contents['curlOpts'] : [];
    }

    /**
     * depth get Market depth
     *
     * $depth = $api->depth("ETHBTC");
     *
     * @param $symbol string the symbol to get the depth information for
     * @param $limit int set limition for number of market depth data
     * @return array with error message or array of market depth
     * @throws \Exception
     */
    public function depth(string $symbol, string $type='step0')
    {

        if (isset($symbol) === false || is_string($symbol) === false) {
            // WPCS: XSS OK.
            echo "asset: expected bool false, " . gettype($symbol) . " given" . PHP_EOL;
        }
        $json = $this->httpRequest("open/api/market_dept", "GET", [
            "symbol" => $symbol,
            "type" => $type,
        ]);
        if (isset($this->info[$symbol]) === false) {
            $this->info[$symbol] = [];
        }
        $this->info[$symbol]['firstUpdate'] = $json['lastUpdateId'];
        return $this->depthData($symbol, $json);
    }

    /**
     * httpRequest curl wrapper for all http api requests.
     * You can't call this function directly, use the helper functions
     *
     * @see buy()
     * @see sell()
     * @see marketBuy()
     * @see marketSell() $this->httpRequest( "https://api.binance.com/api/v1/ticker/24hr");
     *
     * @param $url string the endpoint to query, typically includes query string
     * @param $method string this should be typically GET, POST or DELETE
     * @param $params array addtional options for the request
     * @param $signed bool true or false sign the request with api secret
     * @return array containing the response
     * @throws \Exception
     */
    private function httpRequest(string $url, string $method = "GET", array $params = [], bool $signed = false)
    {
        if (function_exists('curl_init') === false) {
            throw new \Exception("Sorry cURL is not installed!");
        }

        if ($this->caOverride === false) {
            if (file_exists(getcwd() . '/ca.pem') === false) {
                $this->downloadCurlCaBundle();
            }
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

        curl_setopt($curl, CURLOPT_VERBOSE, $this->httpDebug);
        $query = http_build_query($params, '', '&');

        // signed with params
        if ($signed === true) {
            if (empty($this->api_key)) {
                throw new \Exception("signedRequest error: API Key not set!");
            }

            if (empty($this->api_secret)) {
                throw new \Exception("signedRequest error: API Secret not set!");
            }

            $base = $this->base;
            $ts = (microtime(true) * 1000) + $this->info['timeOffset'];
            $params['timestamp'] = number_format($ts, 0, '.', '');
            if (isset($params['wapi'])) {
                unset($params['wapi']);
                $base = $this->wapi;
            }
            $query = http_build_query($params, '', '&');
            $signature = hash_hmac('sha256', $query, $this->api_secret);
            if ($method === "POST") {
                $endpoint = $base . $url;
                $params['signature'] = $signature; // signature needs to be inside BODY
                $query = http_build_query($params, '', '&'); // rebuilding query
            } else {
                $endpoint = $base . $url . '?' . $query . '&signature=' . $signature;
            }


            curl_setopt($curl, CURLOPT_URL, $endpoint);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,'Content-Type: application/x-www-form-urlencoded',
            ));
        }
        // params so buildquery string and append to url
        else if (count($params) > 0) {
            curl_setopt($curl, CURLOPT_URL, $this->base . $url . '?' . $query);

        }
        // no params so just the base url
        else {
            curl_setopt($curl, CURLOPT_URL, $this->base . $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'X-MBX-APIKEY: ' . $this->api_key,'Content-Type: application/x-www-form-urlencoded',
            ));
        }
        // Post and postfields
        if ($method === "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        }
        // Delete Method
        if ($method === "DELETE") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // PUT Method
        if ($method === "PUT") {
            curl_setopt($curl, CURLOPT_PUT, true);
        }

        // proxy settings
        if (is_array($this->proxyConf)) {
            curl_setopt($curl, CURLOPT_PROXY, $this->getProxyUriString());
            if (isset($this->proxyConf['user']) && isset($this->proxyConf['pass'])) {
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxyConf['user'] . ':' . $this->proxyConf['pass']);
            }
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        // headers will proceed the output, json_decode will fail below
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);

        // set user defined curl opts last for overriding
        foreach ($this->curlOpts as $key => $value) {
            curl_setopt($curl, constant($key), $value);
        }

        if ($this->caOverride === false) {
            if (file_exists(getcwd() . '/ca.pem') === false) {
                $this->downloadCurlCaBundle();
            }
        }

        $output = curl_exec($curl);
        // Check if any error occurred
        if (curl_errno($curl) > 0) {
            // should always output error, not only on httpdebug
            // not outputing errors, hides it from users and ends up with tickets on github
            echo 'Curl error: ' . curl_error($curl) . "\n";
            return [];
        }
        curl_close($curl);
        $json = json_decode($output, true);
        if (isset($json['msg'])) {
            // should always output error, not only on httpdebug
            // not outputing errors, hides it from users and ends up with tickets on github
            echo "signedRequest error: {$output}" . PHP_EOL;
        }
        $this->transfered += strlen($output);
        $this->requestCount++;
        return $json;
    }
    private function depthData(string $symbol, array $json)
    {
        $bids = $asks = [];
        foreach ($json['bids'] as $obj) {
            $bids[$obj[0]] = $obj[1];
        }
        foreach ($json['asks'] as $obj) {
            $asks[$obj[0]] = $obj[1];
        }
        return $this->depthCache[$symbol] = [
            "bids" => $bids,
            "asks" => $asks,
        ];
    }


}