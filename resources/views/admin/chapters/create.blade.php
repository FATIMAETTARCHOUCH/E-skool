@extends('layouts.admin')

@section('header', 'Ajouter un chapitre')

@section('content')
    @include('admin.chapters.partials._form', ['courses' => $courses, 'course_id' => $course_id])
@endsection
