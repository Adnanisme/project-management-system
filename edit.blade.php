{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Edit User')

@section('content')
<link rel="stylesheet" href="{{ asset('css/edit-subdivision.css') }}">

<div class="edit-container">
    <div class="edit-header">
        <h2>Edit User</h2>
        <div class="header-buttons">
            <button type="button" class="save-continue-btn" onclick="document.getElementById('edit-user-form').submit();">
                <i class="icon-save"></i> Save and continue
            </button>
            <button type="button" class="save-changes-btn" onclick="document.getElementById('edit-user-form').submit();">
                Save Changes
            </button>
        </div>
    </div>

    <form id="edit-user-form" method="POST" action="{{ route('admin.users.update', $user) }}" class="edit-form">
        @csrf
        @method('PATCH')

        <!-- Username -->
        <div class="form-group">
            <label for="username" class="form-label">Username<span class="required">*</span></label>
            <input 
                type="text" 
                name="username" 
                id="username" 
                value="{{ old('username', $user->username) }}" 
                required 
                class="form-input"
            >
            @error('username')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Email<span class="required">*</span></label>
            <input 
                type="email" 
                name="email" 
                id="email" 
                value="{{ old('email', $user->email) }}" 
                required 
                class="form-input"
            >
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Role -->
        <div class="form-group">
            <label for="role" class="form-label">Role<span class="required">*</span></label>
            <select name="role" id="role" class="form-select" required>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
            @error('role')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Department -->
        <div class="form-group">
            <label for="department_id" class="form-label">Department<span class="required">*</span></label>
            <select name="department_id" id="department_id" class="form-select" required>
                @foreach($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
            @error('department_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Subdivision -->
        <div class="form-group">
            <label for="subdivision_id" class="form-label">Subdivision<span class="required">*</span></label>
            <select name="subdivision_id" id="subdivision_id" class="form-select" required>
                @foreach($subdivisions as $subdivision)
                    <option value="{{ $subdivision->id }}" {{ old('subdivision_id', $user->subdivision_id) == $subdivision->id ? 'selected' : '' }}>{{ $subdivision->name }}</option>
                @endforeach
            </select>
            @error('subdivision_id')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Active Status Toggle -->
        <div class="form-group">
            <label for="status" class="form-label">Active</label>
            <input type="hidden" name="status" value="inactive">
            <label class="toggle-switch">
                <input 
                    type="checkbox" 
                    name="status" 
                    id="status" 
                    value="active" 
                    {{ old('status', $user->status) === 'active' ? 'checked' : '' }}
                >
                <span class="slider round"></span>
            </label>
        </div>
    </form>
</div>
@endsection
