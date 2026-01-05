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
                    <h4 class="card-title">OPEN PRODUCTS </h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped  ref" id="datatableloanstatus" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NAME</th>
                                <th>TYPE</th>

                                <th>ACADEMIC YEAR</th>
                                <th>APPLICATION COUNT</th>
                                <th>MOBILE COUNT</th>
                                <th>USSD COUNT</th>
                                <th>MINIAPP COUNT</th>
                                <th>IOS COUNT</th>

                                <th>CLOSE DATE</th>



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
                    <h4 class="card-title">UPDATE PORTAL NAMES</h4>
                </div>
                <div class="card-body">

                    <form id="axidform" name="axidform">
                        @csrf
                        <div class="form-group">
                            <label for="idnumber">ID/Index Number</label>
                            <input required type="number" class="form-control" id="idnumberax" name="idnumberax" aria-describedby="idnumber" placeholder="Enter id number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
                <div class="card-body">
                <div class="table-wrapper">

                    <table class="table table-bordered table-striped  ref" id="datatableportalname" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>IDNO</th>

                                <th>ACCOUNTNUMBER</th>
                                <th>EMAIL</th>
                                <th>NAME</th>
                                <th>PHONE</th>
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
                    <h4 class="card-title"> Immediately post MPESA records</h4>
                </div>
                <div class="card-body">

                    <form id="mpesaidform" name="mpesaidform">
                        @csrf
                        <div class="form-group">
                            <label for="mpesaidform">MPESA Document Number</label>
                            <input required type="text" class="form-control" id="documentno" name="documentno" aria-describedby="documentno" placeholder="Enter MPESA document number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

            </div>

        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Search if Application Was Successful/if student Qualifies for loan Application</h4>
                </div>
                <div class="card-body">

                    <form id="ifqualifiedform" name="ifqualifiedform">
                        @csrf


                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="applicantsubmittedetails" id="applicantsubmittedetails" class="form-control">
                                        @foreach ($cached_closed_loans['subsequentloansall'] as $names)
                                        <option value="{{ $names->name }}|{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                                            {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
                                        </option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT NATIONAL NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="nationalidnumber" name="nationalidnumber" aria-describedby="nationalidnumber" placeholder="Enter nationalid number">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
                <div class="card-body">
                <div class="table-wrapper">

                    <table class="table  table-bordered table-striped  ref" id="datatableifqualified" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NAMES</th>
                                <th>PRODUCT</th>

                                <th>SERIAL NUMBER</th>

                                <th>IDNUMBER</th>
                                <th>SUBMITTEDLOAN</th>
                                <th>submittedscholarship</th>

                                <th>SOURCE</th>
                                <th>date_loan_submit</th>
                                <th>date_sch_submit</th>
                                <th>disbursementoption</th>

                                <th>disbursementoptionvalue</th>
                                
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
                    <h4 class="card-title">Add institution details(solves Please ensure you made a previous application
                        and that you have not exhausted the number of allowable loans for your course.)</h4>
                </div>
                <div class="card-header">
                <h4 class="card-title">UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to new course and add course duration on AX then pull institution to allow student to apply
                </h4>
                </div>
                <div class="card-body">

                    <form id="addinstitutionform" name="addinstitutionform">
                        @csrf


                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="addinstitutiondet" id="addinstitutiondet" class="form-control">
                                        @foreach ($cached_closed_loans['subsequentloansall'] as $names)
                                        <option value="{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                                        {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
                                        </option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT NATIONAL NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="idinstitution" name="idinstitution" aria-describedby="idinstitution" placeholder="Enter nationalid number">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    @can('lending')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add institution details Extra Loan</h4>
                </div>
                <div class="card-header">
                    <h4 class="card-title">UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to new course and add course duration on AX then pull institution to allow student to apply
                    </h4>
                </div>
                <div class="card-body">

                    <form id="addinstitutionformextra" name="addinstitutionformextra">
                        @csrf


                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="addinstitutiondetextra" id="addinstitutiondetextra" class="form-control">
                                        @foreach ($cached_closed_loans['subsequentloansall'] as $names)
                                        <option value="{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                                        {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
                                        </option>
                                        @endforeach
                                    </select>


                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT NATIONAL NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="idinstitutionextra" name="idinstitutionextra" aria-describedby="idinstitution" placeholder="Enter nationalid number">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    @endcan

    @can('lending')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">ADD LATE APPLICANT SUBSEQUENT LOAN</h4>
            </div>
            <div class="card-header">
                <!-- <h4 class="card-title">UG Degree maximum depends on course duration,TVET artisan certificate maximum applications 1,TVET craft certificate maximum applications 2,DIPLOMA  maximum applications 3.For course progression TVET change to new course and add course duration on AX then pull institution to allow student to apply
                </h4> -->
            </div>
            <div class="card-body">

                <form id="addlateapplicant" name="addlateapplicant">
                    @csrf


                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-9">
                                <select name="addlateapplicant" id="addlateapplicant" class="form-control">
                                    @foreach ($cached_closed_loans['subsequentloansall'] as $names)
                                    <option value="{{ $names->productid }}|{{ $names->studentgrouping }}|{{ $names->academicyear }}|{{ $names->productcode }}">
                                    {{ $names->name }} {{ $names->type }} {{ $names->academicyear }}
                                    </option>
                                    @endforeach
                                </select>


                            </div>
                        </div>
                    </div>




                    <div class="form-group">
                        <div class="row">

                            <div class="col-md-9">
                                <label for="idnumber">APPLICANT NATIONAL NUMBER</label>
                                <input required type="number" min=12 class="form-control" id="idlateapplicant" name="idlateapplicant" aria-describedby="idlateapplicant" placeholder="Enter nationalid number">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Submit</button>
                </form>
            </div>

        </div>

    </div>
