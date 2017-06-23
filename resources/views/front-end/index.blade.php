@extends('front-end.main')

@section('content')

    <p>kuku</p>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">x</button>
                <strong>{{$message}}</strong>
        </div>
    @endif
    {!! Session::forget('success') !!}
    @if($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">x</button>
                <strong>{{$message}}</strong>
            </div>
    {!! Session::forget('error') !!}

            {!! Form::open(['url' => route('paypal.payment')]) !!}

    <div class="form-group">
        {!! Form::label('amount', 'Enter ' . ucfirst('amount' . ':')) !!}
        {!! Form::text('amount', '', ['class' => 'form-control'])!!}<br/>
    </div>

    {!! Form::submit('Create' , ['class' => 'btn btn-success']) !!}

    {!! Form::close() !!}












@endsection

