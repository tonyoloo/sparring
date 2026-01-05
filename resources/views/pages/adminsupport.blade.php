@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'mobilesupport',
])
{{-- @php
dd($cached_closed_loans['openloans']);
@endphp --}}
@section('content')
    <div class="form-group" id="overlay">
        <div class="spinner"></div>
    </div>
    <div class="content">
        <div id ="divopenloan" name = "divopenloan" class="row ">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">MANAGE USERS </h4>
                    </div>
                    <div class="card-body">
                    <table class="table table-bordered table-striped " id="datatableadmins">
                        <thead>
                            <tr>


                                <th>UsersId</th>
                                <th>SID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>NationalID</th>
                                <th>Role</th>
                                <th>Permission</th>
                                <th>Date Created</th>
                                <th>Updated at</th>
                                <th width="150px">Action</th>

                            </tr>
                        </thead>
                    </table>
                    </div>
                </div>

            </div>
        </div>

       









    </div>
@endsection
