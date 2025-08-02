@extends('backend.app')

@section('title', 'All User List')

@push('styles')
<style>
    table, td, th, tr { border: none; }
    .image img { width: 50px; border-radius: 6px; }
    .form-check.form-switch { display: flex; justify-content: center; }
    .badge-light { background: silver; font-size: 9px; }
    select { width: 140px; }
    .filter { font-weight: 600; font-size: 14px; color: black; }
    .btn-danger { font-size: 11px; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="main-content-wrap">
    <div class="flex items-center flex-wrap justify-between gap20 mb-27">
        <h3>@yield('title')</h3>
        <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
            <li><a href="{{ route('dashboard') }}"><div class="text-tiny">Dashboard</div></a></li>
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
                <div class="filter">Filter : </div>

                <select id="filter-gender" style="padding: 8px;">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>

                <select id="filter-country" style="padding: 8px;">
                    <option value="">All Countries</option>
                    @foreach(\App\Models\User::whereNotNull('country')->select('country')->distinct()->orderBy('country')->pluck('country') as $country)
                        @if($country)
                            <option value="{{ $country }}">{{ ucfirst($country) }}</option>
                        @endif
                    @endforeach
                </select>

                @php
                    $allCategories = \App\Models\User::whereNotNull('categories')
                        ->pluck('categories')
                        ->map(function ($item) {
                            if (is_string($item)) {
                                $decoded = json_decode($item, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    return $decoded;
                                }

                                return [$item];
                            } elseif (is_array($item)) {
                                return $item;
                            } else {
                                return [];
                            }
                        })
                        ->flatten()
                        ->filter()
                        ->unique()
                        ->sort()
                        ->values();
                @endphp

                <select id="filter-category" style="padding: 8px;">
                    <option value="">All Categories</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                    @endforeach
                </select>

                @php
                    $allAges = \App\Models\User::whereNotNull('age')
                        ->select('age')
                        ->distinct()
                        ->orderBy('age')
                        ->pluck('age');
                @endphp

                <select id="filter-age" style="padding: 8px;">
                    <option value="">All Ages</option>
                    @foreach($allAges as $age)
                        <option value="{{ $age }}">{{ $age }}</option>
                    @endforeach
                </select>

                <div>
                    <button class="btn btn-danger" id="export-pdf-btn">Export PDF</button>
                </div>
            </div>
        </div>

        <div class="wg-table table-all-user mt-4">
            <table id="table" class="w-full">
                <thead>
                    <tr>
                        <th class="text-left body-title" style="width: 1%;">Sl.</th>
                        <th class="text-left body-title" style="width: 17%;">User</th>

                        <th class="text-center body-title">Coins</th>
                        <th class="text-center body-title">Gender</th>
                        <th class="text-center body-title">Age</th>
                        <th class="text-center body-title">Country</th>
                        <th class="text-center body-title">Register Date</th>
                        <th class="text-center body-title">Status</th>
                        <th style="text-align: right;" class="body-title">Action</th>
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
            <div id="custom-pagination" class="wg-pagination"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <script src="{{ asset('assets/js/datatables.min.js') }}"></script>

    <script>
        let table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('user.index') }}",
                data: function (d) {
                    d.gender = $('#filter-gender').val();
                    d.country = $('#filter-country').val();
                    d.category = $('#filter-category').val();
                    d.age = $('#filter-age').val();
                }
            },
            pageLength: 10,
            lengthMenu: [[10, 50, 100, -1], [10, 50, 100, "All"]],
            lengthChange: true,
            searching: true,
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'avatar', name: 'name', orderable: false, searchable: true },
                { data: 'coins', name: 'coins', className: 'text-center', orderable: false, searchable: true },
                { data: 'gender', name: 'gender', className: 'text-center', orderable: false, searchable: true },
                { data: 'age', name: 'age', className: 'text-center', orderable: false, searchable: true },
                { data: 'country', name: 'country', className: 'text-center', orderable: false, searchable: true },
                { data: 'created_at', name: 'created_at', className: 'text-center', orderable: false, searchable: true },
                { data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center' },
                { data: 'details', name: 'details', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function (settings) {
                let info = table.page.info();
                $('#custom-info').text(`Showing ${info.start + 1} to ${info.end} of ${info.recordsTotal} entries`);

                let pagination = '';
                pagination += `<li><a href="#" class="prev ${info.page === 0 ? 'disabled' : ''}"><i class="icon-chevron-left"></i></a></li>`;
                for (let i = 0; i < info.pages; i++) {
                    let activeClass = (i === info.page) ? 'active' : '';
                    pagination += `<li><a href="#" class="${activeClass}" data-page="${i}">${i + 1}</a></li>`;
                }
                pagination += `<li><a href="#" class="next ${info.page === info.pages - 1 ? 'disabled' : ''}"><i class="icon-chevron-right"></i></a></li>`;
                $('#custom-pagination').html(`<ul class="wg-pagination">${pagination}</ul>`);
            }
        });

        $('#custom-length').on('change', function () {
            let length = this.value === 'all' ? -1 : this.value;
            table.page.len(length).draw();
        });

        $('#custom-search').on('keyup', function () {
            table.search(this.value).draw();
        });

        $(document).on('click', '#custom-pagination a', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;

            if ($(this).hasClass('prev')) {
                table.page('previous').draw('page');
            } else if ($(this).hasClass('next')) {
                table.page('next').draw('page');
            } else {
                let pageNum = $(this).data('page');
                table.page(pageNum).draw('page');
            }
        });

        $('#filter-gender, #filter-country, #filter-category, #filter-age').on('change', function () {
            table.draw();
        });
    </script>


    <script>
        $('#export-pdf-btn').on('click', function () {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            let tableData = [];
            $('#table tbody tr').each(function () {
                let tds = $(this).find('td');

                let row = [
                    $(tds[0]).text().trim(), // Sl.
                    $(tds[1]).text().trim(), // User
                    $(tds[2]).text().trim(), // Email
                    $(tds[4]).text().trim(), // Coins
                    $(tds[5]).text().trim(), // Gender
                    $(tds[6]).text().trim(), // Age
                    $(tds[7]).text().trim()  // Country
                ];

                // Replace "No login yet" with "Not Logged In"
                row = row.map(text => text === "No login yet" ? "Not Logged In" : text);
                tableData.push(row);
            });

            doc.autoTable({
                head: [['Sl.', 'User', 'Email', 'Coins', 'Gender', 'Age', 'Country']],
                body: tableData,
                margin: { top: 20 },
                styles: {
                    fontSize: 10,
                    cellPadding: 2,
                    halign: 'center',
                    valign: 'middle',
                    lineColor: [44, 62, 80],
                    lineWidth: 0.1,
                    overflow: 'linebreak',
                    minCellHeight: 10
                },
                headStyles: {
                    fillColor: [44, 62, 80],
                    textColor: [255, 255, 255],
                    fontSize: 12,
                    halign: 'center',
                    valign: 'middle'
                },
                columnStyles: {
                    0: { cellWidth: 15 },  // Sl.
                    1: { cellWidth: 40 },  // User
                    2: { cellWidth: 40 },  // Email
                    3: { cellWidth: 20 },  // Coins
                    4: { cellWidth: 20 },  // Gender
                    5: { cellWidth: 20 },  // Age
                    6: { cellWidth: 30 }   // Country
                },
                theme: 'striped'
            });

            doc.save('users-list.pdf');
        });
    </script>

    <script>
        $(document).on('change', '.status-switch', function() {
            var status = $(this).prop('checked') ? 'active' : 'inactive';
            var userId = $(this).data('id');
            $.ajax({
                url: '{{ route("user.status") }}',
                method: 'POST',
                data: {
                    user_id: userId,
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    ajaxMessage(response.message, response.type);
                },
                error: function(error) {
                    alert('Error updating status.');
                }
            });
        });
    </script>
@endpush

