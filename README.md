# Overview

This is a PoC to illustrate how IRMA could integrate with a Silverstripe CMS site. 

To get started, you need to provide the following keys in your environment:
```dotenv
IRMA_API=xxx
IRMA_SERVER=https://example.com/
IRMA_KEY=/some/path/to/a/key.pem
```

The PEM key is not currently use, but it might be in some future iteration.

# What's in there?

This project allows you to create a special IRMA Page that his restricted to users who disclose a specific claim. (e.g. I'm 18 or older) 

- [app/src/IrmaClient.php](app/src/IrmaClient.php) which handles all the comms with the IRMA server
- [app/src/IrmaPage.php](app/src/IrmaPage.php) which is a generic page that can be restricted based on an IRMA disclosure
- [app/src/IrmaPageController.php](app/src/IrmaPageController.php) which links up the frontend, the IRMA Page and the IRMA client
- [client/dist/index.js](client/dist/index.js) which handles the front end bit to show the QR code
- [client/dist/irma.js](client/dist/irma.js) which is a generic library copied from the IRMA project 
