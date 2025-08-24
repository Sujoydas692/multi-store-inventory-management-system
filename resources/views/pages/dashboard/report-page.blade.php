@extends('layouts.sidenav-layout')
@section('content')
<style>
    html, body {
        height: 100%;
        margin: -30px 0px 0px 0px;
        overflow: hidden; /* 🔑 prevents scrolling */
    }
</style>
    <div class="container-fluid d-flex justify-content-center align-items-center vh-100">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6 col-lg-6">
                <div class="card shadow-lg p-4" style="width: 450px;">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Sales Report</h3>
                        <label class="form-label mt-2">Date From</label>
                        <input id="FormDate" type="date" class="form-control" />

                        <label class="form-label mt-2">Date To</label>
                        <input id="ToDate" type="date" class="form-control" value="{{ date('Y-m-d') }}" />

                        <div class="text-center">
                            <button onclick="SalesReport()" class="btn mt-3 bg-gradient-primary">Download</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>

    function SalesReport() {
        let FormDate = document.getElementById('FormDate').value;
        let ToDate = document.getElementById('ToDate').value;

        if (FormDate.length === 0 || ToDate.length === 0) {
            errorToast("Date Range Required !")
        } else {
            window.open('/backend/sales-report/' + FormDate + '/' + ToDate);
        }

    }

</script>