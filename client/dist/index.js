window.onload = function () {

    console.dir(irmaUrl);

    const example = irma.newWeb({
        debugging: false, // Enable to get helpful output in the browser console
        element: '.irma-login',
        language:  'en',
        translations: {
            header:  'Continue with <i class="irma-web-logo">IRMA</i>',
            loading: 'Just one second please!'
        },
        // Back-end options
        session: {
            url: irmaUrl,
            // start: {
            //     url: o => `${o.url}/session`,
            //     method: 'GET',
            //     headers: {},
            // },
            // mapping: {
            //     // The only thing included in the request is the session pointer, so no additional parsing is needed.
            //     sessionPtr: r => r,
            // },
            // result: false,
        },
        // state: {
        //     url: 'http://irma.max.silverstripe.com/new-irma-page',
        //     serverSentEvents: {
        //         url:        o => `${o.url}/statusevents`,
        //         timeout:    2000,
        //     },
        //
        //     polling: {
        //         url:        o => `${o.url}/status`,
        //         interval:   500,
        //         startState: 'INITIALIZED'
        //     },
        //
        //     cancel: {
        //         url:        o => o.url
        //     }
        // }
    });


    example.start()
        .then(result => location.reload())
        .catch(error => console.error("Couldn't do what you asked 😢", error));
}
