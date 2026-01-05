@extends('layouts.app', [
    'class' => '',
    'elementActive' => 'ussdsupport',
])
{{-- @php
dd($cached_closed_loans['openloans']);
@endphp --}}
@section('content')
    <div class="form-group" id="overlay">
        <div class="spinner"></div>
    </div>
    <div class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">SEARCH USSD RECORD USING PHONE/ID NUMBER</h4>
                    </div>
                    <div class="card-body">

                        <form id="ussdverifyform" name="ussdverifyform">
                            @csrf
                         <div class="form-group">
                                <div class= "row">

                                    <div class="col-md-9">
                                        <label for="idnumber">APPLICANT PHONE NUMBER</label>
                                        <input required type="number" min=12 class="form-control" id="phoneussd"
                                            name="phoneussd" aria-describedby="phoneussd"
                                            placeholder="Enter phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class= "row">

                                    <div class="col-md-9">
                                        <label for="idnumber">APPLICANT ID NUMBER/KUCPPS ID</label>
                                        <input required type="number" class="form-control" id="numberussd"
                                            name="numberussd" aria-describedby="numberussd"
                                            placeholder="Enter id/kuccps number">
                                    </div>
                                </div>
                            </div>





                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">USSD DETAILS</h4>

                            <table class="table table-bordered table-striped  ref" id="datatableussdetails" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>UNIQUE ID</th>
                                        <th>FIRST NAME</th>
                                        <th>ID NUMBER</th>
                                         <th>PHONE NUMBER</th>
                                        <th>DATE OF BIRTH</th>
                                        <th>LOGIN FLAG</th>
                                    </tr>
                                </thead>

                            </table>
                    </div>

                </div>

            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title"> Update ussd record password/UNBLOCKS ussd record/RESET USSD PIN</h4>
                        <h6 class="card-title">search phone number used by student to register *642#.NB he/she could have used more than one number
                            get the user_id for the phone number you want to change password and update
                            sms with password will be sent</h6>

                    </div>
                    <div class="card-body">

                        <form id="updateussdform" name="updateussdform">
                            @csrf
                            <div class="form-group">
                                <label for="updateussdform">UNIQUE ID</label>
                                <input required type="text" class="form-control" id="uniqueuserid" name="uniqueuserid"
                                    aria-describedby="documentno" placeholder="Enter unique ID for USSD">
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <h4 class="card-title">UPDATED USSD DETAILS</h4>

                            <table class="table table-bordered table-striped  ref" id="datatableupdatedussdetails" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>UNIQUE ID</th>
                                        <th>FIRST NAME</th>
                                        <th>ID NUMBER</th>
                                         <th>PHONE NUMBER</th>
                                        <th>DATE OF BIRTH</th>
                                        <th>LOGIN FLAG</th>
                                    </tr>
                                </thead>

                            </table>
                    </div>

                </div>

            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">CHANGE USSD Phone number .Safaricom\Airtel</h4>
                    </div>
                    <div class="card-body">

                        <form id="updateussdfm" name="updateussdfm">
                            @csrf


                            <div class="form-group">

                                <div class= "row">

                                    <div class="col-md-9">
                                        <select name="phonenumberdropdown" id="phonenumberdropdown" class="form-control">
                                            <option value="safaricom">Safaricom</option>
                                            <option value="airtel">Airtel</option>
                                        </select>



                                    </div>
                                </div>
                            </div>




                            <div class="form-group">
                                <div class= "row">

                                    <div class="col-md-9">
                                        <label for="idnumber">APPLICANT NEW PHONE NUMBER</label>
                                        <input required type="number" min=12 class="form-control" id="phoneverify"
                                            name="phoneverify" aria-describedby="phoneverify"
                                            placeholder="Enter phone number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class= "row">

                                    <div class="col-md-9">
                                        <label for="idnumber">APPLICANT ID NUMBER</label>
                                        <input required type="number" class="form-control" id="idphoneverify"
                                            name="idphoneverify" aria-describedby="idphoneverify"
                                            placeholder="Enter id number">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class= "row">

                                    <div class="col-md-9">
                                        <label for="idnumber">APPLICANT FIRST NAME</label>
                                        <input required type="text" class="form-control" id="idphoneverifyname"
                                            name="idphoneverifyname" aria-describedby="idphoneverifyname"
                                            placeholder="Enter first name">
                                    </div>
                                </div>
                            </div>



                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>

                </div>

            </div>
        </div>















    </div>
@endsection
