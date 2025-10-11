<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    /**
     * Display a listing of positions.
     */
    public function index()
    {
        $positions = Position::withCount('employees')->orderBy('PositionName')->paginate(10);
        return view('Admin.positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        return view('Admin.positions.create');
    }

    /**
     * Store a newly created position.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PositionName' => 'required|string|max:255|unique:positions,PositionName',
            'Salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Position::create($request->all());

        return redirect()->route('positions.index')
            ->with('success', 'Position created successfully.');
    }

    /**
     * Display the specified position.
     */
    public function show(Position $position)
    {
        $position->load(['employees.employeeType']);
        return view('Admin.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position)
    {
        return view('Admin.positions.edit', compact('position'));
    }

    /**
     * Update the specified position.
     */
    public function update(Request $request, Position $position)
    {
        $validator = Validator::make($request->all(), [
            'PositionName' => 'required|string|max:255|unique:positions,PositionName,' . $position->PositionID . ',PositionID',
            'Salary' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $position->update($request->all());

        return redirect()->route('positions.index')
            ->with('success', 'Position updated successfully.');
    }

    /**
     * Remove the specified position.
     */
    public function destroy(Position $position)
    {
        // Check if position has employees
        if ($position->employees()->count() > 0) {
            return redirect()->route('positions.index')
                ->with('error', 'Cannot delete position. It has assigned employees.');
        }

        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}
