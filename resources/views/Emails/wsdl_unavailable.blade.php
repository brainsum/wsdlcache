<h1>Dear admin!</h1>
<p>An unavailable WSDL host has been detected.</p>
<table>
    <tbody>
        <tr>
            <th scope="row">ID in map</th>
            <td>{{ $WSDLID }}</td>
        </tr>
        <tr>
            <th scope="row">Name</th>
            <td>{{ $WSDLName }}</td>
        </tr>
        <tr>
            <th scope="row">URL</th>
            <td>{{ $WSDLUrl }}</td>
        </tr>
        <tr>
            <th scope="row">HTTP Status Code</th>
            <td>{{ $WSDLStatusCode }}</td>
        </tr>
    </tbody>
</table>
<p>Greetings,<br>
    WSDLcache cron
</p>