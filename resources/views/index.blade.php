@extends('Layouts.template')

@section('title')
  WSDL Cache Status Page
@endsection

@section('content')
  <div class="container container-fluid">
    <div class="row">
      <div class="col-md-12">
        <h1>Mabiasz WSDL Cache status</h1>

        <table style="vertical-align: middle;" class="table table-responsive table-bordered table-striped text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
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
                <td>{{ $wsdl->getName(TRUE, TRUE, TRUE) }}</td>
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
  </div>
@endsection