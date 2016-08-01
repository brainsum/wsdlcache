@extends('Layouts.template')

@section('title')
    WSDL Cache Status - Dev stuff
@endsection

@section('content')
    <div class="container container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1>Todo list</h1>

                <h1>
                    <span>E-mail log here: <a target="_blank" href="https://mailtrap.io/inboxes/124612/messages/223731773/html">Open</a></span>
                </h1>

                <ol>
                    <li>Fixme: Cleanup kernel, outsource the stuff to another file</li>
                    <li>Fixme: email names to config</li>
                    <li>The parse should skip unfinished stuff</li>
                    <li>Create unexisting stuff (directory, etc)</li>
                    <li>Proper install guide</li>
                    <li>
                        drupal/moduls/mabiaszm/config file betöltése kéne, drupalt bootstrapelve; így éles siteból jönnének a jelszavak
                    </li>
                </ol>

                <table class="table table-responsive table-bordered table-striped text-center">
                    <tbody>
                    <tr>
                        <th>MAJOR</th>
                        <td>go live as fast as possible</td>
                        <td>php version might be blocker (5.6 on server, 7.0 on local)</td>
                    </tr>
                    <tr>
                        <th>MAJOR</th>
                        <td>Write server API so outsiders can access the cached WSDL files</td>
                    </tr>
                    <tr>
                        <th>Standard</th>
                        <td>What if we add smth to the map but not to the status part?</td>
                        <td>We should create a status in this case (automation rules)</td>
                    </tr>
                    <tr>
                        <th>Standard</th>
                        <td>restructure logs (watchdog style)</td>
                        <td>research: standard log formats [try monolog logger]</td>
                    </tr>
                    <tr>
                        <th>minor</th>
                        <td>Refactor the code</td>
                    </tr>
                    <tr>
                        <td>NOTES</td>
                        <td>illuminate/mail suggests installing aws/aws-sdk-php (Required to use the SES mail driver (~3.0).)
                            illuminate/mail suggests installing guzzlehttp/guzzle (Required to use the Mailgun and Mandrill mail drivers (~5.3|~6.0).)
                            illuminate/mail suggests installing jeremeamia/superclosure (Required to be able to serialize closures (~2.0).)
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection