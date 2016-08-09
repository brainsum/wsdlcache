@extends('Layouts.template')

@section('title')
  @if(env('APP_ENV') == "local")
    LOCAL
  @endif
  WSDL Cache Status Page
@endsection

@section('content')
  <div class="container container-fluid">
    <div class="row">
      <div class="col-md-12">
        <h1>
          @if(env('APP_ENV') == "local")
            LOCAL
          @endif
          Mabiasz WSDL Cache status
        </h1>

        <div>
          <a style="font-size: xx-large;" href="{{ route("informations") }}">Bővebb információk</a>
        </div>

        <table style="vertical-align: middle;" class="table table-responsive table-bordered table-striped text-center">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>KGFB/Casco?</th>
              <th>Calculation/Offer?</th>
              <th>Test/Live?</th>
              <th>Status</th>
              <th>Last Check Date</th>
              <th>Last Modification Date</th>
              <th>WSDL</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @if (empty($wsdlList))
              <tr>
                <td colspan="10">No WSDL data found.</td>
              </tr>
            @else
              <?php $i = 0 ?>
              @foreach ($wsdlList as $wsdl)
              <tr>
                <td>{{ ++$i }}</td>
                <td><b>{{ $wsdl->getName() }}</b></td>
                <td>
                  @if ($wsdl->isKgfb() == TRUE)
                    KGFB
                  @else
                    CASCO
                  @endif
                </td>
                <td>
                  @if ($wsdl->isCalculation() == TRUE)
                    Calculation
                  @else
                    Offer
                  @endif
                </td>
                <td>
                  @if ($wsdl->isTest() == TRUE)
                    Test
                  @else
                    Live
                  @endif
                </td>
                @if ($wsdl->isAvailable())
                  <?php $tdClass = "success"; $spanClass = "ok"; $isAvailableText = "Ok."; $textColor = "darkgreen"; ?>
                @else
                  <?php $tdClass = "danger"; $spanClass = "remove"; $isAvailableText = "Not&nbsp;ok."; $textColor = "darkred"; ?>
                @endif
                <td class="{{ $tdClass }}" title="Status code: {{ $wsdl->getStatusCode() }}" style="color: {{ $textColor }};">
                  <a target="_blank" href="https://http.cat/{{ $wsdl->getStatusCode() }}" style="color: {{ $textColor }};">
                    <span style="font-size:1.8em;" class="glyphicon glyphicon-{{ $spanClass }}-circle"></span>
                  </a><br>
                  <span>{{ $wsdl->getStatusCode() }}</span><br>
                  <span style="font-size: large;">{{ $isAvailableText }}</span>
                </td>
                <td>
                  {{ $wsdl->getLastCheck()->format("Y-m-d H:i:s") }}
                </td>
                <?php

                /**
                 * @var App\Models\WSDL $wsdl
                 * @var integer $today
                 */
                  if($wsdl->getLastModification()->getTimestamp() >= $today) {
                    $tdClass = "danger"; $tdTitle = "Was updated today.";
                  } else {
                    $tdClass = ""; $tdTitle = "";
                  }
                ?>
                <td class="{{ $tdClass }}" title="{{ $tdTitle }}">
                  {{ $wsdl->getLastModification()->format("Y-m-d H:i:s") }}
                </td>
                <td>{{ $wsdl->getWsdl() }}</td>
                <td>
                  <ul class="list-inline">
                    @if(env('APP_ENV') == "local")
                      <li><a target="_blank" href="{{ route("getWSDLById", array("id" => $wsdl->getId())) }}" title="Checks the given entry independently from the cron.">Check</a></li>
                    @endif
                      <li><a href="{{ route("downloadWSDLLogById", array("id" => $wsdl->getId())) }}" title="Download the full log for the given entry.">Get&nbsp;logs</a></li>
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