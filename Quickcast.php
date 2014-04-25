<?hh // strict

namespace Quickcast;

class Quickcast
{
    protected Config $config;
    public string $token;
    public string $tokenFile = 'token.txt';
    public string $useragent = 'QuickCast/1.13 CFNetwork/673.2.1 Darwin/13.1.0 (x86_64) (MacBookPro10%2C1)';
    protected Map $_data;

    function __construct(Config $config): void
    {
        $this->_config = $config;

        $this->s = curl_init();
        $this->connect();
        $this->doSignIn();
    }

    public function doSignIn(): void
    {
        if(!$this->getToken()){
            $this->_signIn();
        } else {
            $this->_signInWithToken();
        }

    }

    public function getToken(): ?string
    {
        if(!$this->token){
            $this->token = file_get_contents($this->tokenFile);
            if($this->token === false){
                return null;
            }
        }

        return $this->token;
    }

    protected function _signIn(): void
    {
        $endpoint = $this->getConfig()->getUrl();
        $endpoint .= '/api/v2/users/signin';
        $fields = sprintf('username=%s&password=%s',
                    $this->getConfig()->getUsername(),$this->getConfig()->getPassword()
                  );

        curl_setopt($this->s,CURLOPT_POST, 2);
        curl_setopt($this->s,CURLOPT_POSTFIELDS, $fields);

        $this->auth = json_decode($this->exec($endpoint));
        $this->token = $this->auth->token;
        file_put_contents($this->tokenFile, $this->token);
    }

    protected function _signInWithToken(): void
    {
        $endpoint = $this->getConfig()->getUrl();
        $endpoint .= '/api/v2/users/userbytoken';

        curl_setopt($this->s, CURLOPT_HTTPHEADER, array('token: ' . $this->token));
        $this->exec($endpoint);
    }

    public function getData(): Map
    {
        if(!$this->_data){
            //return a json-decoded array of casts
            $endpoint = $this->getConfig()->getUrl();
            $endpoint .= '/api/v2/users/casts/1';

            curl_setopt($this->s, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($this->s, CURLOPT_HTTPHEADER, array('token: ' . $this->token));

            $this->_data = new Map(json_decode($this->exec($endpoint),true));
        }

        return $this->_data;
    }

    public function getCasts(): Map
    {
        $data = $this->getData();
        return new Map($data->get('casts'));
    }

    public function connect(): void
    {
        if(!$this->s && !$this->getConfig()->getUrl()){
            die('You cannot establish a connection without a valid url.');
        }
        
        $this->_timeout            = 300;
        $this->_followlocation     = true;
        $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';

        curl_setopt($this->s,CURLOPT_TIMEOUT,$this->_timeout);
        curl_setopt($this->s,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->s,CURLOPT_FOLLOWLOCATION,$this->_followlocation);
        curl_setopt($this->s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
        curl_setopt($this->s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);


        curl_setopt($this->s,CURLOPT_USERAGENT,$this->useragent);

        $this->_status = curl_getinfo($this->s,CURLINFO_HTTP_CODE);
    }

    public function exec($url): ?string
    {
        if($url){
            curl_setopt($this->s,CURLOPT_URL,$url);
        } else {
            curl_setopt($this->s,CURLOPT_URL,$this->getConfig()->getUrl());
        }
        return curl_exec($this->s);
    }

    public function getConfig(): Config
    {
        return $this->_config;
    }

    public function __destruct(): void
    {
        curl_close($this->s);
    }

}

class Config
{
    protected array $options;

    /**
     * Read the config document
     */
    public function __construct(): void
    {
        $file = file_get_contents('quickcast.json');
        if(!$file){
            throw new Exception('A configuration json file was not found. Exiting.');
        }
        $this->_options = new ImmMap(json_decode($file,true));
    }

    public function getOptions(): ImmMap
    {
        return $this->_options;
    }

    public function getUrl(): string
    {
        return $this->getOptions()->get('url');
    }

    public function getUsername(): string
    {
        return $this->getOptions()->get('username');
    }

    public function getPassword(): string
    {
        return $this->getOptions()->get('password');
    }
}
