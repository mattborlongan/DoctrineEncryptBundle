<?php

namespace TDM\DoctrineEncryptBundle\Encryptors;

/**
 * Class for AES-256 Prefixed encryption
 * 
 * @author Errin Pace
 */
class AES256PrefixedEncryptor implements EncryptorInterface {

    const CIPHER = MCRYPT_RIJNDAEL_128;
    const MODE = MCRYPT_MODE_CBC;

    /**
     * Prefix to indicate if data is encrypted
     * @var string
     */
    private $prefix;

    /**
     * Secret key for aes algorythm
     * @var string
     */
    private $secretKey;

    /**
     * Secret key for aes algorythm
     * @var string
     */
    private $systemSalt;

    /**
     *
     * @var int
     */
    private $iv_size;

    /**
     * Initialization of encryptor
     * @param string $key 
     */
    public function __construct($key, $systemSalt, $encryptedPrefix) {
        $this->secretKey = $this->convertKey($key);
        $this->systemSalt = $systemSalt;
        $this->prefix = $encryptedPrefix;
        $this->iv_size = mcrypt_get_iv_size(self::CIPHER, self::MODE);
    }

    /**
     * Implementation of EncryptorInterface encrypt method
     * @param string $data
     * @param bool Deterministic
     * @return string
     */
    public function encrypt($data, $deterministic) {

        $iv = $this->determineIV($deterministic);

        // Encrypt plaintext data with given parameters
        $encrypted = mcrypt_encrypt(self::CIPHER, $this->secretKey, $this->systemSalt . $data, self::MODE, $iv);

        // Encode data with MIME base64
        $base64_encoded = base64_encode($iv . $encrypted);

        // Strip NULL-bytes from the end of the string
        $rtrimmed = rtrim($base64_encoded, "\0");

        return $this->prefix . $rtrimmed;
    }

    /**
     * Implementation of EncryptorInterface decrypt method
     * @param string $data
     * @param bool Deterministic
     * @return string 
     */
    public function decrypt($data, $deterministic) {
        // Return data if not annotated as encrypted
        if (strncmp($this->prefix, $data, strlen($this->prefix)) !== 0)
            return $data;

        // Strip annotation and decode data encoded with MIME base64
        $base64_decoded = base64_decode(substr($data, strlen($this->prefix)));

        // Split Initialization Vector
        $iv = substr($base64_decoded, 0, $this->iv_size);
        $iv_removed = substr($base64_decoded, $this->iv_size);

        // return decrypted, de-salted, and trimed value
        return rtrim($this->removeSalt(mcrypt_decrypt(self::CIPHER, $this->secretKey, $iv_removed, self::MODE, $iv)), "\0");
    }

    /**
     * 
     * @param string $secretKey
     * @return string
     */
    private function convertKey($secretKey) {
        return pack('H*', hash('sha256', $secretKey));
    }

    /**
     * Return an initialization vector (IV) from a random source
     * @param type $deterministic
     * @return string Initialization Vecotr
     */
    private function determineIV($deterministic) {
        return $deterministic ? str_repeat("\0", $this->iv_size) : mcrypt_create_iv($this->iv_size, MCRYPT_DEV_URANDOM);
    }

    /**
     * Strips the salt off the decrypted value (if it is present)
     * @param string $saltedDecrypted
     * @return string
     */
    private function removeSalt($saltedDecrypted) {
        $systemSaltLength = strlen($this->systemSalt);
        if (substr($saltedDecrypted, 0, $systemSaltLength) === $this->systemSalt) {
            return substr($saltedDecrypted, $systemSaltLength);
        } else {
            return $saltedDecrypted;
        }
    }

}
