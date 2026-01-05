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
    <div id="divopenloan" name="divopenloan" class="row ">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">USERS </h4>
                </div>
                <div class="card-body">
                <div class="table-wrapper">

                    <table class="table table-bordered table-striped  ref" id="datatableusers" style="width: 100%;">
                        <thead>
                            <tr>
                            <th>UsersId</th>
                    <th>SID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date Created</th>
                    <th>Updated at</th>
                    <th>Action</th>


                            </tr>
                        </thead>

                    </table>
                </div></div>
            </div>

        </div>
    </div>
</div>
  <!--************************************************************************************-->
  <div id="ajax-editpriviledges-modal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
            aria-labelledby="classInfo" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title" id="editpriviledgesCrudModal"></h6>
                        <hr><br><br>
                        <small id="fullNameHelp" class="form-text text-muted">user roles information</small>
                        <hr>
                        <br><br>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            ×
                        </button>

                    </div>
                    <div class="modal-body">
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped " id="datatableuseroles">
                            <thead>
                                <tr>



                                    <th>SID</th>

                                    <th>current role/roles</th>


                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped " id="datatableuserpermissions">
                            <thead>
                                <tr>



                                    <th>SID</th>

                                    <th>current permissions</th>


                                </tr>
                            </thead>
                        </table>
                    </div>
                        <small id="fullNameHelp" class="form-text text-muted">delete/add roles to user</small>
                        <hr>
                        <br><br>
                        <input name="useridentifier" id="useridentifier">
                        <form id="updateroleform" name="updateroleform" class="form-horizontal">

                        <div class="table-wrapper">

                            <table class="table table-bordered table-striped " id="datatableallroles">
                                <thead>
                                    <tr>



                                        <th>assign role</th>
                                        <th>sid</th>
                                        <th>Role</th>


                                    </tr>
                                </thead>
                            </table>
                        </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="btn-save-updaterole"
                                    value="create">UPDATE
                                </button>
                            </div>
                        </form>
                        <small id="fullNameHelp" class="form-text text-muted">delete/add permission to user</small>
                        <hr>
                        <br><br>
                        <input name="useridentifier" id="useridentifier">
                        <form id="updatepermissionform" name="updateroleform" class="form-horizontal">

                        <div class="table-wrapper">

                            <table class="table table-bordered table-striped " id="datatableallpermission">
                                <thead>
                                    <tr>



                                        <th>assign permission</th>
                                        <th>sid</th>
                                        <th>permission</th>


                                    </tr>
                                </thead>
                            </table>
                        </div>
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="btn-save-updatepermission"
                                    value="create">UPDATE
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!--************************************************************************************-->

@endsection