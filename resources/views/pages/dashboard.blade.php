@extends('layouts.app', [
'class' => '',
'elementActive' => 'dashboard',
])
{{-- @php
dd($cached_closed_loans['openloans']);
@endphp --}}
@section('content')
<div class="form-group" id="overlay">
    <div class="spinner"></div>
</div>

@endsection