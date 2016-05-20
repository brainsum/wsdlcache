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
    <div class="col-md-1"></div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
          <h1>Mabiasz WSDL Cache status</h1>
          <table class="table table-responsive table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>WSDL</th>
                <th>Status</th>
                <th>Last Check Date</th>
                <th>Last Modification Date</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Dummy1</td>
                <td>off</td>
                <td>now</td>
                <td>now</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Dummy2</td>
                <td>off</td>
                <td>now</td>
                <td>now</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        test: https://0000922995:Pkr80022995@tesztfe64.aegon.hu/dijkalk_webservice/gfb.asmx?WSDL
      </div>
      <div class="row">
        <?php
          echo app()->basePath();
        ?>
      </div>
    </div>
    <div class="col-md-1"></div>
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