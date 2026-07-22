<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        'name' => 'required|string|max:255|unique:projects,name',
    ]);

    // ✅ Create the project
    $project = Project::create([
        'name' => trim($request->name),
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
        $project = Project::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('projects', 'name')->ignore($project->id)],
        ]);

        $project->update([
            'name' => trim($request->name),
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
