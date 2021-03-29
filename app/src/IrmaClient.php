<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use IRMA\Requestor;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Session;
use SilverStripe\Core\Environment;

class IrmaClient
{

    const SESSION_KEY = 'IRMA_DISCLOSURE';

    /** @var Client */
    private $client;

    /** @var Requestor */
    private $requestor;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => Environment::getEnv('IRMA_SERVER')]);
        $this->requestor = new Requestor(
            'MaxTestSite',
            'MaxTestSite',
            Environment::getEnv('IRMA_KEY')
        );
    }

    public function startSession(array $attributes)
    {
        try {
            $response = $this->client->post(
                'session',
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => Environment::getEnv('IRMA_API')
                    ],
                    'json' => [
                        "@context" => "https://irma.app/ld/request/disclosure/v2",
                        "disclose" => [
                            [
                                $attributes
                            ]
                        ]
                    ]
                ]
            );
        } catch (ClientException $ex) {
            var_dump($ex->getRequest()->getHeaders());
            echo $ex->getRequest()->getBody()->getContents();
            echo $ex->getResponse()->getBody()->getContents();
            die();
        }


        return json_decode($response->getBody()->getContents(), true);
    }

    public function result($token)
    {
        try {
            $response = $this->client->get(
                "session/$token/result",
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => Environment::getEnv('IRMA_API')
                    ]
                ]
            );
        } catch (ClientException $ex) {
            var_dump($ex->getRequest()->getHeaders());
            echo $ex->getRequest()->getBody()->getContents();
            echo $ex->getResponse()->getBody()->getContents();
            die();
        }


        $json = json_decode($response->getBody()->getContents(), true);

        if ($json['status'] === 'DONE' && $json['proofStatus'] === 'VALID') {
            foreach ($json['disclosed'] as $disclosed) {
                foreach ($disclosed as $claim ) {
                    $this->recordDisclosure($claim);
                }
            }
        }

        return $json;
    }

    public function getDisclosure($id)
    {
        $disclosures = $this->getActiveDisclosures();
        return isset($disclosures[$id]) ? $disclosures[$id] : null;
    }

    private function recordDisclosure($disclosed)
    {
        $disclosures = $this->getActiveDisclosures();
        if ($disclosed['status'] === "PRESENT") {
            $disclosures[$disclosed['id']] = $disclosed['rawvalue'];
            $session = $this->getSession();
            $disclosures = $session->set(self::SESSION_KEY, $disclosures);
        }
    }

    private function getActiveDisclosures()
    {
        $session = $this->getSession();
        $disclosures = $session->get(self::SESSION_KEY);

        return $disclosures ?: [];
    }

    private function getSession(): ?Session
    {
        return Controller::curr()->getRequest()->getSession();
    }
}
