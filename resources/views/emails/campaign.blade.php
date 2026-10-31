@extends('emails.layout', ['subject' => $subject])

@section('content')
    {!! $renderedContent !!}
@endsection
