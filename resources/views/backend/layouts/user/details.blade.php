@extends('backend.app')

@section('title', $data->name . ' Details')

@push('styles')
<style>
    .user-detail-box {
        background: #fff;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .user-detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 10px;
    }
    .label {
        font-weight: bold;
        color: #555;
        width: 30%;
        font-size: 13px;
    }
    .value {
        color: #333;
        width: 65%;
        text-align: right;
        font-size: 13px;
    }
    .user-img {
        width: 120px;
        display: block;
        margin: auto;
        margin-bottom: 20px;
        border-radius: 50%;
        border: 1px solid #eee;
    }
</style>
@endpush

@section('content')
<div class="main-content-wrap">
    <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>@yield('title')</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
            <li>
                <a href="{{ route('dashboard') }}"><div class="text-tiny">Dashboard</div></a>
            </li>
            <li><i class="icon-chevron-right"></i></li>
            <li><div class="text-tiny">@yield('title')</div></li>
        </ul>
    </div>

    <div class="wg-box user-detail-box">

        <img src="{{ asset($data->avatar ?? 'user.png') }}" class="user-img">

        <div class="user-detail-row">
            <div class="label">Full Name:</div>
            <div class="value">{{ $data->name }} {{ $data->last_name }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Email:</div>
            <div class="value">
                <a href="mailto:{{ $data->email }}">{{ $data->email }}</a>
            </div>
        </div>

        <div class="user-detail-row">
            <div class="label">Date of Birth:</div>
            <div class="value">{{ $data->date_of_birth ?? '-' }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Gender:</div>
            <div class="value">{{ ucfirst($data->gender ?? '-') }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">City:</div>
            <div class="value">{{ $data->city ?? '-' }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Country:</div>
            <div class="value">{{ $data->country ?? '-' }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Age:</div>
            <div class="value">{{ $data->age ?? '-' }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Coins:</div>
            <div class="value text-success">{{ $data->coins }}</div>
        </div>

        <div class="user-detail-row">
            <div class="label">Selected Categories:</div>
            <div class="value text-right">
                @php
                    $categories = is_array($data->categories) ? $data->categories : json_decode($data->categories, true);
                @endphp


                @if(is_array($categories))
                    @foreach($categories as $category)
                        <span class="badge bg-primary">
                            {{ ucfirst($category) }}
                        </span>
                    @endforeach
                @else
                    <span class="text-gray-400">No category selected</span>
                @endif
            </div>
        </div>


        <div class="user-detail-row">
            <div class="label">Status:</div>
            <div class="value">
                <span class="badge {{ $data->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                    {{ ucfirst($data->status) }}
                </span>
            </div>
        </div>

        <div class="user-detail-row">
            <div class="label">Email Verified:</div>
            <div class="value">
                <span class="badge {{ $data->email_verified_at ? 'bg-success' : 'bg-danger' }}">
                    {{ $data->email_verified_at ? 'verified' : 'unverified' }}
                </span>
            </div>
        </div>


    </div>
</div>
@endsection

@push('scripts')
<!-- Add any JS if needed -->
@endpush
