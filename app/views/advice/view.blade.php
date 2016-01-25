@extends('master')

@section('breadcrumb')
    <li>List advices</li>
@endsection

@section('titleSection')
    Dashboard
    <small>Control panel</small>
    <hr>
@endsection

@section('content')
<form method="post" action="{{route('advice.function')}}">
    <div class="row">
        @if($select == 'root')
            @foreach($questions as $key=>$q)
            <div class="col-xs-3">
                <p style="font-weight: bold">{{$key+1}}. {{$q->name}}</p>
                <div class="form-group">
                @foreach($q->answers as $a)
                    <?php $answer = $a->detail ?>
                    <div class="radio">
                        <label>
                            <input name="question[{{$q->id}}]" required type="radio" value="{{$answer->id}}">
                            {{$answer->name}}
                            @if($q->id == 1)
                            &nbsp;-&nbsp;{{Location::getLocation($answer->name)->name}}
                            @endif
                        </label>
                    </div>
                @endforeach
                </div>
            </div>
            @endforeach
        @elseif($select == 'next')
            <div class="col-xs-12">
                <p style="font-weight: bold">{{$question->name}}</p>
                <div class="form-group">
                    @foreach($question->answers as $a)
                        <?php $answer = $a->detail ?>
                        <div class="radio">
                            <label>
                                <input name="question[{{$question->id}}][]" type="checkbox" value="{{$answer->id}}">
                                {{$answer->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div class="row">
        <div class="col-xs-12">
            <hr>
            <button type="submit" class="btn btn-primary">Submit</button>
            <input type="hidden" name="function" value="advice">
            <input type="hidden" name="select" value="{{$select}}">
            <input type="hidden" name="advice" value="{{$id}}">
        </div>
    </div>
</form>
@endsection


@section('script')

@endsection