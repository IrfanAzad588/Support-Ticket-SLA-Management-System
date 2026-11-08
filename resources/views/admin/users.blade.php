<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">User & Agent Management</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if($errors->any())<div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded">{{ $errors->first() }}</div>@endif
            @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded">{{ session('success') }}</div>@endif
            <section class="bg-white border rounded-lg p-5">
                <h3 class="font-semibold mb-4">Create user</h3>
                <form method="POST" action="{{ route('admin.users.store') }}" class="grid md:grid-cols-4 gap-3">@csrf
                    <input name="name" required placeholder="Name" class="rounded border-gray-300"><input name="email" type="email" required placeholder="Email" class="rounded border-gray-300"><select name="role" class="rounded border-gray-300">
                        <option value="customer">Customer</option>
                        <option value="agent">Agent</option>
                        <option value="admin">Admin</option>
                    </select><input name="password" type="password" required minlength="8" placeholder="Password (min. 8)" class="rounded border-gray-300"><button class="md:col-span-4 w-fit px-4 py-2 bg-blue-600 rounded text-sm" style="color: green">Create user</button>
                </form>
            </section>
            <section class="bg-white border rounded-lg p-5">
                <h3 class="font-semibold mb-4">Users and workload</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="p-2">User</th>
                                <th class="p-2">Role</th>
                                <th class="p-2">Active workload</th>
                                <th class="p-2">Account</th>
                                <th class="p-2">Save</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)<tr class="border-b">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}">@csrf @method('PATCH')<td class="p-2"><input name="name" value="{{ $user->name }}" required class="rounded border-gray-300 w-full"><input name="email" value="{{ $user->email }}" required type="email" class="rounded border-gray-300 w-full mt-1"></td>
                                    <td class="p-2"><select name="role" class="rounded border-gray-300">
                                            <option value="admin" @selected($user->role === 'admin')>Admin</option>
                                            <option value="agent" @selected($user->role === 'agent')>Agent</option>
                                            <option value="customer" @selected($user->role === 'customer')>Customer</option>
                                        </select></td>
                                    <td class="p-2">{{ $user->role === 'agent' ? $user->active_workload : '—' }}</td>
                                    <td class="p-2"><select name="is_active" class="rounded border-gray-300">
                                            <option value="1" @selected($user->is_active)>Active</option>
                                            <option value="0" @selected(!$user->is_active)>Inactive</option>
                                        </select></td>
                                    <td class="p-2"><button class="px-3 py-2 rounded bg-gray-800 text-white">Save</button></td>
                                </form>
                            </tr>@endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $users->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>