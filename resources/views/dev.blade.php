@extends('Layouts.template')

@section('title')
    WSDL Cache Status - Dev stuff
@endsection

@section('content')
    <div class="container container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1>Todo list</h1>
                <ol>
                    <li>
                        cím alá egy info link: (info page magyarul;)
                                infókkal, hogy mi mit csinál (status codes, mi a lastmoddate, actionök);
                                mi a wsdl;
                                + email-ről írni picit + mire szolgál + milyen gyakroisággal check
                    </li>

                    <li>inline förmedvényeket kivenni css-be</li>
                </ol>


                <h3>DONE</h3>

                <ol>
                    <li>
                        WSDL, name legyen kisebb, kevesebb helyet foglaljon
                    </li>
                    <li>
                        oszlopook: Bizt. név; típus (casco/kgfb); Calc/Offer? ;live?(test/live); status; dates; (curr)name; WSDL; ACtions
                    </li>
                    <li>
                        bizt név legyen bold
                    </li>
                    <li>
                        nbsp legyen az action-öknél
                    </li>
                    <li>
                        status: legyen kiírva, hogy ok; kék pipák legyenek zöldek; pirosak mg pirosak
                    </li>
                    <li>
                        Gergővel consult: font-family open-sans; h1 legyen light
                    </li>
                </ol>


                <h1>
                    <span>E-mail log here: <a target="_blank" href="https://mailtrap.io/inboxes/124612/messages/223731773/html">Open</a></span>
                </h1>

                <ol>
                    <li>MAJOR!! Solve parallel wsdl access, so we don't run out of time (cron runs every minute, stuff must be ready by that)</li>
                    <li>REFACTOR THE WSDL FILE ACCORDING TO NEW FIELDS</li>
                    <li>The parse should skip unfinished stuff</li>
                    <li>Create unexisting directories (parents, subs, etc.)</li>
                    <li>Every dumped string should go into the lumen log.</li>
                    <li>Proper install guide</li>
                    <li>
                        drupal/moduls/mabiaszm/config file betöltése kéne, drupalt bootstrapelve; így éles siteból jönnének a jelszavak, nem itt lennének tárolva
                    </li>
                    <li>Look out for additional PHP7.0 features (like String $name) and remove them.</li>
                    <li>Test the API</li>
                    <li>better logging (standard format, try monolog, etc)</li>
                    <li>refactor parts of the code</li>
                    <li>add stuff, so illuminate/mail has everything it might need. aws/aws-sdk-php ;; guzzlehttp/guzzle ;; jeremeamia/superclosure  </li>
                    <li>Try guzzlehttp as a secondary download tool; it might be more modern than curl</li>
                    <li>Add commands: mabiasz:clear:cache; mabiasz:clear:logs; mabiasz:clear:backup; mabiasz:init:status; mabiasz:map:idreset</li>

                </ol>
            </div>
        </div>
    </div>
@endsection