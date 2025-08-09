<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Notification;
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
                'created_at' => $proj->created_at->format('d-m-Y'),
                'updated_at' => $proj->updated_at->format('d-m-Y'),
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

        return view('pages.projects.listProjects.listProjects', [
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
    // ✅ Validate project input
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    // ✅ Create the project
    $project = Project::create([
        'name' => $request->name,
    ]);

    // ✅ Create a notification
    Notification::create([
        'title'    => "New Project Created: {$project->name}",
        'link'     => route('projects.index'),
        'icon'     => 'la la-folder-plus',
        'bg_color' => 'bg-success',
        'is_read'  => false,
    ]);

    // ✅ Redirect with success message
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
