@extends('layouts.admin')

@section('header', 'Edit chapter')

@section('content')
    @include('admin.chapters.partials._form', ['courses' => $courses, 'chapter' => $chapter])
@endsection
