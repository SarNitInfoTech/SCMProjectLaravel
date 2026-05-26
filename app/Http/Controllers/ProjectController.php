<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Project List';
        $query = Project::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $projects = $query->orderBy('name')->paginate(10);

        return view('pages.projects.listProjects.listProjects', compact('title', 'projects'));
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
