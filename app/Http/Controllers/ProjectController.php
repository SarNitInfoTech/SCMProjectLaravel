<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $title = 'Project List';

        $columns = [
            ['key' => 'name', 'label' => 'Project Name'],
            ['key' => 'created_at', 'label' => 'Created At'],
            ['key' => 'updated_at', 'label' => 'Updated At'],
            ['key' => 'action', 'label' => 'Action', 'type' => 'action'],
        ];

        $projects = Project::select('id', 'name', 'created_at', 'updated_at')->paginate(10);

        $rows = $projects->map(function ($proj) {
            return [
                'name' => $proj->name,
                'created_at' => $proj->created_at->format('Y-m-d H:i'),
                'updated_at' => $proj->updated_at->format('Y-m-d H:i'),
                'action' => route('projects.edit', $proj->id),
            ];
        });

        $searchPlaceholder = 'Search projects...';
        $redirectUrl = route('projects.create');

        $customButton = <<<HTML
<a href="{$redirectUrl}" class="ti-btn ti-btn-primary-full">
    <i class="bi bi-plus-lg"></i>
    Add New Project
</a>
HTML;

        return view('pages.projects.projects', [
            'title' => $title,
            'columns' => $columns,
            'rows' => $rows,
            'searchPlaceholder' => $searchPlaceholder,
            'customButton' => $customButton,
            'pagination' => $projects,
        ]);
    }

    public function create()
    {
        return view('pages.projects.addProjects.addProjects');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Project::create([
            'name' => $request->name,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        return view('pages.projects.editProjects.editProjects', compact('project'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::findOrFail($id);
        $project->update([
            'name' => $request->name,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }
}
