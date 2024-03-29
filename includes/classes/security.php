<?php
class security{
	
	public function action($action){
		$sql = "INSERT INTO `passman_audit` VALUES(null, :user, :action, :cat, :time)";
		db::Query($sql, array(':user'=>$_SESSION['user'], ':action'=>security::encrypt($action), ':cat'=>$_POST['v'], ':time'=>time()));
	}
	
	public function encryptStringForComparison($salty, $password){
		$salt = sha1($salty);
		$salt = substr($salt, 0, (count($salt) / 2));
        $hash = base64_encode( sha1($password . $salty, true) . $salt );
        return $hash;
	}
	
	/**
     * @var string $cipher The mcrypt cipher to use for this instance
     */
    public $cipher = MCRYPT_BlOWFISH;

    /**
     * @var int $mode The mcrypt cipher mode to use
     */
    public $mode = MCRYPT_MODE_CBC;

    /**
     * Decrypt the data with the provided key
     *
     * @param string $data The encrypted datat to decrypt
     * @param string $key  The key to use for decryption
     * 
     * @returns string|false The returned string if decryption is successful
     *                           false if it is not
     */
    public function decrypt($data) {
		$key = @$_SESSION['key'];
        $key = security::stretch($key);
        $iv = security::getIv($data, $key);
        if ($iv === false) {
            return false; //Invalid IV, so we can't continue
        }
        $de = mcrypt_decrypt(MCRYPT_BlOWFISH, $key, $data, MCRYPT_MODE_CBC, $iv);
        if (!$de || strpos($de, ':') === false) return false;

        list ($hmac, $data) = explode(':', $de, 2);
        $data = rtrim($data, "\0");

        if ($hmac != hash_hmac('sha1', $data, $key)) {
            return false;
        }
        return $data;
    }

    /**
     * Encrypt the supplied data using the supplied key
     * 
     * @param string $data The data to encrypt
     * @param string $key  The key to encrypt with
     *
     * @returns string The encrypted data
     */
    public function encrypt($data) {
		$key = @$_SESSION['key'];
        $key = security::stretch($key);
        $data = hash_hmac('sha1', $data, $key) . ':' . $data;

        $iv = security::generateIv();
        $enc = mcrypt_encrypt(MCRYPT_BlOWFISH, $key, $data, MCRYPT_MODE_CBC, $iv);

        return security::storeIv($enc, $iv, $key);
    }

    /**
     * Generate an Initialization Vector based upon the class's cypher and mode
     *
     * @returns string The initialization vector
     */
    protected function generateIv() {
        $size = mcrypt_get_iv_size(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
        return mcrypt_create_iv($size, MCRYPT_RAND);
    }

    /**
     * Extract a stored initialization vector from an encrypted string
     *
     * This will shorten the $data pramater by the removed vector length.
     * 
     * @see Encryption::storeIv()
     *
     * @param string &$data The encrypted string to process.
     * @param string $key   The supplied key to extract the IV with
     *
     * @returns string The initialization vector that was stored
     */
    protected function getIv(&$data, $key) {
        $size = mcrypt_get_iv_size(MCRYPT_BlOWFISH, MCRYPT_MODE_CBC);
        $iv = '';
        for ($i = $size - 1; $i >= 0; $i--) {
            $pos = hexdec($key[$i]);
            $iv = substr($data, $pos, 1) . $iv;
            $data = substr_replace($data, '', $pos, 1);
        }
        if (strlen($iv) != $size) {
            return false;
        }
        return $iv;
    }

    /**
     * Store the Initialization Vector inside the encrypted string.
     *
     * We will need the IV later to decrypt the data, so we need to
     * make it available.  We don't want to just append it, since that
     * could open MITM style attacks on the data.  So we'll hide it 
     * using the key to determine exactly how to hide it.  That way,
     * without knowing the key, it should be impossible to get the IV.
     *
     * @param string $data The data to hide the IV within
     * @param string $iv   The IV to hide
     * @param string $key  The key to use to hide the IV with
     *
     * @returns string The $data parameter with the hidden IV
     */
    protected function storeIv($data, $iv, $key) {
        for ($i = 0; $i < strlen($iv); $i++) {
            $offset = hexdec($key[$i]);
            $data = substr_replace($data, $iv[$i], $offset, 0);
        }
        return $data;
    }

    /**
     * Stretch the key using a simple hmac based stretching algorythm
     *
     * We want to use sha1 here over something stronger since Blowfish
     * expects a key between 4 and 56 bytes.  Sha1 produces a 40 byte
     * hash, so it should be good for these purposes.  This also allows
     * an arbitrary key of any length to be used for encryption.
     *
     * Another benefit of streching the kye is that it actually slows
     * down any potential brute force attacks. 
     *
     * We use 5000 runs for the stretching since it's a good balance
     * between brute force protection and system load.  We could increase
     * this if we were paranoid, but it shouldn't be necessary.
     *
     * @see http://en.wikipedia.org/wiki/Key_stretching
     *
     * @param string $key The key to stretch
     *
     * @returns string A 40 character hex string with the stretched key
     */
    protected function stretch($key) {
        $hash = sha1($key);
        $runs = 0;
        do {
            $hash = hash_hmac('sha1', $hash, $key);
        } while ($runs++ < 5000);
        return $hash;
    }
	
}
?>