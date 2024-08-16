@php

    $all_documents = collect($data['all_documents'])->sortBy('order_number_key');

    $income_records = $all_documents->where('type_transaction_prefix', 'income');
    $egress_records = $all_documents->where('type_transaction_prefix', 'egress');
    $income_methods_payment = $income_records->pluck('payment_method_description')->unique();
    //iterar sobre los metodos de pago y por cada uno crea un array con los documentos que tienen ese metodo de pago, si el documento no tiene metodo de pago se le asigna 'Otros'
    $income_methods_payment = $income_methods_payment->map(function ($item, $key) use ($income_records) {
        $documents = $income_records->where('payment_method_description', $item)->values();
        if ($item == null) {
            $item = 'Otros';
        }
        return [
            'payment_method' => $item,
            'documents' => $documents,
        ];
    });
@endphp


@if (count($income_records) > 0)

    <p align="center" class="title">Ingresos</p>
    <table>
        <thead>
            <tr>
                <th>
                    #
                </th>
                <th>
                    Tipo transacción
                </th>
                <th>
                    Tipo documento
                </th>
                <th>
                    Documento
                </th>
                @isset($detail)
                    <th>N° Operación</th>
    @endif
    <th>
        Fecha emisión
    </th>
    <th>
        Cliente/Proveedor
    </th>
    <th>
        N° Documento
    </th>
    <th>
        Moneda
    </th>
    <th>
        T.Pagado
    </th>
    {{-- <th>
        Total
    </th> --}}
    </tr>
    </thead>
    <tbody>
        {{-- @foreach ($income_records as $key => $value)
            <tr>
                <td class="celda">
                    {{ $loop->iteration }}
                </td>
                <td class="celda">
                    {{ $value['type_transaction'] }}
                </td>
                <td class="celda">
                    {{ $value['document_type_description'] }}
                </td>
                <td class="celda">
                    {{ $value['number'] }}
                </td>
                @isset($detail)
                
                    <td class="celda">
                        {{$value['reference']}}
                    </td>
                @endisset
                <td class="celda">
                    {{ $value['date_of_issue'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_name'] }}
                </td>
                <td class="celda">
                    {{ $value['customer_number'] }}
                </td>
                <td class="celda">
                    {{ $value['currency_type_id'] }}
                </td>
                <td class="celda">
                    {{ $value['total_payments'] ?? '0.00' }}
                </td>
                <td class="celda">
                    {{ $value['total_string'] }}
                </td>
            </tr>
        @endforeach --}}
        @foreach ($income_methods_payment as $key => $value)
            <tr>
                <td colspan="10">
                    <strong>{{ $value['payment_method'] }}</strong>
                </td>
            </tr>
            @php
                $total_acum = 0;
            @endphp
            @foreach ($value['documents'] as $key => $document)
                <tr>
                    <td class="celda">
                        {{ $loop->iteration }}
                    </td>
                    <td class="celda">
                        {{ $document['type_transaction'] }}
                    </td>
                    <td class="celda">
                        {{ $document['document_type_description'] }}
                    </td>
                    <td class="celda">
                        {{ $document['number'] }}
                    </td>
                    @isset($detail)
                        <td class="celda">
                            {{ $document['reference'] }}
                        </td>
                    @endisset
                    <td class="celda">
                        {{ $document['date_of_issue'] }}
                    </td>
                    <td class="celda">
                        {{ $document['customer_name'] }}
                    </td>
                    <td class="celda">
                        {{ $document['customer_number'] }}
                    </td>
                    <td class="celda">
                        {{ $document['currency_type_id'] }}
                    </td>
                    <td class="celda">
                        {{ $document['total_payments'] ?? '0.00' }}
                    </td>
                    {{-- <td class="celda">
                        {{ $document['total_string'] }}
                    </td> --}}
                </tr>
                @php
                    $total_acum += $document['total_payments'];
                @endphp
            @endforeach
            <tr>
                <td colspan="8" class="celda"></td>
                <td class="celda">Total</td>
                <td class="celda">
                    {{ number_format($total_acum, 2, '.', '') }}
                </td>
            </tr>
        @endforeach
    </tbody>
    </table>

    @endif



    @if (count($egress_records) > 0)

        <p align="center" class="title">Egresos</p>
        <table>
            <thead>
                <tr>
                    <th>
                        #
                    </th>
                    <th>
                        Tipo transacción
                    </th>
                    <th>
                        Tipo documento
                    </th>
                    <th>
                        Documento
                    </th>
                    <th>
                        Fecha emisión
                    </th>
                    <th>
                        Cliente/Proveedor
                    </th>
                    <th>
                        N° Documento
                    </th>
                    <th>
                        Moneda
                    </th>
                    <th>
                        T.Pagado
                    </th>
                    <th>
                        Total
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($egress_records as $key => $value)
                    <tr>
                        <td class="celda">
                            {{ $loop->iteration }}
                        </td>
                        <td class="celda">
                            {{ $value['type_transaction'] }}
                        </td>
                        <td class="celda">
                            {{ $value['document_type_description'] }}
                        </td>
                        <td class="celda">
                            {{ $value['number'] }}
                        </td>
                        <td class="celda">
                            {{ $value['date_of_issue'] }}
                        </td>
                        <td class="celda">
                            {{ $value['customer_name'] }}
                        </td>
                        <td class="celda">
                            {{ $value['customer_number'] }}
                        </td>
                        <td class="celda">
                            {{ $value['currency_type_id'] }}
                        </td>
                        <td class="celda">
                            {{ $value['total_payments'] ?? '0.00' }}
                        </td>
                        <td class="celda">
                            @php
                                $value['total_string'] = str_replace('-', '', $value['total_string']);
                            @endphp
                            {{ $value['total_string'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endif
