@extends('layouts.admin')

@section('header', 'Modifier le chapitre')

@section('content')
    @include('admin.chapters.partials._form', ['courses' => $courses, 'chapter' => $chapter])
@endsection
