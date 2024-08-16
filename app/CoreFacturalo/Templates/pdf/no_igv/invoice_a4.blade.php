@php
$establishment = $document->establishment;
$establishment__ = \App\Models\Tenant\Establishment::find($document->establishment_id);
$logo = $establishment__->logo ?? $company->logo;

if ($logo === null && !file_exists(public_path("$logo}"))) {
    $logo = "{$company->logo}";
}

if ($logo) {
    $logo = "storage/uploads/logos/{$logo}";
    $logo = str_replace("storage/uploads/logos/storage/uploads/logos/", "storage/uploads/logos/", $logo);
}


$customer = $document->customer;
$invoice = $document->invoice;
$document_base = ($document->note) ? $document->note : null;

//$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
$document_number = $document->series.'-'.str_pad($document->number, 8, '0', STR_PAD_LEFT);
$accounts = \App\Models\Tenant\BankAccount::where('show_in_documents', true)->get();

if($document_base) {

$affected_document_number = ($document_base->affected_document) ? $document_base->affected_document->series.'-'.str_pad($document_base->affected_document->number, 8, '0', STR_PAD_LEFT) : $document_base->data_affected_document->series.'-'.str_pad($document_base->data_affected_document->number, 8, '0', STR_PAD_LEFT);

} else {

$affected_document_number = null;
}

$payments = $document->payments;

$document->load('reference_guides');

$total_payment = $document->payments->sum('payment');
$balance = ($document->total - $total_payment) - $document->payments->sum('change');

@endphp
<html>

