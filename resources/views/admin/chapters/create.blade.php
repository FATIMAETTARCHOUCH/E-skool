@extends('layouts.admin')

@section('header', 'Add chapter')

@section('content')
    @include('admin.chapters.partials._form', ['courses' => $courses, 'course_id' => $course_id])
@endsection
