@extends('backend.app')

@section('title', $programme->title . ' - Volunteer List')

@push('styles')
    <style>
        .is-featured-checkbox {
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>@yield('title')</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('dashboard') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">@yield('title')</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="wg-table table-all-user mt-4">
                <table id="table" class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left body-title">Sl.</th>
                            <th class="text-center body-title">Volunteer</th>
                            <th class="text-center body-title">Paid Coins</th>
                            <th class="text-right body-title">Register Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $index => $v)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-center body-title">{{ $v->user->name . ' ' . $v->user->last_name}}</td>
                                <td class="text-center body-title">
                                    <input type="checkbox" class="is-featured-checkbox" data-id="{{ $v->id }}" {{ $v->paid ? 'checked' : '' }}>
                                </td>
                                <td class="text-right body-title">{{ $v->created_at->format('d M, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
@endsection

@push('scripts')

    <script>
        $(document).on('change', '.is-featured-checkbox', function () {
            let id = $(this).data('id');
            let paid = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '{{ route("coin.paid") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    paid: paid
                },
                success: function (response) {
                    ajaxMessage(response.message, 'success');
                },
                error: function () {
                    alert('Something went wrong');
                }
            });
        });
    </script>

@endpush
