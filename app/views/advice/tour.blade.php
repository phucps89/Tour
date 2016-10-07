@extends('master')

@section('breadcrumb')
    <li>List available tour</li>
@endsection

@section('titleSection')
    Dashboard
    <small>Control panel</small>
    <hr>
@endsection

@section('content')
    <div class="roe">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Day duration</th>
                            <th>Duration</th>
                            <th>Start day</th>
                            <th>Start Location</th>
                            <th>Adult Price</th>
                            <th>Children price</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tours as $tour)
                            <tr>
                                <td>{{$tour->id}}</td>
                                <td>{{$tour->code}}</td>
                                <td>{{$tour->name}}</td>
                                <td>{{$tour->day_duration}}</td>
                                <td>{{$tour->duration}}</td>
                                <td>{{$tour->start_date}}</td>
                                <td>{{$tour->locationName}}</td>
                                <td>{{number_format($tour->adult_price)}}</td>
                                <td>{{number_format($tour->children_price)}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Day duration</th>
                            <th>Duration</th>
                            <th>Start day</th>
                            <th>Start Location</th>
                            <th>Adult Price</th>
                            <th>Children price</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')

@endsection