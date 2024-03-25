@extends('errors::minimal')

@section('title', __(__local('Forbidden')))
@section('code', '403')
@section('message', __($exception->getMessage() ?: __local('Forbidden')))
