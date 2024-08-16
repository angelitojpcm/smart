@php
    $establishment__ = \App\Models\Tenant\Establishment::where('id', $document->establishment_id)->first();
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');

    $left = $document->series ? $document->series : $document->prefix;
    $tittle = $left . '-' . str_pad($document->number, 8, '0', STR_PAD_LEFT);
    $payments = $document->payments;
    $accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();
    $logo = "storage/uploads/logos/{$company->logo}";
    if ($establishment->logo) {
        $logo = "{$establishment->logo}";
    }
    $is_integrate_system = Modules\BusinessTurn\Models\BusinessTurn::isIntegrateSystem();
    $quotation = null;
    if ($is_integrate_system) {
        $quotation = \App\Models\Tenant\Quotation::select(['number', 'prefix', 'shipping_address'])
            ->where('id', $document->quotation_id)
            ->first();
    }
    $configuration = \App\Models\Tenant\Configuration::first();

@endphp
<html>

<head>
    {{-- <title>{{ $tittle }}</title> --}}
    {{-- <link href="{{ $path_style }}" rel="stylesheet" /> --}}
</head>

<body>
    <table class="full-width">

        <tr>
            @if ($company->logo)
                <td width="20%">
                    <div class="company_logo_box">
                        <img src="data:{{ mime_content_type(public_path("{$logo}")) }};base64, {{ base64_encode(file_get_contents(public_path("{$logo}"))) }}"
                            alt="{{ $company->name }}" class="company_logo" style="max-width: 150px;">
                    </div>
                </td>
            @else
                <td width="20%">
                </td>
            @endif
            <td width="50%" class="pl-3">
                <div class="text-left">
                    <h4 class="">{{ $company->name }}</h4>
                    <h5>{{ 'RUC ' . $company->number }}</h5>
                    <h6 style="text-transform: uppercase;">
                        {{ $establishment->address !== '-' ? $establishment->address : '' }}
                        {{ $establishment->district_id !== '-' ? ', ' . $establishment->district->description : '' }}
                        {{ $establishment->province_id !== '-' ? ', ' . $establishment->province->description : '' }}
                        {{ $establishment->department_id !== '-' ? '- ' . $establishment->department->description : '' }}
                    </h6>
                    <h6>{{ $establishment->email !== '-' ? $establishment->email : '' }}</h6>
                    <h6>{{ $establishment->telephone !== '-' ? $establishment->telephone : '' }}</h6>
                </div>
            </td>
            <td width="30%" class="border-box py-4 px-2 text-center">
                {{-- <h5 class="text-center">{{ get_document_name('sale_note', 'NOTA DE VENTA') }}</h5> --}}
                <h5 class="text-center">{{ get_document_name('sale_note', 'NOTA DE VENTA') }}</h5>
                <h3 class="text-center">{{ $tittle }}</h3>
            </td>
        </tr>
    </table>
    <table class="full-width mt-5">
        <tr>
            <td width="15%">Cliente:</td>
            <td width="45%">{{ $customer->name }}</td>
            <td width="25%">Fecha de emisión:</td>
            <td width="15%">{{ $document->date_of_issue->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td>{{ $customer->identity_document_type->description }}:</td>
            <td>{{ $customer->number }}</td>

            @if ($document->due_date)
                <td class="align-top">Fecha Vencimiento:</td>
                <td>{{ $document->getFormatDueDate() }}</td>
            @endif

        </tr>
        @if (isset($customer->location) && $customer->location != '')
            <tr>
                <td class="align-top">Ubicación:</td>
                <td colspan="3">{{ $customer->location }}</td>
            </tr>
        @endif
        @if ($customer->address !== '')
            <tr>
                <td class="align-top">Dirección:</td>
                <td colspan="3">
                    {{ strtoupper($customer->address) }}
                    {{ $customer->district_id !== '-' ? ', ' . strtoupper($customer->district->description) : '' }}
                    {{ $customer->province_id !== '-' ? ', ' . strtoupper($customer->province->description) : '' }}
                    {{ $customer->department_id !== '-' ? '- ' . strtoupper($customer->department->description) : '' }}
                </td>
            </tr>
        @endif
        @if ($quotation && $quotation->shipping_address)
            <tr>
                <td class="align-top">Dir. de envío:</td>
                <td colspan="3">{{ $quotation->shipping_address }}</td>
            </tr>
        @endif

        <tr>
            <td>Teléfono:</td>
            <td>{{ $customer->telephone }}</td>
            <td>Vendedor:</td>
            <td>
                @if ($document->seller_id != 0)
                    {{ $document->seller->name }}
                @else
                    {{ $document->user->name }}
                @endif
            </td>
        </tr>
        @if ($document->plate_number !== null)
            <tr>
                <td width="15%">N° Placa:</td>
                <td width="85%">{{ $document->plate_number }}</td>
            </tr>
        @endif
        @if ($document->total_canceled)
            <tr>
                <td class="align-top">Estado:</td>
                <td colspan="3">CANCELADO</td>
            </tr>
        @else
            <tr>
                <td class="align-top">Estado:</td>
                <td colspan="3">PENDIENTE DE PAGO</td>
            </tr>
        @endif
        @if ($document->hotelRent)
            <tr>
                <td class="align-top">Destino:</td>
                <td colspan="3">{{ $document->hotelRent->destiny }}</td>
            </tr>
        @endif
        @if ($document->observation && !is_integrate_system())
            <tr>
                <td class="align-top">Observación:</td>
                <td colspan="3">{{ $document->observation }}</td>
            </tr>
        @endif
        @if ($document->reference_data)
            <tr>
                <td class="align-top">D. Referencia:</td>
                <td colspan="3">{{ $document->reference_data }}</td>
            </tr>
        @endif
        @if ($document->purchase_order)
            <tr>
                <td class="align-top">Orden de compra:</td>
                <td colspan="3">{{ $document->purchase_order }}</td>
            </tr>
        @endif
    </table>

    @if ($document->isPointSystem())
        <table class="full-width mt-3">
            <tr>
                <td width="15%">P. ACUMULADOS</td>
                <td width="8px">:</td>
                <td>{{ $document->person->accumulated_points }}</td>

                <td width="140px">PUNTOS POR LA COMPRA</td>
                <td width="8px">:</td>
                <td>{{ $document->getPointsBySale() }}</td>
            </tr>
        </table>
    @endif


    @if ($document->guides)
        <br />
        {{-- <strong>Guías:</strong> --}}
        <table>
            @foreach ($document->guides as $guide)
                <tr>
                    @if (isset($guide->document_type_description))
                        <td>{{ $guide->document_type_description }}</td>
                    @else
                        <td>{{ $guide->document_type_id }}</td>
                    @endif
                    <td>:</td>
                    <td>{{ $guide->number }}</td>
                </tr>
            @endforeach
        </table>
    @endif
    @if ($document->transport)
        <br>
        <strong>Transporte de pasajeros</strong>
        @php
            $transport = $document->transport;
            $origin_district_id = (array) $transport->origin_district_id;
            $destinatation_district_id = (array) $transport->destinatation_district_id;
            $origin_district = Modules\Order\Services\AddressFullService::getDescription($origin_district_id[2]);
            $destinatation_district = Modules\Order\Services\AddressFullService::getDescription(
                $destinatation_district_id[2],
            );
        @endphp

        <table class="full-width mt-3">
            <tr>
                <td width="120px">{{ $transport->identity_document_type->description }}</td>
                <td width="8px">:</td>
                <td>{{ $transport->number_identity_document }}</td>
                <td width="120px">NOMBRE</td>
                <td width="8px">:</td>
                <td>{{ $transport->passenger_fullname }}</td>
            </tr>
            <tr>
                <td width="120px">N° ASIENTO</td>
                <td width="8px">:</td>
                <td>{{ $transport->seat_number }}</td>
                <td width="120px">M. PASAJERO</td>
                <td width="8px">:</td>
                <td>{{ $transport->passenger_manifest }}</td>
            </tr>
            <tr>
                <td width="120px">F. INICIO</td>
                <td width="8px">:</td>
                <td>{{ $transport->start_date }}</td>
                <td width="120px">H. INICIO</td>
                <td width="8px">:</td>
                <td>{{ $transport->start_time }}</td>
            </tr>
            <tr>
                <td width="120px">U. ORIGEN</td>
                <td width="8px">:</td>
                <td>{{ $origin_district }}</td>
                <td width="120px">D. ORIGEN</td>
                <td width="8px">:</td>
                <td>{{ $transport->origin_address }}</td>
            </tr>
            <tr>
                <td width="120px">U. DESTINO</td>
                <td width="8px">:</td>
                <td>{{ $destinatation_district }}</td>
                <td width="120px">D. DESTINO</td>
                <td width="8px">:</td>
                <td>{{ $transport->destinatation_address }}</td>
            </tr>
        </table>
    @endif
    @if ($document->transport_dispatch)
        <br>
        <strong>Información de encomienda</strong>
        @php
            $transport_dispatch = $document->transport_dispatch;
            $sender_identity_document_type = $transport_dispatch->sender_identity_document_type->description;
            $recipient_identity_document_type = $transport_dispatch->recipient_identity_document_type->description;
            // $origin_district_id = (array) $transport_dispatch->origin_district_id;
            // $destinatation_district_id = (array) $transport_dispatch->destinatation_district_id;
            // $origin_district = Modules\Order\Services\AddressFullService::getDescription($origin_district_id[2]);
            // $destinatation_district = Modules\Order\Services\AddressFullService::getDescription($destinatation_district_id[2]);
        @endphp

        <table class="full-width mt-3">
            <thead>
                <tr>
                    <th colspan="6" class="text-left">
                        <strong>REMITENTE</strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="120px">{{ $sender_identity_document_type }}</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->sender_number_identity_document }}</td>
                    <td width="120px">NOMBRE</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->sender_passenger_fullname }}</td>
                </tr>
                <tr>

                </tr>
                <tr>
                    <td width="120px">TELÉFONO</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->sender_telephone }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th colspan="6" class="text-left">
                        <strong>DESTINATARIO</strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="120px">{{ $recipient_identity_document_type }}</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->recipient_number_identity_document }}</td>
                    <td width="120px">NOMBRE</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->recipient_passenger_fullname }}</td>
                </tr>

                <tr>
                    <td width="120px">TELÉFONO</td>
                    <td width="8px">:</td>
                    <td>{{ $transport_dispatch->recipient_telephone }}</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    @endif
    <table class="full-width mt-10 mb-10">
        <thead class="">
            <tr class="bg-grey">
                <th class="border-top-bottom text-center py-2" width="8%">Cant.</th>
                <th class="border-top-bottom text-center py-2" width="8%">Unidad</th>
                <th class="border-top-bottom text-left py-2">Descripción</th>
                <th class="border-top-bottom text-center py-2" width="8%">Lote</th>
                <th class="border-top-bottom text-center py-2" width="8%">Serie</th>
                <th class="border-top-bottom text-right py-2" width="12%">P.Unit</th>
                <th class="border-top-bottom text-right py-2" width="8%">Dto.</th>
                <th class="border-top-bottom text-right py-2" width="12%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->items as $row)
                <tr>
                    <td class="text-center align-top">
                        @if ((int) $row->quantity != $row->quantity)
                            {{ $row->quantity }}
                        @else
                            {{ number_format($row->quantity, 0) }}
                        @endif
                    </td>
                    <td class="text-center align-top">{{ symbol_or_code($row->item->unit_type_id) }}</td>
                    <td class="text-left">
                        @if ($row->name_product_pdf)
                            {!! $row->name_product_pdf !!}
                        @else
                            {!! $row->item->description !!}
                        @endif
                        {{-- 
                            
   
                         --}}

                        @if ($configuration->name_pdf)
                            @php
                                $item_name = \App\Models\Tenant\Item::select('name')
                                    ->where('id', $row->item_id)
                                    ->first();
                            @endphp
                            @if ($item_name->name)
                                <div>
                                    <span style="font-size: 9px">{{ $item_name->name }}</span>
                                </div>
                            @endif
                        @endif

                        @if ($configuration->presentation_pdf && isset($row->item->presentation) && isset($row->item->presentation->description))
                        <div>
                            <span style="font-size: 9px">{{ $row->item->presentation->description }}</span>
                        </div>
                    @endif

                        @isset($row->item->sizes_selected)
                            @if (count($row->item->sizes_selected) > 0)
                                @foreach ($row->item->sizes_selected as $size)
                                    <small> Talla {{ $size->size }} | {{ $size->qty }} und</small> <br>
                                @endforeach
                            @endif
                        @endisset
                        @if ($row->attributes)
                            @foreach ($row->attributes as $attr)
                                <br /><span style="font-size: 9px">{!! $attr->description !!} :
                                    {{ $attr->value }}</span>
                            @endforeach
                        @endif
                        @if ($row->discounts)
                            @foreach ($row->discounts as $dtos)
                                <br /><span style="font-size: 9px">{{ $dtos->factor * 100 }}%
                                    {{ $dtos->description }}</span>
                            @endforeach
                        @endif

                        @if ($row->item->is_set == 1)
                            <br>
                            @inject('itemSet', 'App\Services\ItemSetService')
                            @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                                {{ $item }}<br>
                            @endforeach
                        @endif

                        @if ($row->item->used_points_for_exchange ?? false)
                            <br>
                            <span style="font-size: 9px">*** Canjeado por {{ $row->item->used_points_for_exchange }}
                                puntos ***</span>
                        @endif

                    </td>
                    <td class="text-center align-top">

                        @inject('itemLotGroup', 'App\Services\ItemLotsGroupService')
                        @php

                            // utilizar propiedad si la nv esta regularizada con dicho campo
                            if (isset($row->item->IdLoteSelected)) {
                                $lot_code = $row->item->IdLoteSelected;
                            } else {
                                // para nv con error de propiedad
                                $lot_code = [];
                                if (isset($row->item->lots_group)) {
                                    $lot_codes_compromise = collect($row->item->lots_group)->where(
                                        'compromise_quantity',
                                        '>',
                                        0,
                                    );
                                    $lot_code = $lot_codes_compromise->all();
                                }
                            }

                        @endphp

                        {{ $itemLotGroup->getLote($lot_code) }}

                    </td>
                    <td class="text-center align-top">

                        @isset($row->item->lots)
                            @foreach ($row->item->lots as $lot)
                                @if (isset($lot->has_sale) && $lot->has_sale)
                                    <span style="font-size: 9px">
                                        {{ $lot->series }}
                                        @if (!$loop->last)
                                            -
                                        @endif
                                    </span>
                                @endif
                            @endforeach
                        @endisset
                    </td>
                    <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
                    <td class="text-right align-top">
                        @if ($row->discounts)
                            @php
                                $total_discount_line = 0;
                                foreach ($row->discounts as $disto) {
                                    $total_discount_line = $total_discount_line + $disto->amount;
                                }
                            @endphp
                            {{ number_format($total_discount_line, 2) }}
                        @else
                            0
                        @endif
                    </td>
                    <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="8" class="border-bottom"></td>
                </tr>
            @endforeach
            @if ($document->total_exportation > 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">Op. Exportación:
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
                </tr>
            @endif
            @if ($document->total_free > 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">Op. Gratuitas:
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
                </tr>
            @endif
            @if ($document->total_unaffected > 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">Op. Inafectas:
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
                </tr>
            @endif
            @if ($document->total_exonerated > 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">Op. Exoneradas:
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
                </tr>
            @endif
            {{-- @if ($document->total_taxed > 0)
             <tr>
                <td colspan="7" class="text-right font-bold">Op. Gravadas: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif --}}
            @if ($document->total_discount > 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">
                        {{ $document->total_prepayment > 0 ? 'Anticipo' : 'Descuento TOTAL' }}:
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
                </tr>
            @endif
            {{-- <tr>
            <td colspan="7" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr> --}}

            @if ($document->total_charge > 0 && $document->charges)
                <tr>
                    <td colspan="7" class="text-right font-bold">CARGOS ({{ $document->getTotalFactor() }}%):
                        {{ $document->currency_type->symbol }}</td>
                    <td class="text-right font-bold">{{ number_format($document->total_charge, 2) }}</td>
                </tr>
            @endif

            <tr>
                <td colspan="7" class="text-right font-bold">Total a pagar: {{ $document->currency_type->symbol }}
                </td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>

            @php
                $change_payment = $document->getChangePayment();
            @endphp
            nsport
            @if ($change_payment < 0)
                <tr>
                    <td colspan="7" class="text-right font-bold">Vuelto: {{ $document->currency_type->symbol }}
                    </td>
                    <td class="text-right font-bold">{{ number_format(abs($change_payment), 2, '.', '') }}</td>
                </tr>
            @endif

        </tbody>
    </table>
    @if (is_integrate_system())
        <table class="full-width">
            @php
                $cot = \App\Models\Tenant\Quotation::where('id', $document->quotation_id)->first();
            @endphp
            @if ($cot)
                <tr>
                    <td width="23%" style="font-weight: bold;text-transform:uppercase;" class="align-top">
                        Cotizacion :</td>
                    <td style="font-weight: bold;text-transform:uppercase;text-align:left;" colspan="3">
                        {{ $cot->prefix }}- {{ $cot->number }}</td>

                </tr>
            @endif
            @if ($cot)
                <tr>
                    <td width="23%" style="font-weight: bold;text-transform:uppercase;" class="align-top">
                        Observación com.:</td>
                    <td style="font-weight: bold;text-transform:uppercase;text-align:left;" colspan="3">
                        {{ $cot->description }}</td>

                </tr>
            @endif
            <tr>
                <td width="23%" style="font-weight: bold;text-transform:uppercase;" class="align-top">Observación
                    adm.:
                </td>
                <td style="font-weight: bold;text-transform:uppercase;text-align:left;" colspan="3">
                    {{ $document->additional_information }}</td>
            </tr>

            @php
                $prod = \App\Models\Tenant\ProductionOrder::where('sale_note_id', $document->id)->first();
            @endphp
            @if ($prod)
                <tr>
                    <td width="23%" style="font-weight: bold;text-transform:uppercase;" class="align-top">
                        Observación prod.:</td>
                    <td style="font-weight: bold;text-transform:uppercase;text-align:left;" colspan="3">
                        {{ $prod->observation }}</td>

                </tr>
            @endif
        </table>
    @endif
    <table class="full-width">
        <tr>
            <td width="65%" style="text-align: top; vertical-align: top;">
                <br>
                @foreach ($accounts as $account)
                    <p>
                        <span class="font-bold">{{ $account->bank->description }}</span>
                        {{ $account->currency_type->description }}
                        <span class="font-bold">N°:</span> {{ $account->number }}
                        @if ($account->cci)
                            - <span class="font-bold">CCI:</span> {{ $account->cci }}
                        @endif
                    </p>
                @endforeach
            </td>
        </tr>
    </table>
    <br>

    @if ($document->payment_method_type_id && $payments->count() == 0)
        <table class="full-width">
            <tr>
                <td>
                    <strong>Pago: </strong>{{ $document->payment_method_type->description }}
                </td>
            </tr>
        </table>
    @endif

    @if ($payments->count())

        <table class="full-width">
            <tr>
                <td>
                    <strong>Pagos:</strong>
                </td>
            </tr>
            @php
                $payment = 0;
            @endphp
            @foreach ($payments as $row)
                <tr>
                    <td>- {{ $row->date_of_payment->format('d/m/Y') }} -
                        {{ $row->payment_method_type->description }}
                        - {{ $row->reference ? $row->reference . ' - ' : '' }}
                        {{ $document->currency_type->symbol }}
                        {{ $row->payment + $row->change }}</td>
                </tr>
                @php
                    $payment += (float) $row->payment;
                @endphp
            @endforeach
            <tr>
                <td><strong>Saldo:</strong> {{ $document->currency_type->symbol }}
                    {{ number_format($document->total - $payment, 2) }}</td>
            </tr>

        </table>
    @endif
    @if($document->fee->count())
    <table class="full-width">
        @foreach ($document->fee as $key => $quote)
            <tr>
                <td>
                    @if (!$configuration->show_the_first_cuota_document)
                        &#8226;
                        {{ 'Cuota #' . ($key + 1) }}
                        / Fecha: {{ $quote->date }} /
                        Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}
                    @else
                        @if ($key == 0)
                            &#8226;
                            {{ 'Cuota #' . ($key + 1)  }}
                            / Fecha: {{ $quote->date }} /
                            Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}
                        @endif
                    @endif

                </td>
            </tr>
        @endforeach
        </tr>
    </table>
    @endif
    <table class="full-width">
        @php
            $establishment_data = \App\Models\Tenant\Establishment::find($document->establishment_id);
        @endphp
        <tbody>
            <tr>
                @if ($configuration->yape_qr_sale_notes && $establishment_data->yape_logo)
                    @php
                        $yape_logo = $establishment_data->yape_logo;
                    @endphp
                    <td class="text-center">
                        <table>
                            <tr>
                                <td>
                                    <strong>
                                        Qr yape
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="data:{{ mime_content_type(public_path("{$yape_logo}")) }};base64, {{ base64_encode(file_get_contents(public_path("{$yape_logo}"))) }}"
                                        alt="{{ $company->name }}" class="company_logo" style="max-width: 150px;">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @if ($establishment_data->yape_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->yape_owner }}
                                        </strong>
                                    @endif
                                    @if ($establishment_data->yape_number)
                                        <br>
                                        <strong>
                                            Número: {{ $establishment_data->yape_number }}
                                        </strong>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                @endif
                @if ($configuration->plin_qr_sale_notes && $establishment_data->plin_logo)
                    @php
                        $plin_logo = $establishment_data->plin_logo;
                    @endphp
                    <td class="text-center">
                        <table>
                            <tr>
                                <td>
                                    <strong>
                                        Qr plin
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="data:{{ mime_content_type(public_path("{$plin_logo}")) }};base64, {{ base64_encode(file_get_contents(public_path("{$plin_logo}"))) }}"
                                        alt="{{ $company->name }}" class="company_logo" style="max-width: 150px;">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @if ($establishment_data->plin_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->plin_owner }}
                                        </strong>
                                    @endif
                                    @if ($establishment_data->plin_number)
                                        <br>
                                        <strong>
                                            Número: {{ $establishment_data->plin_number }}
                                        </strong>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                @endif
            </tr>
        </tbody>
    </table>
    @if ($document->terms_condition)
        <br>
        <table class="full-width">
            <tr>
                <td>
                    <h6 style="font-size: 12px; font-weight: bold;">Términos y condiciones del servicio</h6>
                    {!! $document->terms_condition !!}
                </td>
            </tr>
        </table>
    @endif


</body>

</html>
