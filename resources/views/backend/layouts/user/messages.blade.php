@extends('backend.app')

@section('title', 'User Messages')

@push('styles')
    <style>
        table, td, th, tr {
            border: none;
        }
        .image img {
            width: 50px;
            border-radius: 6px;
        }
        .form-check.form-switch{
            display: flex;
            justify-content: center;
        }
        .badge-light {
            background: silver;
            font-size: 9px;
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

    <!-- User Table Box -->
    <div class="wg-box">
        <div class="flex items-center justify-between gap10 flex-wrap">
            <div class="wg-filter flex-grow">
                <form class="form-search">
                    <fieldset class="name">
                        <input type="text" placeholder="Search here..." id="custom-search">
                    </fieldset>
                    <div class="button-submit">
                        <button type="button"><i class="icon-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="flex gap10 items-center">

            </div>

        </div>

        <div class="wg-table table-all-user mt-4">
            <table id="table" class="w-full">
                <thead>
                    <tr>
                        <th class="text-left body-title" style="width: 3%;">Sl.</th>
                        <th class="text-left body-title">User Name</th>
                        <th class="text-center body-title">User Email</th>
                        <th class="text-center body-title" style="width: 40%;">Message</th>
                        <th class="text-center body-title">Time</th>
                        <th class="text-center body-title">Date</th>

                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="divider mt-4"></div>

        <div class="flex items-center justify-between flex-wrap gap10 mt-4">
            <div class="text-tiny" style="display:flex; align-items: center; gap: 5px;">
                <select id="custom-length" style="width: 50px; padding: 10px;text-align:center;">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">All</option>
                </select>
                <span id="custom-info">Showing entries</span>
            </div>
            <div id="custom-pagination" class="wg-pagination">
                <ul class="wg-pagination">
                    <li>
                        <a><i class="icon-chevron-left"></i></a>
                    </li>
                    <li>
                        <a >1</a>
                    </li>
                    <li class="active">
                        <a>2</a>
                    </li>
                    <li>
                        <a>3</a>
                    </li>
                    <li>
                        <a><i class="icon-chevron-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>
    <script>
        let table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('user.message') }}",

            },
            pageLength: 10,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            lengthChange: true,
            searching: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-left', orderable: false, searchable: false },
                { data: 'name', name: 'name', orderable: false, searchable: true },
                { data: 'email', name: 'email', className: 'text-center', searchable: true, orderable: true },
                { data: 'message', name: 'message', className: 'text-center', searchable: true, orderable: true },
                { data: 'time', name: 'time', className: 'text-center', searchable: true, orderable: true },
                { data: 'date', name: 'date', className: 'text-center', searchable: true, orderable: true },

            ],
            drawCallback: function (settings) {
                let info = table.page.info();
                $('#custom-info').text(`Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries`);

                let pagination = '';
                pagination += `<li><a class="prev ${info.page === 0 ? 'disabled' : ''}"><i class="icon-chevron-left"></i></a></li>`;
                for (let i = 0; i < info.pages; i++) {
                    let activeClass = (i === info.page) ? 'active' : '';
                    pagination += `<li><a class="${activeClass}" data-page="${i}">${i + 1}</a></li>`;
                }
                pagination += `<li><a class="next ${info.page === info.pages - 1 ? 'disabled' : ''}"><i class="icon-chevron-right"></i></a></li>`;
                $('#custom-pagination').html(`<ul class="wg-pagination">${pagination}</ul>`);
            }
        });

        // Handle custom length selection
        $('#custom-length').on('change', function () {
            let length = this.value === 'all' ? -1 : this.value;
            table.page.len(length).draw();
        });

        // Custom search field for name or email
        $('#custom-search').on('keyup', function () {
            table.search(this.value).draw();
        });

        // Handle custom pagination click
        $(document).on('click', '#custom-pagination a', function (e) {
            e.preventDefault();
            let pageNum = $(this).data('page');

            if ($(this).hasClass('prev')) {
                table.page('previous').draw('page');
            } else if ($(this).hasClass('next')) {
                table.page('next').draw('page');
            } else {
                table.page(pageNum).draw('page');
            }
        });

        // Trigger DataTable draw when filters change
        $('#filter-gender, #filter-country').on('change', function () {
            table.draw();
        });
    </script>


    <script>
        $(document).on('change', '.status-switch', function() {
            var status = $(this).prop('checked') ? 'active' : 'inactive';
            var userId = $(this).data('id');

            $.ajax({
                url: '{{ route("admin.status") }}',
                method: 'POST',
                data: {
                    user_id: userId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    ajaxMessage(response.message, response.type);

                   if( response.logout ){
                        setTimeout(function() {
                            window.location.href = '{{ route("login") }}';
                        }, 3000);
                   }
                },
                error: function(error) {
                    alert('Error updating status.');
                }
            });
        });
    </script>
@endpush

