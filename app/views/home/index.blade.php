@extends('master')

@section('content')
    <section class="sectionHome">
        <div class="row" style="height: 530px">
        </div>
        <div class="row" style="margin-bottom: 0">
            <div class="col s6 offset-s3" style="text-align: center; padding-bottom: 2rem">
                <h4>BẠN ĐANG PHÂN VÂN KHÔNG BIẾT NÊN ĐI DU LỊCH NGHỈ DƯỞNG Ở ĐÂU ?</h4>
                <h5>Hãy để chúng tôi tư vấn cho bạn !</h5>
                <a class="waves-effect waves-light btn btn-large" href="{{route('main')}}"><i class="fa fa-hand-o-right right"></i>suggestion</a>
            </div>
        </div>
    </section>
@endsection