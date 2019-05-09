<?php


namespace App\Engineroom;

use GuzzleHttp\Client as GuzzleClient;

class Client extends GuzzleClient
{
    protected $engineroomApiUri;
    protected $engineroomEmail;
    protected $engineroomPassword;

    protected static $token;
    protected static $token_expiry;

    public function __construct(string $apiUri, string $apiEmail, string $apiPassword, $config = [])
    {
        $this->engineroomApiUri = $apiUri;
        $this->engineroomEmail = $apiEmail;
        $this->engineroomPassword = $apiPassword;

        $config['base_uri'] = $apiUri;
        $config['verify'] = false;

        parent::__construct($config);
    }

    public function request($method, $uri = '', array $options = [])
    {
        $headers = $options['headers'] ?? [];
        $headers['Authorization'] = $this->getAuthHeaderValue();

        $options['headers'] = $headers;

        return parent::request($method, $uri, $options);
    }

    protected function getToken()
    {
        if (
            !static::$token ||
            !static::$token_expiry ||
            !(static::$token_expiry < time())
        ) {
            $this->requestToken();
        }

        return static::$token;
    }

    protected function requestToken(): void
    {
        $response = parent::request(
            'POST',
            'auth/logon',
            [
                'form_params' => [
                    'email' => $this->engineroomEmail,
                    'password' => $this->engineroomPassword
                ]
            ]
        );

        $contents = json_decode($response->getBody(), true);

        static::$token = $contents['token'];
        static::$token_expiry = $contents['expiry'];
    }

    public function getAuthHeaderValue(): string
    {
        return 'Bearer ' . $this->getToken();
    }
}