<head>
    {{--<title>{{ $document_number }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>

<body>
    @if($document->state_type->id == '11')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:30%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."anulado.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
    @endif
    @if($document->soap_type_id == '01')
    <div class="company_logo_box" style="position: absolute; text-align: center; top:30%;">
        <img src="data:{{mime_content_type(public_path("status_images".DIRECTORY_SEPARATOR."demo.png"))}};base64, {{base64_encode(file_get_contents(public_path("status_images".DIRECTORY_SEPARATOR."demo.png")))}}" alt="anulado" class="" style="opacity: 0.6;">
    </div>
    @endif
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
                    {{-- <img src="{{ asset('logo/logo.jpg') }}" class="company_logo" style="max-width: 150px"> --}}
                </td>
            @endif
            <td width="50%" class="pl-3">
                <div class="text-left">
                    <h4 class="">{{ $company->name }}</h4>
                    <h5>{{ 'RUC '.$company->number }}</h5>
                    <h6 style="text-transform: uppercase;">
                        {{ ($establishment->address !== '-')? $establishment->address : '' }}
                        {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                        {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                        {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                    </h6>

                    @isset($establishment->trade_address)
                    <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
                    @endisset

                    <h6>{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</h6>

                    <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

                    @isset($establishment->web_address)
                    <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
                    @endisset

                    @isset($establishment->aditional_information)
                    <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
                    @endisset
                </div>
            </td>
            <td width="30%" class="border-box py-4 px-2 text-center">
                <h5 class="text-center">{{ $document->document_type->description }}</h5>
                <h3 class="text-center">{{ $document_number }}</h3>
            </td>
        </tr>
    </table>
    <table class="full-width mt-5">
        <tr>
            <td width="120px">Fecha de emisión</td>
            <td width="8px">:</td>
            <td>{{$document->date_of_issue->format('Y-m-d')}}</td>

            @if ($document->detraction)

            <td width="120px">N. Cta detracciones</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->bank_account}}</td>
            @endif
        </tr>
        @if($invoice)
        <tr>
            <td>F. de vencimiento</td>
            <td width="8px">:</td>
            <td>{{$invoice->date_of_due->format('Y-m-d')}}</td>
        </tr>
        @endif

        @if ($document->detraction)
        <td width="140px">B/S Sujeto a detracción</td>
        <td width="8px">:</td>
        @inject('detractionType', 'App\Services\DetractionTypeService')
        <td width="220px">{{$document->detraction->detraction_type_id}} - {{ $detractionType->getDetractionTypeDescription($document->detraction->detraction_type_id ) }}</td>

        @endif
        <tr>
            <td style="vertical-align: top;">Cliente:</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top;">
                {{ $customer->name }}
                @if ($customer->internal_code ?? false)
                <br>
                <small>{{ $customer->internal_code ?? '' }}</small>
                @endif
            </td>

            @if ($document->detraction)
            <td width="120px">Método de pago</td>
            <td width="8px">:</td>
            <td width="220px">{{ $detractionType->getPaymentMethodTypeDescription($document->detraction->payment_method_id ) }}</td>
            @endif

        </tr>
        <tr>
            <td>{{ $customer->identity_document_type->description }}</td>
            <td>:</td>
            <td>{{$customer->number}}</td>

            @if ($document->detraction)

            <td width="120px">P. Detracción</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->percentage}}%</td>
            @endif
        </tr>
        @if ($customer->address !== '')
        <tr>
            <td class="align-top">Dirección:</td>
            <td>:</td>
            <td style="text-transform: uppercase;">
                {{ $customer->address }}
                {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
            </td>

            @if ($document->detraction)
            <td width="120px">Monto detracción</td>
            <td width="8px">:</td>
            <td>S/ {{ $document->detraction->amount}}</td>
            @endif
        </tr>
        @endif

        @if ($document->reference_data)
        <tr>
            <td width="120px">D. REFERENCIA</td>
            <td width="8px">:</td>
            <td>{{ $document->reference_data}}</td>
        </tr>
        @endif

        @if ($document->detraction)
        @if($document->detraction->pay_constancy)
        <tr>
            <td colspan="3">
            </td>
            <td width="120px">Constancia de pago</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->pay_constancy}}</td>
        </tr>
        @endif
        @endif

        @if($document->detraction && $invoice->operation_type_id == '1004')
        <tr>
            <td colspan="4"><strong>Detalle - Servicios de transporte de carga</strong></td>
        </tr>
        <tr>
            <td class="align-top">Ubigeo origen</td>
            <td>:</td>
            <td>{{ $document->detraction->origin_location_id[2] }}</td>

            <td width="120px">Dirección origen</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->origin_address }}</td>
        </tr>
        <tr>
            <td class="align-top">Ubigeo destino</td>
            <td>:</td>
            <td>{{ $document->detraction->delivery_location_id[2] }}</td>

            <td width="120px">Dirección destino</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->delivery_address }}</td>
        </tr>
        <tr>
            <td class="align-top" width="170px">Valor referencial servicio de transporte</td>
            <td>:</td>
            <td>{{ $document->detraction->reference_value_service }}</td>

            <td width="170px">Valor referencia carga efectiva</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->reference_value_effective_load }}</td>
        </tr>
        <tr>
            <td class="align-top">Valor referencial carga útil</td>
            <td>:</td>
            <td>{{ $document->detraction->reference_value_payload }}</td>

            <td width="120px">Detalle del viaje</td>
            <td width="8px">:</td>
            <td>{{ $document->detraction->trip_detail }}</td>
        </tr>
        @endif

    </table>

    @if ($document->guides)
    <br />
    <table>
        @foreach($document->guides as $guide)
        <tr>
            @if(isset($guide->document_type_description))
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



    @if ($document->reference_guides)
    <br />
    <strong>Guias de remisión</strong>
    <table>
        @foreach($document->reference_guides as $guide)
        <tr>
            <td>{{ $guide->series }}</td>
            <td>-</td>
            <td>{{ $guide->number }}</td>
        </tr>
        @endforeach
    </table>
    @endif


    <table class="full-width mt-3">
        @if ($document->prepayments)
        @foreach($document->prepayments as $p)
        <tr>
            <td width="120px">Anticipo</td>
            <td width="8px">:</td>
            <td>{{$p->number}}</td>
        </tr>
        @endforeach
        @endif
        @if ($document->purchase_order)
        <tr>
            <td width="120px">Orden de compra</td>
            <td width="8px">:</td>
            <td>{{ $document->purchase_order }}</td>
        </tr>
        @endif
        @if ($document->quotation_id)
        <tr>
            <td width="120px">Cotización</td>
            <td width="8px">:</td>
            <td>{{ $document->quotation->identifier }}</td>

            @isset($document->quotation->delivery_date)
            <td width="120px">F. ENTREGA</td>
            <td width="8px">:</td>
            <td>{{ $document->date_of_issue->addDays($document->quotation->delivery_date)->format('d-m-Y') }}</td>
            @endisset
        </tr>

        @endif
        @isset($document->quotation->sale_opportunity)
        <tr>
            <td width="120px">O. Venta</td>
            <td width="8px">:</td>
            <td>{{ $document->quotation->sale_opportunity->number_full}}</td>
        </tr>
        @endisset
        @if(!is_null($document_base))
        <tr>
            <td width="120px">Doc. Afectado</td>
            <td width="8px">:</td>
            <td>{{ $affected_document_number }}</td>
        </tr>
        <tr>
            <td>Tipo de nota</td>
            <td>:</td>
            <td>{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>
        </tr>
        <tr>
            <td>Descripción</td>
            <td>:</td>
            <td>{{ $document_base->note_description }}</td>
        </tr>
        @endif
    </table>

    {{--<table class="full-width mt-3">--}}
    {{--<tr>--}}
    {{--<td width="25%">Documento Afectado:</td>--}}
    {{--<td width="20%">{{ $document_base->affected_document->series }}-{{ $document_base->affected_document->number }}</td>--}}
    {{--<td width="15%">Tipo de nota:</td>--}}
    {{--<td width="40%">{{ ($document_base->note_type === 'credit')?$document_base->note_credit_type->description:$document_base->note_debit_type->description}}</td>--}}
    {{--</tr>--}}
    {{--<tr>--}}
    {{--<td class="align-top">Descripción:</td>--}}
    {{--<td class="text-left" colspan="3">{{ $document_base->note_description }}</td>--}}
    {{--</tr>--}}
    {{--</table>--}}

    <table class="full-width mt-10 mb-10">
        <thead class="">
            <tr class="bg-grey">
                <th class="border-top-bottom text-center py-2" width="8%">Cant.</th>
                <th class="border-top-bottom text-center py-2" width="8%">Unidad</th>
                <th class="border-top-bottom text-left py-2">Descripción</th>
                <th class="border-top-bottom text-left py-2">Modelo</th>
                <th class="border-top-bottom text-center py-2" width="8%">Lote</th>
                <th class="border-top-bottom text-center py-2" width="8%">Serie</th>
                <th class="border-top-bottom text-right py-2" width="12%">P.Unit</th>
                <th class="border-top-bottom text-right py-2" width="8%">Dto.</th>
                <th class="border-top-bottom text-right py-2" width="12%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document->items as $row)
            <tr>
                <td class="text-center align-top">
                    @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                    @else
                    {{ number_format($row->quantity, 0) }}
                    @endif
                </td>
                <td class="text-center align-top">{{symbol_or_code( symbol_or_code($row->item->unit_type_id))}}</td>
                <td class="text-left align-top">
                    @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                    @else
                    {!!$row->item->description!!}
                    @endif

                      

                    @if($row->attributes)
                    @foreach($row->attributes as $attr)
                    <br /><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                    @endif
                    @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                    <br /><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                    @endif

                    @if($row->item->is_set == 1)
                    <br>
                    @inject('itemSet', 'App\Services\ItemSetService')
                    @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                    {{$item}}<br>
                    @endforeach
                    @endif

                    @if($document->has_prepayment)
                    <br>
                    *** Pago Anticipado ***
                    @endif
                </td>
                <td class="text-left align-top">{{ $row->item->model ?? '' }}</td>
                <td class="text-center align-top">
                    @inject('itemLotGroup', 'App\Services\ItemLotsGroupService')
                    {{ $itemLotGroup->getLote($row->item->IdLoteSelected) }}

                </td>
                <td class="text-center align-top">

                    @isset($row->item->lots)
                    @foreach($row->item->lots as $lot)
                    @if( isset($lot->has_sale) && $lot->has_sale)
                    <span style="font-size: 9px">{{ $lot->series }}</span><br>
                    @endif
                    @endforeach
                    @endisset

                </td>
                <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
                <td class="text-right align-top">
                    @if($row->discounts)
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
                <td colspan="9" class="border-bottom"></td>
            </tr>
            @endforeach



            @if ($document->prepayments)
            @foreach($document->prepayments as $p)
            <tr>
                <td class="text-center align-top">1</td>
                <td class="text-center align-top">NIU</td>
                <td class="text-left align-top">
                    Anticipo: {{($p->document_type_id == '02')? 'Factura':'Boleta'}} Nro. {{$p->number}}
                </td>
                <td class="text-center align-top"></td>
                <td class="text-center align-top"></td>
                <td class="text-center align-top"></td>
                <td class="text-right align-top">-{{ number_format($p->total, 2) }}</td>
                <td class="text-right align-top">0</td>
                <td class="text-right align-top">-{{ number_format($p->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="9" class="border-bottom"></td>
            </tr>
            @endforeach
            @endif

            @if($document->total_exportation > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Op. Exportación: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
            @endif
            @if($document->total_free > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Op. Gratuitas: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
            @endif
            @if($document->total_unaffected > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Op. Inafectas: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
            @endif
            @if($document->total_exonerated > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Op. Exoneradas: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
            @endif
            @if($document->total_taxed > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Op. Gravadas: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
            @endif
            @if($document->total_discount > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'Anticipo':'Descuento TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
            @endif
            @if($document->total_plastic_bag_taxes > 0)
            <tr>
                <td colspan="8" class="text-right font-bold">Icbper: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_plastic_bag_taxes, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="8" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
            </tr>
            @if($document->total_charge > 0)
            @php
            $total_factor = 0;
            foreach($document->charges as $charge) {
            $total_factor = ($total_factor + $charge->factor) * 100;
            }
            @endphp
            <tr>
                <td colspan="8" class="text-right font-bold">CARGOS ({{$total_factor}}%): {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_charge, 2) }}</td>
            </tr>
            @endif

            @if($document->perception)
            <tr>
                <td colspan="8" class="text-right font-bold"> Importe total: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="8" class="text-right font-bold">Percepción: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->perception->amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="8" class="text-right font-bold">Total a pagar: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(($document->total + $document->perception->amount), 2) }}</td>
            </tr>
            @else
            <tr>
                <td colspan="8" class="text-right font-bold">Total a pagar: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
            </tr>
            @endif

            @if($balance < 0) <tr>
                <td colspan="8" class="text-right font-bold">Vuelto: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format(abs($balance),2, ".", "") }}</td>
                </tr>

                @endif



        </tbody>
    </table>
    <table class="full-width">
        <tr>
            <td width="65%" style="text-align: top; vertical-align: top;">
                @foreach(array_reverse( (array) $document->legends) as $row)
                @if ($row->code == "1000")
                <p style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
                @if (count((array) $document->legends)>1)
                <p><span class="font-bold">Leyendas</span></p>
                @endif
                @else
                <p> {{$row->code}}: {{ $row->value }} </p>
                @endif

                @endforeach
                <br />
                @if ($document->detraction)
                <p>
                    <span class="font-bold">
                        Operación sujeta al Sistema de Pago de Obligaciones Tributarias
                    </span>
                </p>
                <br />
                @endif
                @if ($customer->department_id == 16)
                <br /><br /><br />
                <div>
                    <center>
                        Representación impresa del Comprobante de Pago Electrónico.
                        <br />Esta puede ser consultada en:
                        <br /><b>{!! url('/buscar') !!}</b>
                        <br /> "Bienes transferidos en la Amazonía
                        <br />para ser consumidos en la misma".
                    </center>
                </div>
                <br />
                @endif
                @foreach($document->additional_information as $information)
                @if ($information)
                @if ($loop->first)
                <strong>Información adicional</strong>
                @endif
                <p>{{ $information }}</p>
                @endif
                @endforeach
                <br>
                @if(in_array($document->document_type->id,['01','03']))
                @foreach($accounts as $account)
                <p>
                    <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                    <span class="font-bold">N°:</span> {{$account->number}}
                    @if($account->cci)
                    <span class="font-bold">CCI:</span> {{$account->cci}}
                    @endif
                </p>
                @endforeach
                @endif
            </td>
            <td width="35%" class="text-right">
                <img src="data:image/png;base64, {{ $document->qr }}" style="margin-right: -10px;" />
                <p style="font-size: 9px">Código Hash: {{ $document->hash }}</p>
            </td>
        </tr>
    </table>
    @php
    $paymentCondition = \App\CoreFacturalo\Helpers\Template\TemplateHelper::getDocumentPaymentCondition($document);
    @endphp
    {{-- Condicion de pago  Crédito / Contado --}}
    <table class="full-width">
        <tr>
            <td>
                <strong>Condición de Pago: {{ $paymentCondition  }} </strong>
            </td>
        </tr>
    </table>

    @if($document->payment_method_type_id)
    <table class="full-width">
        <tr>
            <td>
                <strong>Método de Pago: </strong>{{ $document->payment_method_type->description }}
            </td>
        </tr>
    </table>
    @endif

    @if ($document->payment_condition_id === '01')
    @if($payments->count())
    <table class="full-width">
        <tr>
            <td><strong>Pagos:</strong></td>
        </tr>
        @php $payment = 0; @endphp
        @foreach($payments as $row)
        <tr>
            <td>&#8226; {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment + $row->change }}</td>
        </tr>
        @endforeach
        </tr>
    </table>
    @endif
    @else
    <table class="full-width">
        @foreach($document->fee as $key => $quote)
        <tr>
            <td>&#8226; {{ (empty($quote->getStringPaymentMethodType()) ? 'Cuota #'.( $key + 1) : $quote->getStringPaymentMethodType()) }} / Fecha: {{ $quote->date->format('d-m-Y') }} / Monto: {{ $quote->currency_type->symbol }}{{ $quote->amount }}</td>
        </tr>
        @endforeach
        </tr>
    </table>
    @endif

    <br>
    <table class="full-width">
        <tr>
            <td>
                <strong>Vendedor:</strong>
            </td>
        </tr>
        <tr>
            @if ($document->seller)
            <td>{{ $document->seller->name }}</td>
            @else
            <td>{{ $document->user->name }}</td>
            @endif
        </tr>
    </table>

    <table class="full-width">
        @php
            $configuration = \App\Models\Tenant\Configuration::first();
            $establishment_data = \App\Models\Tenant\Establishment::find($document->establishment_id);
        @endphp
        <tbody>
            <tr>
                @if ($configuration->yape_qr_documents && $establishment_data->yape_logo)
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
                                    @if($establishment_data->yape_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->yape_owner }}
                                        </strong>
                                    @endif
                                    @if($establishment_data->yape_number)
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
                @if ($configuration->plin_qr_documents && $establishment_data->plin_logo)
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
                                    @if($establishment_data->plin_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->plin_owner }}
                                        </strong>
                                    @endif
                                    @if($establishment_data->plin_number)
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
    <table class="full-width">
        @php
            $configuration = \App\Models\Tenant\Configuration::first();
            $establishment_data = \App\Models\Tenant\Establishment::find($document->establishment_id);
        @endphp
        <tbody>
            <tr>
                @if ($configuration->yape_qr_documents && $establishment_data->yape_logo)
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
                                    @if($establishment_data->yape_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->yape_owner }}
                                        </strong>
                                    @endif
                                    @if($establishment_data->yape_number)
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
                @if ($configuration->plin_qr_documents && $establishment_data->plin_logo)
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
                                    @if($establishment_data->plin_owner)
                                        <strong>
                                            Nombre: {{ $establishment_data->plin_owner }}
                                        </strong>
                                    @endif
                                    @if($establishment_data->plin_number)
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