<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use App\Models\Category;
use App\Models\Holiday;
use App\Models\Priority;
use App\Models\SlaRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function users()
    {
        $users = User::query()
            
            ->withCount(['assignedTickets as active_workload' => fn($query) => $query->active()])
            ->orderBy('role')->orderBy('name')
            // ->where('role', '!=', 'admin') 
            ->paginate(25);

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'agent', 'customer'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($data);

        return back()->with('success', 'User created.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(['admin', 'agent', 'customer'])],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if ($user->is(auth()->user()) && !$data['is_active']) {
            return back()->withErrors(['user' => 'You cannot deactivate your own account.']);
        }

        if (blank($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'User updated.');
    }

    public function settings()
    {
        $categories = Category::orderBy('name')->get();
        $priorities = Priority::with('slaRule')->orderBy('level')->get();
        $businessHours = BusinessHour::orderBy('weekday')->get()->keyBy('weekday');
        $holidays = Holiday::orderBy('holiday_date')->get();

        return view('admin.settings', compact('categories', 'priorities', 'businessHours', 'holidays'));
    }

    public function storeCategory(Request $request)
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'Category updated.');
    }

    public function destroyCategory(Category $category)
    {
        abort_if($category->tickets()->exists(), 422, 'Categories in use cannot be deleted. Deactivate them instead.');
        $category->delete();

        return back()->with('success', 'Category deleted.');
    }

    public function storePriority(Request $request)
    {
        Priority::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:priorities,name'],
            'level' => ['required', 'integer', 'min:1', 'max:255', 'unique:priorities,level'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Priority created.');
    }

    public function updatePriority(Request $request, Priority $priority)
    {
        $priority->update($request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('priorities', 'name')->ignore($priority)],
            'level' => ['required', 'integer', 'min:1', 'max:255', Rule::unique('priorities', 'level')->ignore($priority)],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'Priority updated.');
    }

    public function destroyPriority(Priority $priority)
    {
        abort_if($priority->tickets()->exists(), 422, 'Priorities in use cannot be deleted. Deactivate them instead.');
        $priority->delete();

        return back()->with('success', 'Priority deleted.');
    }

    public function saveSlaRule(Request $request, Priority $priority)
    {
        SlaRule::updateOrCreate(['priority_id' => $priority->id], $request->validate([
            'resolution_minutes' => ['required', 'integer', 'min:1'],
            'warning_percent' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]));

        return back()->with('success', 'SLA rule saved.');
    }

    public function saveBusinessHours(Request $request)
    {
        $data = $request->validate([
            'hours' => ['required', 'array'],
            'hours.*.is_working_day' => ['nullable', 'boolean'],
            'hours.*.start_time' => ['nullable', 'date_format:H:i'],
            'hours.*.end_time' => ['nullable', 'date_format:H:i', 'after:hours.*.start_time'],
        ]);

        foreach (range(0, 6) as $weekday) {
            $hour = $data['hours'][$weekday] ?? [];
            BusinessHour::updateOrCreate(['weekday' => $weekday], [
                'is_working_day' => (bool) ($hour['is_working_day'] ?? false),
                'start_time' => $hour['start_time'] ?? null,
                'end_time' => $hour['end_time'] ?? null,
            ]);
        }

        return back()->with('success', 'Business hours saved.');
    }

    public function storeHoliday(Request $request)
    {
        Holiday::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date', 'unique:holidays,holiday_date'],
        ]));

        return back()->with('success', 'Holiday added.');
    }

    public function destroyHoliday(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Holiday deleted.');
    }
}
