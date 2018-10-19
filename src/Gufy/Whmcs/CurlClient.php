<?php
namespace Gufy\Whmcs;

class CurlClient
{
    public function post($url, $postfields = array())
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));

            $response = curl_exec($ch);

            if (curl_error($ch)) {
                throw new Exception('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
            }

            curl_close($ch);

            return json_decode($response, true);
        } catch (Exception $e) {
            throw new Exception(
                sprintf("Error while calling %s URL, with message: %s", $url, $e->getMessage())
            );
        }
    }
}
