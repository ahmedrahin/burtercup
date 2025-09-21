@extends('backend.app')

@section('title',  $programme->title .' - Donation List')

@push('styles')

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

        <div class="wg-box">
            <div class="wg-table table-all-user mt-4">
                <table id="table" class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left body-title">Sl.</th>
                            <th class="text-center body-title">Donor</th>
                            <th class="text-center body-title">Amount</th>
                            <th class="text-right body-title">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($donations as $index => $donation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-center body-title">{{ $donation->user->name }}</td>
                                <td class="text-center body-title">{{ $donation->amount }} Coins</td>
                                <td class="text-right body-title">{{ $donation->created_at->format('d M, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>

    </div>
@endsection

@push('scripts')

@endpush

