<?php

use SilverStripe\View\Requirements;


// Look at https://github.com/privacybydesign/irma-frontend-packages/blob/6a8899c855da6d82f40b38b0f5088462d385084c/plugins/irma-client/README.md

class IrmaPageController extends PageController
{
    /**
     * An array of actions that can be accessed via a request. Each array element should be an action name, and the
     * permissions or conditions required to allow the user to access it.
     *
     * <code>
     * [
     *     'action', // anyone can access this action
     *     'action' => true, // same as above
     *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
     *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
     * ];
     * </code>
     *
     * @var array
     */
    private static $allowed_actions = [
        'sessionPtr',
        'statusevent',
        'result'
    ];

    private static $url_handlers = [
        'session/$Token/result' => 'result',
        'session' => 'sessionPtr',
    ];


    protected function init()
    {
        parent::init();
        // You can include any CSS or JS required by your project here.
        // See: https://docs.silverstripe.org/en/developer_guides/templates/requirements/

    }

    private function getIrmaClient()
    {
        return new IrmaClient();
    }

    public function IRMALogin()
    {
        Requirements::javascript('client/dist/irma.js');

        $irmaUrl = strtok($this->Link(), '?');
        Requirements::customScript(sprintf('var irmaUrl = %s ;', json_encode($irmaUrl)));
        Requirements::javascript('client/dist/index.js');


        return $this->renderWith('IrmaLogin');
    }

    public function sessionPtr()
    {
        $client = $this->getIrmaClient();
        $session = $client->startSession([$this->IRMAAttribute]);
        return JSONResponse::create($session, 200);
    }

    public function statusevent()
    {
        return JSONResponse::create(
            [
                "status" => 500,
                "error" => "SSE_DISABLED",
                "description"=> "Server sent events disabled"
            ],
            500
        );
    }

    public function result(\SilverStripe\Control\HTTPRequest $request)
    {
        $token = $request->param('Token');
        $client = $this->getIrmaClient();
        $result = $client->result($token);
        return JSONResponse::create($result, 200);
    }

    public function Content()
    {
        $client = $this->getIrmaClient();
        $disclosure = $client->getDisclosure($this->IRMAAttribute);

        if ($disclosure === null) {
            return $this->IRMALogin();
        } elseif ($disclosure !== $this->IRMAAttributeValue) {
            return $this->IRMADenied;
        }
        return $this->data()->Content;
    }
}
