@extends('Layouts.template')

@section('title')
    WSDL Cache Információk
@endsection

@section('content')
    <div class="container container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1>Információk a rendszerről</h1>

                <p>
                    Az alkalmazás a MABIASZ rendszerben is használt WSDL fájlok nyilvántartására illetve ellenőrzésére szolgál. A felületen az alábbi információkat tekinthetjük át:
                </p>

                <table class="table table-responsive table-bordered table-striped">
                    <thead>
                        <tr>
                            <th colspan="2">Oszlopleírások</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">#</th>
                            <td>Az adott bejegyzés sorszáma a listában.</td>
                        </tr>
                        <tr>
                            <th scope="row">Name</th>
                            <td>A biztosító neve.</td>
                        </tr>
                        <tr>
                            <th scope="row">KGFB/Casco?</th>
                            <td>Azt jelzi, hogy az adott bejegyzés KGFB vagy CASCO WSDL-hez tartozik-e.</td>
                        </tr>
                        <tr>
                            <th scope="row">Calculation/Offer?</th>
                            <td>Azt jelzi, hogy az adott bejegyzés Kalkulációs vagy Ajánlat WSDL-hez tartozik-e.</td>
                        </tr>
                        <tr>
                            <th scope="row">Test/Live?</th>
                            <td>Azt jelzi, hogy az adott bejegyzés Teszt vagy Éles WSDL-hez tartozik-e.</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>Az adott WSDL státuszát jelzi az alábbi módokon:
                                <ol>
                                    <li>Ikonnal (<span class="glyphicon glyphicon-ok-circle"></span> = Ok. / <span class="glyphicon glyphicon-remove-circle"></span> = Nem ok.),</li>
                                    <li>Szövegesen (Ok. / Not ok.),</li>
                                    <li>HTTP státuszkóddal (200, 404, 503, stb.),</li>
                                    <li>Illetve színkódolva (Zöld = Ok. / Piros = Nem ok.)</li>
                                </ol>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Last Check Date</th>
                            <td>Azt jelzi, hogy utoljára mikor ellenőrizte a rendszer az adott WSDL-t. Ez az ellenőrzés percenként történik.</td>
                        </tr>
                        <tr>
                            <th scope="row">Last Modification Date</th>
                            <td>Azt jelzi, hogy utoljára mikor észlelt változást a rendszer az adott WSDL-ről.</td>
                        </tr>
                        <tr>
                            <th scope="row">WSDL</th>
                            <td>Az az URL amit a rendszer ellenőriz és ami a biztosítói WSDL fájl címét jelzi.</td>
                        </tr>
                        <tr>
                            <th scope="row">Actions</th>
                            <td>Az elérhető műveletek, például az adott WSDL-hez tartozó naplófájl letöltése.</td>
                        </tr>
                    </tbody>
                </table>
                <p>

                </p>
            </div>
        </div>
    </div>
@endsection