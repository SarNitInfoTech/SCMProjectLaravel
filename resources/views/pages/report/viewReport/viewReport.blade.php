@extends("layouts.layout")

@section("bodyContent")

@include("common.table.commonTableWithFilter", [
    'title' => 'PO Report by Indent',
    'columns' => $columns,
    'rows' => $reports,
    'pagination' => $reports,
    'searchPlaceholder' => 'Search by PO No, Party, or Department...',
    'enableDateFilter' => true,
    'dateFieldKey' => 'po_created_at',
])
@endsection
