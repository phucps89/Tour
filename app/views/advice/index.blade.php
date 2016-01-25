@extends('master')

@section('breadcrumb')
    <li>List advices</li>
@endsection

@section('titleSection')
    Dashboard
    <small>Control panel</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <a class="btn btn-primary" data-toggle="modal" data-target="#newAdvice"><i class="fa fa-plus"></i> <span>Add</span></a>
            <hr>
            <div class="box">
                <div class="box-body">
                    <table id="example2" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Percent</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($advices as $a)
                            <?php
                                $percent = round(($a->sumQuestion/$sumQuestion) * 100);
                            ?>
                            <tr>
                                <td>{{$a->id}}</td>
                                <td>{{$a->name}}</td>
                                <td>
                                    <span>{{$percent}}% Complete</span>
                                    <div class="progress progress-xs active">
                                        <div class="progress-bar progress-bar-warning progress-bar-striped" role="progressbar" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$percent}}%">
                                        </div>
                                    </div>
                                </td>
                                <td>{{$a->updated_at->format('m/d/Y H:i:s')}}</td>
                                <td>
                                    <a href="{{route('advice.view', ['id' => $a->id])}}" title="View detail" class="btn btn-primary"><i class="fa fa-search"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Percent</th>
                            <th>Updated at</th>
                            <th>Action</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="newAdvice">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{route('advice.function')}}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Modal Default</h4>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
                            <span class="input-group-addon">Name</span>
                            <input type="text" name="name" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                        <input type="hidden" name="function" value="newActive">
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('resource')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{asset('dashboard/plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('script')
    <script src="{{asset('dashboard/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('dashboard/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $('#example2').DataTable();
        });
    </script>
@endsection