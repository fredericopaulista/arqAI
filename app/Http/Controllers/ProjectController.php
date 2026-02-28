<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ArchitectAgentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(protected ArchitectAgentService $agentService)
    {
    }

    public function index()
    {
        $projects = Project::orderBy('created_at', 'desc')->get();
        return Inertia::render('Projects/Index', [
            'projects' => $projects
        ]);
    }

    public function status(Project $project)
    {
        return response()->json($project);
    }

    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB
        ]);

        if (Auth::user()->credits <= 0) {
            return back()->withErrors(['message' => 'Você não possui créditos suficientes.']);
        }

        $path = $request->file('image')->store('projects/originals', 'public');

        $project = Project::create([
            'user_id' => Auth::id(),
            'original_image_path' => $path,
            'status' => 'collecting_data',
        ]);

        // Start briefing
        $initialQuestion = $this->agentService->getNextQuestion($project);
        $project->conversations()->create([
            'role' => 'assistant',
            'message' => $initialQuestion
        ]);

        return redirect()->route('projects.chat', $project->id);
    }

    public function chat(Project $project)
    {
        $project->load('conversations');
        return Inertia::render('Projects/Chat', [
            'project' => $project
        ]);
    }

    public function sendMessage(Request $request, Project $project)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $response = $this->agentService->processResponse($project, $request->message);

        return back();
    }

    public function show(Project $project)
    {
        return Inertia::render('Projects/Show', [
            'project' => $project
        ]);
    }
}
