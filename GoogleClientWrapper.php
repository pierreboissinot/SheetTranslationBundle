<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 06/01/18
 * Time: 14:07
 */

namespace PierreBoissinot\SheetTranslationBundle;


use Google_Client;
use Google_Service_Sheets;

class GoogleClientWrapper
{
    /**
     * @var string
     */
    private $applicationName;
    /**
     * @var string
     */
    private $credentialsPath;
    /**
     * @var string
     */
    private $clientSecretPath;

    /**
     * @var array
     */
    private $scopes;

    function __construct()
    {
        $this->applicationName = 'Google Sheet API PHP used for translation bundle';
        $this->credentialsPath = '~/.credentials/sheets.googleapis.com-php-quickstart.json';
        $this->clientSecretPath = __DIR__ . '/client_secret.json';
        $this->scopes = implode(
            ' ',
            array(
                Google_Service_Sheets::SPREADSHEETS)
        );
    }

    /**
     * Returns an authorized API client.
     * @return Google_Client the authorized client object
     */
    function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName($this->applicationName);
        $client->setScopes($this->scopes);
        $client->setAuthConfig($this->clientSecretPath);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory($this->clientSecretPath);
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

            // Store the credentials to disk.
            if (!file_exists(dirname($credentialsPath))) {
                mkdir(dirname($credentialsPath), 0700, true);
            }
            file_put_contents($credentialsPath, json_encode($accessToken));
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }
        return $client;
    }

    /**
     * Expands the home directory alias '~' to the full path.
     * @param string $path the path to expand.
     * @return string the expanded path.
     */
    function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}