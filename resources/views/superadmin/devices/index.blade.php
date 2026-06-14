@extends('layouts.app')
@section('content')
    <div class="py-5">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold">All Devices</h1>
                <a href="{{ route($routePrefix . '.devices.create') }}"
                    class="px-4 py-2 font-semibold text-white rounded bg-slate-500 hover:bg-slate-600">
                    Add New Device
                </a>
            </div>

            @if (session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500" role="alert">
                    <p class="font-bold">Success!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="overflow-x-auto">
                <div id="notification" class="hidden p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500"
                    role="alert">
                    <p id="notificationMessage" class="font-bold"></p>
                </div>

                <div x-data="{ isOpen: false, qrCode: '', loading: false }">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left">#</th>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Phone</th>
                                <th class="px-6 py-3 text-left">Quota</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($devices as $index => $device)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3">{{ $device['name'] }}</td>
                                    <td class="px-6 py-3">{{ $device['device'] }}</td>
                                    <td class="px-6 py-3">{{ $device['quota'] }}</td>
                                    <td class="px-6 py-3">
                                        <span class="px-4 py-2 font-semibold text-white bg-green-500 rounded">
                                            Connected
                                        </span>
                                        <span class="px-4 py-2 font-semibold text-white bg-red-500 rounded">
                                            Disconnect
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 space-x-2">
                                        <button
                                            class="px-4 py-2 font-semibold text-white bg-blue-500 rounded hover:bg-blue-600"
                                            onclick="copyToClipboard('{{ $device['token'] }}')">
                                            Copy Token
                                        </button>
                                        <button class="px-4 py-2 text-white rounded bg-slate-500"
                                            onclick="openSendMessageModal('{{ $device['token'] }}')">
                                            Send Message
                                        </button>
                                        <button class="px-4 py-2 text-white bg-red-500 rounded disconnectButton"
                                            data-device-token="{{ $device['token'] }}"
                                            onclick="disconnectDevice('{{ $device['token'] }}')">
                                            Disconnect
                                            <svg class="hidden w-5 h-5 ml-2 text-white disconnectSpinner animate-spin"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </button>
                                        <!-- Tombol Connect dengan Alpine.js -->
                                        <button @click="activateDevice('{{ $device['token'] }}', $el)"
                                            class="px-4 py-2 text-white bg-green-500 rounded">
                                            Connect
                                        </button>
                                        <button class="px-4 py-2 text-white bg-orange-500 rounded"
                                            onclick="confirmDelete('{{ $device['token'] }}', '{{ $device['name'] }}')">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- @include('devices.partials.modal-qr-code') --}}
                </div>
            </div>
        </div>
    </div>
@endsection
