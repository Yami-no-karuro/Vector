<?php 
namespace Vector\Functions;
use Exception;

if (!defined('NO_DIRECT_ACCESS')) { 
    header('HTTP/1.0 403 Forbidden');
    echo '403 Forbidden';
    die(); 
}

class HttpClientException extends Exception {}
class HttpClient {

    /**
	 * @package Vector
	 * Vector\Functions\HttpClient::remote_put()
	 * @param {string} $uri
     * @param {json} $data
     * @param {array} $headers
	 * @param {bool} $return_transfer
	 * @param {bool} $follow_location
	 * @param {bool} $verify_host
	 * @param {bool} $verify_peer
	 */
    public static function remote_put(string $uri, $data, $headers = false, bool $return_transfer = true, bool $follow_location = true, bool $verify_host = false, bool $verify_peer = false) {
        $data_size = strlen($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        if (false !== $headers) { curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return_transfer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify_host);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_peer);
        $output = curl_exec($ch);
        if (!$output) { 
            throw new HttpClientException;
            return; 
        }
        curl_close($ch);
        return $output;
    }

    /**
	 * @package Vector
	 * Vector\Functions\HttpClient::remote_post()
	 * @param {string} $uri
     * @param {json} $data
     * @param {array} $headers
	 * @param {bool} $return_transfer
	 * @param {bool} $follow_location
	 * @param {bool} $verify_host
	 * @param {bool} $verify_peer
	 */
    public static function remote_post(string $uri, $data, $headers = false, bool $return_transfer = true, bool $follow_location = true, bool $verify_host = false, bool $verify_peer = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $uri);
        if (false !== $headers) { curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return_transfer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify_host);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_peer);
        $output = curl_exec($ch);
        if (!$output) { 
            throw new HttpClientException;
            return; 
        }
        curl_close($ch);
        return $output;
    }

    /**
	 * @package Vector
	 * Vector\Functions\HttpClient::remote_get()
	 * @param {string} $uri
     * @param {array} $headers
	 * @param {bool} $return_transfer
	 * @param {bool} $follow_location
	 * @param {bool} $verify_host
	 * @param {bool} $verify_peer
	 */
    public static function remote_get(string $uri, $headers = false, bool $return_transfer = true, bool $follow_location = true, bool $verify_host = false, bool $verify_peer = false) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        if (false !== $headers) { curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $return_transfer);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verify_host);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_peer);
        $output = curl_exec($ch);
        if (!$output) { 
            throw new HttpClientException;
            return; 
        }
        curl_close($ch);
        return $output;
    }

}
