@extends('layouts.app')

@section('content')

<div class="card">

<div class="card-header bg-warning">

<h4>Edit Route</h4>

</div>

<div class="card-body">

<form action="{{ route('routes.update',$route->id) }}" method="POST">

@csrf

@method('PUT')

<div class="mb-3">

<label>Project</label>

<select name="project_id" class="form-control">

@foreach($projects as $project)

<option value="{{ $project->id }}"
{{ $project->id == $route->project_id ? 'selected' : '' }}>

{{ $project->name }}

</option>

@endforeach

</select>

</div>

<div class="mb-3">

<label>Route Name</label>

<input

type="text"

name="route_name"

class="form-control"

value="{{ $route->route_name }}">

</div>

<div class="mb-3">

<label>Path</label>

<input

type="text"

name="path"

class="form-control"

value="{{ $route->path }}">

</div>

<div class="mb-3">

<label>Method</label>

<select name="method" class="form-control">

@foreach(['GET','POST','PUT','DELETE'] as $m)

<option value="{{ $m }}"
{{ $route->method==$m?'selected':'' }}>

{{ $m }}

</option>

@endforeach

</select>

</div>

<div class="mb-3">

<label>Group</label>

<input

type="text"

name="route_group"

class="form-control"

value="{{ $route->route_group }}">

</div>

<div class="mb-3">

<label>Status</label>

<select name="is_monitor" class="form-control">

<option value="1" {{ $route->is_monitor?'selected':'' }}>

Aktif

</option>

<option value="0" {{ !$route->is_monitor?'selected':'' }}>

Nonaktif

</option>

</select>

</div>

<button class="btn btn-success">

Update

</button>

<a href="{{ route('routes.index') }}" class="btn btn-secondary">

Kembali

</a>

</form>

</div>

</div>

@endsection