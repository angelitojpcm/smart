@extends('tenant.layouts.app')

@section('content')
    <tenant-sale-notes-index :is-commercial="{{ json_encode($is_comercial) }}"
        :is-integrate-system="{{ json_encode($is_integrate_system) }}" :soap-company="{{ json_encode($soap_company) }}"
        :type-user="{{ json_encode(auth()->user()->type) }}"
        :configuration="{{ \App\Models\Tenant\Configuration::getPublicConfig() }}"></tenant-sale-notes-index>
@endsection
