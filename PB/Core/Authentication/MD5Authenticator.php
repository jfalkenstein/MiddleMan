<?php

namespace PB\Core\Authentication;

use PB\Core\Interfaces\IAuthenticator;
use PB\Core\Interfaces\IConfigManager;
use PB\Core\Request\Request;

/**
 * Handles the authentication for the request using md5 hashing.
 * 
 * How the authentication string is made:
 * -----
 * 
 * The authentication string that this authenticator expects is an MD5 hash of
 * the following (in this order):
 * 
 *  *   <strong>Date</strong>: Format of dm-1yyyy. For example, 8/7/2016 would be
 *      772016
 *  *   <strong>Passphrase</strong>: A string known by client and server, set in 
 *      the config files for both.
 *  *   <strong>Module</strong>: The name of the module, which would be the second
 *      url segment after the ip address.
 * 
 * As an example, if today was 8/7/2016, the Passphrase was "PASSWORD" and the
 * url was:
 *
 *     http://ip.ad.dr.ess:port/Demo/GetInfo
 *
 * The secret string would be an MD5 hash of "772016PASSWORDGetInfo".
 * 
 * If the request properly authenticates, then a return token is generated and 
 * returned with the response so that the client can authenticate the response.
 * The return token is an MD5 hash of the following, in this order:
 * 
 *  *   <strong>The original hashed secret string</strong>
 *  *   <strong>Return stalt</strong>: A string known by client and server,
 *      set in the config files for both.
 * 
 * @author jfalkenstein
 */
class MD5Authenticator implements IAuthenticator {
    private $config; /**< The Interfaces::IConfigManager accessed by the authenticator.*/
    
    public function __construct(IConfigManager $config) {
        $this->config = $config;
    }
    
    /**
     * Responsible for authenticating the request.
     * 1. Checks if there bypass is set to true in the configuration file.
     * 2. Obtains the passphrase, authentication string key, salt, and return salt from the config file.
     * 3. It creates an MD5 hash.
     * 4. It creates the return token of the first hash and the return salt (concatenated).
     * 5. It compares the first hash with the recevied authentication string from the request.
     *      * If they are equal, it returns the return token.
     *      * If they are not equal, it returns false.
     *
     * @param Request $request
     * @return boolean|string
     */
    public function authenticate(Request $request) {
        $bypass = $this->config->getValue(['authenticator','bypass']);
        $passphraseToSearch = $this->config->getPassphrase();
        $authKeyName = $this->config->getValue(['authenticator','authenticationStringKey']);
        $salt = $this->config->getSalt();
        $returnSalt = $this->config->getReturnSalt();
        $hash = md5($salt . $passphraseToSearch . $request->Module);
        
        $token = md5($hash . $returnSalt);
        if($bypass === true){
            return $token;
        }
        $authString = $request->Data[$authKeyName];
        if($hash === $authString){
            return $token;
        }else{
            return false;
        }
    }
}
