@extends('master')

@section('content')
    <section class="sectionHome">
        <div class="row" style="height: 30px">
        </div>
        <div class="row" style="margin-bottom: 0">
            <div class="col s6 offset-s3" style="padding-bottom: 2rem">
                <ul class="collapsible popout" data-collapsible="accordion">
                    <li>
                        <div class="collapsible-header"><i class="fa fa-question-circle"></i>Các câu hỏi bắt buộc!</div>
                        <div class="collapsible-body">
                            <ol class="listIndex padding">
                                @foreach($defaultQuestions as $question)
                                    <li>{{$question->question}}</li>
                                @endforeach
                            </ol>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
    </section>
@endsection