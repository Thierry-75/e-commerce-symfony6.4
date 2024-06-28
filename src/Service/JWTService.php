<?php

namespace App\Service;



class JWTService
{

    /**
     * Génération Du jwt 
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param int $validity
     * return string
     * 
     */

    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {

        if ($validity > 0) {

            $now = new \DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }
        // encode base64
        $base64Header = base64_encode(json_encode($header));
        $base64PayLoad = base64_encode(json_encode($payload));

        //nettoyage
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64PayLoad = str_replace(['+', '/', '='], ['-', '_', ''], $base64PayLoad);

        //generate signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64PayLoad, $secret, true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);
        //create token

        $jwt = $base64Header . '.' . $base64PayLoad . '.' . $base64Signature;

        return $jwt;
    }

    public function isValid(string $token): bool
    {
        return preg_match('/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/', $token) === 1;
    }

    public function getPayload(string $token): array
    {
        // extrait du token
        $array = explode('.', $token);
        // decodage token
        $payload = json_decode(base64_decode($array[1]), true);

        return $payload;
    }

    public function getHeader(string $token): array
    {
        // extrait du token
        $array = explode('.', $token);
        // decodage token
        $header = json_decode(base64_decode($array[0]), true);

        return $header;
    }

    // date valid
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new \DateTimeImmutable();
        return $payload['exp'] < $now->getTimestamp();
    }

    // signature
    public function check(string $token, string $secret)
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        $verifToken = $this->generate($header, $payload, $secret,0);
        return $token === $verifToken;
    }
}
