<?php

namespace App\Lib\Mir;

/**
 * CloudFront SignedURL用ライブラリ	
 */
class MirSignedUrl
{
    // resource_path = https://drfp6lv8dn0r5.cloudfront.net/client_upload/2110/RU7ZuNYpjvbDJ2GVBbtI4kkZXsqbdyayuAPnzOT9.m4a
    public static function getSignedUrl($resource_path)
    {
        $private_key_filename = storage_path('cloudfront_signed_private_key.pem');

        $key_pair_id = config('_env.CLOUDFRONT_SIGNED_KEY_PAIR_ID');

        $expires = time() + config('_env.CLOUDFRONT_SIGNED_EXPIRE_SEC', 300);

        return self::getCannedPolicyStreamName(
            $resource_path,
            $private_key_filename,
            $key_pair_id,
            $expires
        );
    }

    private static function getCannedPolicyStreamName($resource_path, $private_key_filename, $key_pair_id, $expires)
    {
        //logger("getCannedPolicyStreamName resource_path={$resource_path} private_key_filename={$private_key_filename} key_pair_id={$key_pair_id} expires={$expires}");
        // this policy is well known by CloudFront, but you still need to sign it, since it contains your parameters
        $canned_policy = '{"Statement":[{"Resource":"' . $resource_path . '","Condition":{"DateLessThan":{"AWS:EpochTime":'. $expires . '}}}]}';

        //logger("canned_policy={$canned_policy}");
        // the policy contains characters that cannot be part of a URL, so we base64 encode it
        //$encoded_policy = MirSignedUrl::urlSafeBase64encode($canned_policy);
        //logger("encoded_policy={$encoded_policy}");

        // sign the original policy, not the encoded version
        $signature = self::rsaSha1Sign($canned_policy, $private_key_filename);
        //logger("signature={$signature}");

        // make the signature safe to be included in a URL
        $encoded_signature = self::urlSafeBase64encode($signature);
        //logger("encoded_signature={$encoded_signature}");

        // combine the above into a stream name
        $stream_name = self::createStreamName($resource_path, null, $encoded_signature, $key_pair_id, $expires);
        //logger("stream_name={$stream_name}");

        // URL-encode the query string characters to support Flash Player
        //$encoded_query_param = MirSignedUrl::encode_query_params($stream_name);
        return $stream_name;
    }

    private static function rsaSha1Sign($policy, $private_key_filename)
    {
        $signature = "";

        // load the private key
        $fp = fopen($private_key_filename, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);

        // compute signature
        openssl_sign($policy, $signature, $pkeyid);

        return $signature;
    }

    private static function urlSafeBase64encode($value)
    {
        $encoded = base64_encode($value);
        // replace unsafe characters +, = and / with the safe characters -, _ and ~
        return str_replace(
            ['+', '=', '/'],
            ['-', '_', '~'],
            $encoded
        );
    }

    private static function createStreamName($path, $policy, $signature, $key_pair_id, $expires)
    {
        // if the stream already contains query parameters, attach the new query parameters to the end
        // otherwise, add the query parameters
        $separator = strpos($path, '?') == FALSE ? '?' : '&';
        // the presence of an expires time means we're using a canned policy
        $result = $path . $separator . "Expires=" . $expires . "&Signature=" . $signature . "&Key-Pair-Id=" . $key_pair_id;

        // new lines would break us, so remove them
        return str_replace('\n', '', $result);
    }
}
