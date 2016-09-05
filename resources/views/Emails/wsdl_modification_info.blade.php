<h1>Dear admin!</h1>
<p>The wsdl cache for mabiasz.hu has been checked at {{ $datetimeOfCheck }}</p>
<p>The cache for {{ $WSDLName }} with ID {{ $WSDLID }} and the remote host {{ $WSDLUrl }} are out of sync.</p>
<p>See the differences below:</p>
<pre><br />
    {{ $WSDLDiff }}
<br /></pre>
<p>Greetings,<br>
    WSDLcache cron
</p>