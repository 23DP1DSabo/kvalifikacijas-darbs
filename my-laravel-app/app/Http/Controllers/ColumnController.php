<?php

namespace App\Http\Controllers;

use App\Models\BoardColumn;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function index(Request $request)
    {
        $columns = $request->user()->columns()->get();

        if ($columns->isEmpty()) {
            $default = $request->user()->columns()->create([
                'name'        => 'Iesūtne',
                'description' => 'Noklusējuma kolonna',
                'color'       => '#764ba2',
                'is_default'  => true,
            ]);
            $columns = collect([$default]);
        }

        return response()->json($columns);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'color'       => 'nullable|string|max:20',
        ]);

        $column = $request->user()->columns()->create($data);
        return response()->json($column, 201);
    }

    public function destroy(Request $request, BoardColumn $column)
    {
        if ($column->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $defaultColumn = $request->user()->columns()
            ->where('is_default', true)
            ->where('id', '!=', $column->id)
            ->first();

        $column->tasks()->update(['column_id' => $defaultColumn?->id]);
        $column->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
