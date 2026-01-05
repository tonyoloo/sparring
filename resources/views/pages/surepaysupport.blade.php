@extends('layouts.app', [
'class' => '',
'elementActive' => 'surepaysupport',
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
                    <h4 class="card-title">UNLOCK ACCOUNTS -FOR FUNDS DISBURSED BY INDEX INSTEAD OF NATIONAL ID</h4>
                    <!-- <h4 class="card-title">FETCH INDEX AND INSTITUTION  FROM HEF PORTAL AND UPDATES TO ID ON MOBILE PORTAL FOR APPLICANTS ALLOWED </h4> -->

                </div>
                <div class="card-body">

                    <form id="allocateform" name="allocateform">
                        @csrf

                        <div class="form-group">
                            <label for="academicyear">KCSE YEAR</label>

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="academicyear" id="academicyear" class="form-control">
                                        @php
                                        $startYear = 2020;
                                        $currentYear = date('Y');
                                        @endphp
                                        @for ($year = $startYear; $year <= $currentYear; $year++)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                            @endfor
                                    </select>



                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="indexumber">INDEX Number</label>
                            <input required type="number" class="form-control" id="indexnumber" name="indexnumber"
                                aria-describedby="indexnumber" placeholder="Enter index number">
                        </div>
                        <div class="form-group">
                            <label for="idumber">ID Number</label>
                            <input required type="number" class="form-control" id="idnumber" name="idnumber"
                                aria-describedby="idnumber" placeholder="Enter id number">
                        </div>
                        <button type="submit" class="btn btn-danger">UNLOCK</button>
                    </form>
                </div>


            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SEARCH STATUS FOR ACCOUNTS THAT HAVE BEEN UNLOCKED</h4>
                </div>




                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT RE-ALLOCATION</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped dataTables_scroll" id="datatablepaymentrealocation" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>clawbackid</th>
                                    <th>reallocated</th>

                                    <th>allocatedQuota</th>
                                    <th>remainingAmount</th>

                                    <th>phoneNumber</th>
                                    <th>currency</th>

                                    <th>expiredTime</th>
                                    <th>fundType</th>
                                    <th>groupCode</th>

                                    <th>providerId</th>
                                    <th>batchno</th>
                                    <th>idno</th>

                                    <th>institutioncode</th>
                                    <th>loanserialno</th>
                                    <th>date_created</th>
                                    <th>update_time</th>
                                    <th>action</th>






                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> MOBILE PAYMENT WITHDRAWALS</h4>
                </div>
                <div class="card-body">

                    <form id="withdrawform" name="withdrawform">
                        @csrf
                        <div class="form-group">
                            <label for="idnumber">Phone Number</label>
                            <input required type="number" class="form-control" id="paynumber" name="paynumber"
                                aria-describedby="paynumber" placeholder="Enter pay number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
                <div class="card-body">
                    <h4 class="card-title">WITHDRAWAL STATEMENT</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped  ref" id="datatablewithdrawalstats" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Amount</th>

                                    <th>createTime</th>

                                    <th>Withdrawn By</th>
                                    <th>Details</th>
                                    <th>Mpesa Transaction ID</th>
                                    <th>Status</th>




                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> MOBILE PAYMENT PHONE SEARCH</h4>
                </div>
                <div class="card-body">

                    <form id="mobipayform" name="mobipayform">
                        @csrf
                        <div class="form-group">
                            <label for="idnumber">Phone Number</label>
                            <input required type="number" class="form-control" id="paynumber" name="paynumber"
                                aria-describedby="paynumber" placeholder="Enter pay number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
                <div class="card-body">
                    <h4 class="card-title">KYC MOBILE PAYMENT</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped  ref" id="datatableappliedstats" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>fullName</th>
                                    <th>ID Number</th>
                                    <th>Phonenumber</th>
                                    <th>idType</th>
                                    <th>kycFlag</th>
                                    <th>status</th>
                                    <th>userStatus</th>
                                    <th>userType</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT DETAILS</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped " id="datatablepaymentnumber" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Loan Serial No</th>
                                    <th>Admission Number</th>
                                    <th>Institution Code</th>
                                    <th>Institution</th>
                                    <th>Email</th>

                                    <th>Loan Product Code</th>
                                    <th>ID No</th>
                                    <th>Product Description</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>

                                    <th>Middle Name</th>
                                    <th>Applicant Type</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT ALLOCATION</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped dataTables_scroll" id="datatablepaymentalocation" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>allocatedQuota</th>
                                    <th>status</th>
                                    <th>batchno</th>

                                    <th>beneficiaryIdentifier</th>
                                    <th>clawBackAmount</th>
                                    <th>createTime</th>

                                    <th>idno</th>
                                    <th>institutioncode</th>
                                    <th>loanserialno</th>

                                    <th>expiredAmount</th>
                                    <th>expiredTime</th>
                                    <th>unutilized Amount</th>

                                    <th>utilized Amount</th>

                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> MOBILE PAYMENT ID NUMBER SEARCH</h4>
                </div>
                <div class="card-body">

                    <form id="idnumbersearchmobipayform" name="idnumbersearchmobipayform">
                        @csrf
                        <div class="form-group">
                            <label for="idnumber">ID Number</label>
                            <input required type="text" class="form-control" id="idnumbersearch" name="idnumbersearch"
                                aria-describedby="idnumbersearch" placeholder="Enter ID number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
                <div class="card-body">
                    <h4 class="card-title">KYC MOBILE PAYMENT</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped  ref" id="idnumbersearchdatatableappliedstats" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>fullName</th>
                                    <th>ID Number</th>
                                    <th>Phonenumber</th>
                                    <th>idType</th>
                                    <th>kycFlag</th>
                                    <th>status</th>
                                    <th>userStatus</th>
                                    <th>userType</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT DETAILS</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped " id="idnumbersearchdatatablepaymentnumber" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Loan Serial No</th>
                                    <th>Admission Number</th>
                                    <th>Institution Code</th>
                                    <th>Institution</th>
                                    <th>Email</th>

                                    <th>Loan Product Code</th>
                                    <th>Product Description</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>

                                    <th>Middle Name</th>
                                    <th>Applicant Type</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>

                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT ALLOCATION</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped dataTables_scroll" id="idnumbersearchdatatablepaymentalocation" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>allocatedQuota</th>
                                    <th>status</th>
                                    <th>batchno</th>
                                    <th>beneficiaryIdentifier</th>
                                    <th>clawBackAmount</th>
                                    <th>createTime</th>
                                    <th>idno</th>
                                    <th>institutioncode</th>
                                    <th>loanserialno</th>
                                    <th>expiredAmount</th>
                                    <th>expiredTime</th>
                                    <th>unutilized Amount</th>
                                    <th>utilized Amount</th>

                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">SEARCH ALL ALLOCATIONS DONE FOR A PARTICULAR PHONE/ID NUMBER</h4>
                </div>




                <div class="card-body">
                    <h4 class="card-title">MOBILE PAYMENT ALLOCATION</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped dataTables_scroll" id="bulkdatatablepaymentalocation" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>allocatedQuota</th>
                                    <th>status</th>
                                    <th>batchno</th>
                                    <th>beneficiaryIdentifier</th>
                                    <th>clawBackAmount</th>
                                    <th>createTime</th>
                                    <th>idno</th>
                                    <th>institutioncode</th>
                                    <th>loanserialno</th>
                                    <th>expiredAmount</th>
                                    <th>expiredTime</th>
                                    <th>unutilized Amount</th>
                                    <th>utilized Amount</th>

                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


</div>
@endsection