{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Users')

@section('content')
<link rel="stylesheet" href="{{ asset('css/divisions.css') }}">
<link rel="stylesheet" href="{{ asset('css/edit-subdivision.css') }}">

<style>
.toggle-switch {
    width: 36px !important;
    height: 18px !important;
}
.toggle-switch .slider {
    height: 18px !important;
}
.toggle-switch .slider.round {
    border-radius: 18px !important;
}
.toggle-switch .slider:before {
    height: 14px !important;
    width: 14px !important;
    left: 2px !important;
    bottom: 2px !important;
}
.toggle-switch input:checked + .slider:before {
    transform: translateX(18px) !important;
}
</style>

<div class="division-header-row">
    <h2 class="division-title">Users</h2>
</div>

<div class="division-table-wrapper">
    <table class="division-table">
        <thead>
            <tr>
                <th class="checkbox-col"><input type="checkbox" id="select-all"></th>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Department</th>
                <th>Subdivision</th>
                <th class="actions-col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td class="checkbox-col"><input type="checkbox" class="row-checkbox"></td>
                <td>{{ $user->id }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <select class="form-control form-control-sm role-select" data-user-id="{{ $user->id }}">
                        <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="executive_secretary" {{ $user->role === 'executive_secretary' ? 'selected' : '' }}>Executive Secretary</option>
                        <option value="general_manager" {{ $user->role === 'general_manager' ? 'selected' : '' }}>General Manager</option>
                        <option value="department_head" {{ $user->role === 'department_head' ? 'selected' : '' }}>Department Head</option>
                        <option value="subdivision_head" {{ $user->role === 'subdivision_head' ? 'selected' : '' }}>Subdivision Head</option>
                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="dashboard_viewer" {{ $user->role === 'dashboard_viewer' ? 'selected' : '' }}>Dashboard Viewer</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="status-badge {{ $user->status === 'active' ? 'bg-success' : 'bg-secondary' }} me-2">
                            {{ ucfirst($user->status) }}
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" class="status-toggle" data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </td>
                <td>{{ $user->department->name ?? 'N/A' }}</td>
                <td>{{ $user->subdivision->name ?? 'N/A' }}</td>
                <td class="actions-col">
                    <div class="action-buttons">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn-edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn-delete" onclick="confirmDelete(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@include('admin.partials.pagination', ['paginator' => $users])

<style>
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    color: white;
    font-size: 0.75rem;
    font-weight: 500;
}

.bg-success { background-color: #10b981; }
.bg-secondary { background-color: #6b7280; }
.bg-warning { background-color: #f59e0b; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox logic
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    }
    
    // Handle role changes
    document.querySelectorAll('.role-select').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const newRole = this.value;
            
            fetch(`{{ route('admin.users.role.update', ['user' => '__USER_ID__']) }}`.replace('__USER_ID__', userId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ role: newRole })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', 'Role updated successfully');
                } else {
                    showToast('error', data.message || 'Failed to update role');
                    // Revert the select value on error
                    this.value = this.dataset.previousValue || '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'An error occurred while updating the role');
                this.value = this.dataset.previousValue || '';
            });
            
            // Store the current value for potential revert
            this.dataset.previousValue = newRole;
        });
    });
    
    // Handle status toggles
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const userId = this.dataset.userId;
            const currentStatus = this.closest('td').querySelector('.status-badge').textContent.trim().toLowerCase();
            const newStatus = this.checked ? 'active' : 
                             (currentStatus === 'pending' ? 'pending' : 'inactive');
            
            fetch(`{{ route('admin.users.status.update', ['user' => '__USER_ID__']) }}`.replace('__USER_ID__', userId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the status badge
                    const badge = this.closest('td').querySelector('.status-badge');
                    if (badge) {
                        badge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        let badgeClass = 'status-badge me-2 ';
                        
                        if (newStatus === 'active') {
                            badgeClass += 'bg-success';
                        } else if (newStatus === 'pending') {
                            badgeClass += 'bg-warning';
                        } else {
                            badgeClass += 'bg-secondary';
                        }
                        
                        badge.className = badgeClass;
                    }
                    showToast('success', 'Status updated successfully');
                    
                    // If status was changed from pending to active, reload the page after 1.5 seconds
                    if (currentStatus === 'pending' && newStatus === 'active') {
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    // Revert the toggle on error
                    this.checked = !this.checked;
                    showToast('error', data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked;
                showToast('error', 'An error occurred while updating the status');
            });
        });
    });
    
    // Show toast notification
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast bg-${type} text-white`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        const toastContainer = document.querySelector('.toast-container');
        if (toastContainer) {
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
    }
    
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const all = Array.from(checkboxes).every(cb => cb.checked);
            const some = Array.from(checkboxes).some(cb => cb.checked);
            if (selectAll) {
                selectAll.checked = all;
                selectAll.indeterminate = !all && some;
            }
        });
    });
});

function confirmDelete(button) {
    if (confirm('Are you sure you want to delete this user?')) {
        button.closest('form').submit();
    }
}
</script>
@endsection
