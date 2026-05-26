@extends("layouts.layout")

@section("bodyContent")
@include("containers.indent.generateIndent.generateIndent")
<br>

<div class="card shadow-sm border mb-6 bg-white">
    <div class="card-header p-4 border-b">
        <h3 class="text-xl font-semibold text-gray-800">{{ $title }}</h3>
    </div>

    <div class="table-responsive p-4">
        <table class="table whitespace-nowrap min-w-full">
            <thead>
                <tr class="border-b border-defaultborder">
                    <th scope="col" class="text-start">Indent ID</th>
                    <th scope="col" class="text-start">Department Name</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-defaultborder hover:bg-gray-50 transition-colors">
                        <td class="font-medium text-gray-900">{{ $row['indent_id'] }}</td>
                        <td>{{ $row['department_name'] }}</td>
                        <td class="text-center">
                            <a href="{{ $row['action'] }}" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-green-600 hover:bg-green-700 rounded transition-all">
                                <i class="bi bi-pencil-square"></i> Generate Form
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-500">No draft tickets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection