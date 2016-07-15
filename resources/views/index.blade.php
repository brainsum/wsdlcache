<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>WSDL Cache Status</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
          crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
          integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
          crossorigin="anonymous">
  </head>

  <body>
  <!-- Start of Content -->
  <div class="container container-fluid">
    <div class="row">
      <div class="col-md-12">
        <h1>Mabiasz WSDL Cache status</h1>

        <span>E-mail log here: <a target="_blank" href="https://mailtrap.io/inboxes/124612/messages/223731773/html">Open</a></span>

        <table style="vertical-align: middle;" class="table table-responsive table-bordered table-striped text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Type</th>
              <th>WSDL</th>
              <th>Status</th>
              <th>Last Check Date</th>
              <th>Last Modification Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @if (empty($wsdlList))
              <tr>
                <td colspan="7">No WSDL data found.</td>
              </tr>
            @else
              <?php $i = 0 ?>
              @foreach ($wsdlList as $wsdl)
              <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $wsdl->getName() }}</td>
                <td>{{ $wsdl->getType() }}</td>
                <td>{{ $wsdl->getWsdl() }}</td>
                @if ($wsdl->isAvailable())
                  <?php $tdClass = "success"; $spanClass = "ok"; ?>
                @else
                  <?php $tdClass = "danger"; $spanClass = "remove"; ?>
                @endif
                <td class="{{ $tdClass }}" title="Status code: {{ $wsdl->getStatusCode() }}">
                  <a target="_blank" href="https://http.cat/{{ $wsdl->getStatusCode() }}">
                  <span style="font-size:1.8em;" class="glyphicon glyphicon-{{ $spanClass }}-circle"></span>
                  <span>{{ $wsdl->getStatusCode() }}</span>
                  </a>
                </td>
                <td>{{ $wsdl->getLastCheck()->format("Y-m-d H:i:s") }}</td>
                <td>{{ $wsdl->getLastModification()->format("Y-m-d H:i:s") }}</td>
                <td>
                  <ul class="list-inline">
                    <li>* <a target="_blank" href="{{ route("getWSDLByName", array("name" => $wsdl->getName())) }}">Check</a></li>
                    <li>* <a href="{{ route("downloadWSDLLogByName", array("name" => $wsdl->getName())) }}">Get logs</a></li>
                  </ul>
                </td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>
    </div>
    <div class="random">
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
            <td>restructure logs (watchdog style)</td>
            <td>research: standard log formats [try monolog logger]</td>
          </tr>
          <tr>
            <th>minor</th>
            <td>Refactor the code</td>
          </tr>
        <tr>
          <td></td>
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
  <!-- End of Content -->
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-2.2.3.min.js"
          integrity="sha256-a23g1Nt4dtEYOj7bR+vTu7+T8VP13humZFBJNIYoEJo="
          crossorigin="anonymous"></script>
  <!-- Latest compiled and minified JavaScript -->
  <script
    src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
    integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
    crossorigin="anonymous"></script>
  </body>
</html>