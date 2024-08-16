<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type"
        content="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Guias</title>
</head>

<body>
    <div>
        <h3 align="center" class="title"><strong>Reporte de guias</strong></h3>

    </div>
    <br>
    @if (!empty($records))
        <div class="">
            <div class=" ">
                <table class="">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha de emisión</th>
                            <th>
                                {{ $isTransport ? 'Destinatario' : 'Cliente' }}
                            </th>
                            <th>Número</th>
                            <th>Estado</th>
                            <th>Fecha de envio</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $key => $value)
                            @php
                                /** @var Dispatch $value */
                                $newValue = $value->getCollectionData();
                                
                            @endphp
                            <tr>
                                <td class="celda">{{ $loop->iteration }}</td>
                                <td class="celda">{{ $newValue['date_of_issue'] }}</td>
                                <td class="celda">
                                    {{ $isTransport ? $newValue['sender_name'] : $newValue['customer_name'] }}
                                </td>
                                <td class="celda">{{ $newValue['number'] }}</td>
                                <td class="celda">{{ $newValue['state_type_description'] }}</td>
                                <td class="celda">{{ $newValue['date_of_shipping'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div>
            <p>No se encontraron registros.</p>
        </div>
    @endif
</body>

</html>
