@extends('tenant.layouts.app')

@section('content')

    <tenant-inventory-report
    :configuration="{{\App\Models\Tenant\Configuration::getPublicConfig()}}"
    ></tenant-inventory-report>

@endsection
