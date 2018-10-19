<?php namespace Gufy\Whmcs;

use Gufy\Whmcs\CurlClient;

class Whmcs
{
    /**
     * Execute API action.
     *
     * @param  string $action
     * @param  array  $params
     * @return stdClass
     * @throws Exception
     */
    public function execute($action, $params = [])
    {
        // Initiate
        $params['username']     = \Config::get('whmcs::username');
        $params['responsetype'] = \Config::get('whmcs::responsetype');
        $params['action']       = $action;

        $auth_type = \Config::get('whmcs::auth_type', 'password');

        switch ($auth_type) {
            case 'api':
                if (false === \Config::has('whmcs::password') || '' === \Config::get('whmcs::password')) {
                    throw new \Exception("Please provide api key for authentication");
                }

                $params['accesskey'] = \Config::get('whmcs::password');
                break;
            case 'password':
                if (false === \Config::has('password') || '' === \Config::get('password')) {
                    throw new \Exception("Please provide username password for authentication");
                }

                $params['password'] = md5(\Config::get('whmcs::password'));
                break;
        }

        $url = \Config::get('whmcs::url');
        // unset url
        unset($params['url']);

        $client   = new CurlClient;
        $response = $client->post($url, $params);

        try {
            return $this->processResponse($response);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Process API response.
     *
     * @param  mixed $response
     * @return stdClass
     * @throws Exception
     */
    public function processResponse($response)
    {
        if (isset($response['result']) && 'error' === $response['result']
            || isset($response['status']) && 'error' === $response['status']) {
            throw new \Exception("WHMCS Error : " . $response['message']);
        }

        return is_string($response) ? json_decode(json_encode($response)) : $response;
    }

    // using magic method
    public function __call($action, $params)
    {
        return $this->execute($action, $params);
    }
}
