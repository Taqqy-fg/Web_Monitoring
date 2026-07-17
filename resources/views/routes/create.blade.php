@extends('layouts.app')

@section('content')

<div class="card">

<div class="card-header bg-primary text-white">

<h4>

Tambah Route Monitoring

</h4>

</div>

<div class="card-body">

<form action="{{ route('routes.store') }}" method="POST">

@csrf

<div class="mb-3">

<label>

Project

</label>

<select name="project_id" class="form-control">

@foreach($projects as $project)

<option value="{{ $project->id }}">

{{ $project->name }}

</option>

@endforeach

</select>

</div>

<div class="mb-3">

<label>

Route Name

</label>

<input type="text"

name="route_name"

class="form-control"

required>

</div>

<div class="mb-3">

<label>

Path

</label>

<input type="text"

name="path"

class="form-control"

placeholder="/api/login"

required>

</div>

<div class="mb-3">

<label>

Method

</label>

<select name="method"

class="form-control">

<option>

GET

</option>

<option>

POST

</option>

<option>

PUT

</option>

<option>

DELETE

</option>

</select>

</div>

<div class="mb-3">

<label>

Group

</label>

<input type="text"

name="route_group"

class="form-control"

placeholder="API">

</div>

<div class="mb-3">

<label>

Monitoring

</label>

<select

name="is_monitor"

class="form-control">

<option value="1">

Aktif

</option>

<option value="0">

Nonaktif

</option>

</select>

</div>

<button class="btn btn-success">

Simpan

</button>

<a href="{{ route('routes.index') }}"

class="btn btn-secondary">

Kembali

</a>

</form>

</div>

</div>

@endsection