</div>

@endcan







    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">CHECK MOBILE APP & MINI APP DETAILS</h4>
                </div>
                <div class="card-body">

                    <form id="platform" name="platform">
                        @csrf
                        <div class="form-group">
                            <label for="platformnumber">Phone Number</label>
                            <input required type="number" class="form-control" id="platformnumber" name="platformnumber" aria-describedby="platformnumber" placeholder="Enter phone number">
                        </div>
                        <button type="submit" class="btn btn-success">Submit</button>

                    </form>
                </div>
                <div class="card-body">
                    <h6 class="card-title">ANDROID DETAILS</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped  ref" id="datatableandroid" style="width: 100%;">
                            <thead>
                                <tr>
                                <th>ID</th>

                                    <th>PHONE</th>
                                    <th>IDNO</th>
                                    <th>GSF</th>
                                   
                                    <th>DEVICE</th>
                                    <th>APP VERSION</th>
                                    <th>TIME</th>
                                    <th>ACTION</th>

                                </tr>
                            </thead>

                        </table></div>
                </div>
                <div class="card-body">
                    <h6 class="card-title">MINIAPP DETAILS</h4>
                    <div class="table-wrapper">

                        <table class="table table-bordered table-striped  ref" id="datatableminiapp" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>PHONE</th>

                                    <th>DEVICE</th>

                                    <th>TIME</th>
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
                    <h4 class="card-title">Phone number verification.Safaricom\Airtel</h4>
                </div>
                <div class="card-body">

                    <form id="phonenumberverifyform" name="phonenumberverifyform">
                        @csrf


                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-9">
                                    <select name="phonenumberdropdown" id="phonenumberdropdown" class="form-control">
                                        <option value="safaricom">Safaricom</option>
                                        <option value="airtel">Airtel</option>
                                    </select>



                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT PHONE NUMBER</label>
                                    <input required type="number" min=12 class="form-control" id="phoneverify" name="phoneverify" aria-describedby="phoneverify" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT ID NUMBER</label>
                                    <input required type="number" class="form-control" id="idphoneverify" name="idphoneverify" aria-describedby="idphoneverify" placeholder="Enter id number">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">

                                <div class="col-md-9">
                                    <label for="idnumber">APPLICANT FIRST NAME</label>
                                    <input required type="text" class="form-control" id="idphoneverifyname" name="idphoneverifyname" aria-describedby="idphoneverifyname" placeholder="Enter first name">
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