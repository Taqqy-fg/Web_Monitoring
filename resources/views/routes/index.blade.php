@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Routes Monitoring</h2>

    <a href="{{ route('routes.create') }}" class="btn btn-primary">
        Tambah Route
    </a>

</div>

<table class="table table-bordered table-hover">

    <thead class="table-dark">

    <tr>

        <th>No</th>

        <th>Website</th>

        <th>Route Name</th>

        <th>Path</th>

        <th>Method</th>

        <th>Group</th>

        <th>Status</th>

        <th width="150">Action</th>

    </tr>

    </thead>

    <tbody>

    @forelse($routes as $route)

    <tr>

        <td>{{ $loop->iteration }}</td>

        <td>{{ $route->project->name }}</td>

        <td>{{ $route->route_name }}</td>

        <td>{{ $route->path }}</td>

        <td>

            <span class="badge bg-primary">

                {{ $route->method }}

            </span>

        </td>

        <td>{{ $route->route_group }}</td>

        <td>

            @if($route->is_monitor)

                <span class="badge bg-success">

                    Aktif

                </span>

            @else

                <span class="badge bg-secondary">

                    Nonaktif

                </span>

            @endif

        </td>

        <td>

            <a href="{{ route('routes.edit',$route->id) }}" class="btn btn-warning btn-sm">

                Edit

            </a>

            <form action="{{ route('routes.destroy',$route->id) }}" method="POST" class="d-inline">

                @csrf

                @method('DELETE')

                <button class="btn btn-danger btn-sm">

                    Delete

                </button>

            </form>

        </td>

    </tr>

    @empty

    <tr>

        <td colspan="8" class="text-center">

            Belum ada route.

        </td>

    </tr>

    @endforelse

    </tbody>

</table>

{{ $routes->links() }}

@endsection