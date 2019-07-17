<?php
namespace easyPlanning;

class Security{
    public static function secured_encrypt($data)
    {
        $first_key = base64_decode(SysConfig::FIRSTKEY);
        $second_key = base64_decode(SysConfig::SECONDKEY);
        
        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        $first_encrypted = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
        
        $output = base64_encode($iv . $second_encrypted . $first_encrypted);
        return $output;
    }
    
    public static function secured_decrypt($input)
    {
        $first_key = base64_decode(SysConfig::FIRSTKEY);
        $second_key = base64_decode(SysConfig::SECONDKEY);
        $mix = base64_decode($input);
        
        $method = "aes-256-cbc";
        $iv_length = openssl_cipher_iv_length($method);
        
        $iv = substr($mix, 0, $iv_length);
        $second_encrypted = substr($mix, $iv_length, 64);
        $first_encrypted = substr($mix, $iv_length + 64);
        
        $data = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
        $second_encrypted_new = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
        //$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);
        
        if (hash_equals($second_encrypted, $second_encrypted_new))
            return $data;
            
            return false;
    }
    
    public static function secured_decrypt_url($input){
        $string = $input;
        $string = urlencode($string);
        $string = str_replace("+", "%2B",$string);
        $string = urldecode($string);
        return self::secured_decrypt($string);
    }
}
?>