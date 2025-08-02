@extends('backend.app')

@section('title', 'Dashboard')

@push('styles')

@endpush

@section('content')

    {{-- <h1>{{ $malePercent }}</h1>
    <h1>{{ $femalePercent }}</h1> --}}

    <div class="main-content-wrap">
        <div class="tf-section-2 mb-30">
            <div class="flex gap20 flex-wrap-mobile">
                <div class="w-half">

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="52" viewBox="0 0 48 52" fill="none">
                                        <path opacity="0.08" d="M19.1086 2.12943C22.2027 0.343099 26.0146 0.343099 29.1086 2.12943L42.4913 9.85592C45.5853 11.6423 47.4913 14.9435 47.4913 18.5162V33.9692C47.4913 37.5418 45.5853 40.8431 42.4913 42.6294L29.1086 50.3559C26.0146 52.1423 22.2027 52.1423 19.1086 50.3559L5.72596 42.6294C2.63194 40.8431 0.725956 37.5418 0.725956 33.9692V18.5162C0.725956 14.9435 2.63195 11.6423 5.72596 9.85592L19.1086 2.12943Z" fill="url(#paint0_linear_53_110)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_53_110" x1="-43.532" y1="-34.3465" x2="37.6769" y2="43.9447" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#92BCFF"/>
                                            <stop offset="1" stop-color="#2377FC"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <i class="icon-mail"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Today Messages</div>
                                    <h4>{{ \App\Models\Message::whereDate('created_at', today())->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="wrap-chart">
                            <div id="line-chart-2"></div>
                        </div>
                    </div>

                    <div class="wg-chart-default mb-20">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="52" viewBox="0 0 48 52" fill="none">
                                        <path opacity="0.08" d="M19.1086 2.12943C22.2027 0.343099 26.0146 0.343099 29.1086 2.12943L42.4913 9.85592C45.5853 11.6423 47.4913 14.9435 47.4913 18.5162V33.9692C47.4913 37.5418 45.5853 40.8431 42.4913 42.6294L29.1086 50.3559C26.0146 52.1423 22.2027 52.1423 19.1086 50.3559L5.72596 42.6294C2.63194 40.8431 0.725956 37.5418 0.725956 33.9692V18.5162C0.725956 14.9435 2.63195 11.6423 5.72596 9.85592L19.1086 2.12943Z" fill="url(#paint0_linear_53_110)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_53_110" x1="-43.532" y1="-34.3465" x2="37.6769" y2="43.9447" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#92BCFF"/>
                                            <stop offset="1" stop-color="#2377FC"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <i class="icon-mail"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Total User Messages</div>
                                    <h4>{{ \App\Models\Message::count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="wrap-chart">
                            <div id="line-chart-3"></div>
                        </div>
                    </div>
                    <!-- /chart-default -->
                    <!-- chart-default -->
                    <div class="wg-chart-default">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap14">
                                <div class="image">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="52" viewBox="0 0 48 52" fill="none">
                                        <path opacity="0.08" d="M19.1086 2.12943C22.2027 0.343099 26.0146 0.343099 29.1086 2.12943L42.4913 9.85592C45.5853 11.6423 47.4913 14.9435 47.4913 18.5162V33.9692C47.4913 37.5418 45.5853 40.8431 42.4913 42.6294L29.1086 50.3559C26.0146 52.1423 22.2027 52.1423 19.1086 50.3559L5.72596 42.6294C2.63194 40.8431 0.725956 37.5418 0.725956 33.9692V18.5162C0.725956 14.9435 2.63195 11.6423 5.72596 9.85592L19.1086 2.12943Z" fill="url(#paint0_linear_53_110)"/>
                                        <defs>
                                        <linearGradient id="paint0_linear_53_110" x1="-43.532" y1="-34.3465" x2="37.6769" y2="43.9447" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#92BCFF"/>
                                            <stop offset="1" stop-color="#2377FC"/>
                                        </linearGradient>
                                        </defs>
                                    </svg>
                                    <i class="icon-users"></i>
                                </div>
                                <div>
                                    <div class="body-text mb-2">Total Register User</div>
                                    <h4>{{ \App\Models\User::where('role', 'user')->count() }}</h4>
                                </div>
                            </div>

                        </div>
                        <div class="wrap-chart">
                            <div id="line-chart-4"></div>
                        </div>
                    </div>
                    <!-- /chart-default -->
                </div>

                <!-- gender -->
                <div class="wg-box w-half">
                    <div class="flex items-center justify-between">
                        <h5>Gender Chart</h5>
                    </div>

                    <div id="morris-donut-1" class="text-center my-4"></div>

                    <div class="flex gap20">
                        <div class="block-legend style-1 w-full">
                            <div class="dot t1" style="background-color: #4a90e2;"></div>
                            <div class="text-tiny">Male ({{ $maleCount }} users)</div>
                        </div>
                        <div class="block-legend style-1 w-full">
                            <div class="dot t2" style="background-color: #f06292;"></div>
                            <div class="text-tiny">Female ({{ $femaleCount }} users)</div>
                        </div>
                    </div>

                </div>
                <!-- /gender -->
            </div>

            <!-- age -->
            <div class="wg-box w-half">
                <div class="flex items-center justify-between">
                    <h5>Age by Users</h5>
                </div>

                <div id="age-donut-chart" class="text-center my-4" style="height: 280px;"></div>

                <div class="flex flex-wrap gap20 mt-4">
                    @foreach($ageStats as $index => $age)
                        <div class="block-legend style-1 w-full">
                            <div class="dot t{{ $index + 1 }}"
                                style="background-color: {{ ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c'][$index % 5] }};">
                            </div>
                            <div class="text-tiny">
                                {{ $age['range'] }} — {{ $age['percent'] }}% ({{ $age['count'] }} users)
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <!-- age -->

        </div>

        <div class="tf-section-3">
            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Countries by Users</h5>
                </div>

                <div id="country-donut-chart" class="text-center" style="height: 280px;"></div>

                <div class="flex flex-wrap gap20 mt-4">
                    @foreach($countryStats as $index => $country)
                        <div class="block-legend style-1 w-full">
                            <div class="dot t{{ $index + 1 }}" style="background-color: {{ ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c'][$index % 5] }};"></div>
                            <div class="text-tiny">
                                {{ $country['country'] }} — {{ $country['percent'] }}% ({{ $country['count'] }} users)
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

            <!-- category -->
            <div class="wg-box">
                <div class="flex items-center justify-between">
                    <h5>Categories by Users</h5>
                </div>

                <div id="category-donut-chart" class="text-center my-4" style="height: 280px;"></div>

                <div class="flex flex-wrap gap20 mt-4">
                    @foreach($categoryStats as $index => $category)
                        <div class="block-legend style-1 w-full">
                            <div class="dot t{{ $index + 1 }}"
                                style="background-color: {{ ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c', '#9b59b6', '#1abc9c', '#e74c3c'][$index % 8] }};">
                            </div>
                            <div class="text-tiny">
                                {{ $category['category'] }} — {{ $category['percent'] }}% ({{ $category['count'] }} users)
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- category -->

        </div>
    </div>

@endsection

@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="{{ asset('assets/js/jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jvectormap-us-lcc.js') }}"></script>
    <script src="{{ asset('assets/js/jvectormap.js') }}"></script>
    <script src="{{ asset('assets/js/morris.min.js') }}"></script>

    <script src="{{ asset('assets/js/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-1.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-2.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-3.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-4.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-8.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-9.js') }}"></script>
    <script src="{{ asset('assets/js/apexcharts/line-chart-10.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Morris.Donut({
                element: 'morris-donut-1',
                data: [
                    { label: "Male", value: {{ $malePercent }} },
                    { label: "Female", value: {{ $femalePercent }} }
                ],
                colors: ['#4a90e2', '#f06292'],
                formatter: function (y) {
                    return y + "%";
                }
            });
        });
    </script>

    <script>
        new Morris.Donut({
            element: 'country-donut-chart',
            data: [
                @foreach($countryStats as $country)
                    { label: "{{ $country['country'] }}", value: {{ $country['count'] }} },
                @endforeach
            ],
            colors: ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c']
        });
    </script>


    <script>
        new Morris.Donut({
            element: 'age-donut-chart',
            data: [
                @foreach($ageStats as $age)
                    { label: "{{ $age['range'] }}", value: {{ $age['count'] }} },
                @endforeach
            ],
            colors: ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c'],
            formatter: function (y) {
                return y + " Users";
            }
        });
    </script>

    <script>
        new Morris.Donut({
            element: 'category-donut-chart',
            data: [
                @foreach($categoryStats as $category)
                    { label: "{{ $category['category'] }}", value: {{ $category['count'] }} },
                @endforeach
            ],
            colors: ['#4a90e2', '#f06292', '#7ed6df', '#f39c12', '#6ab04c', '#9b59b6', '#1abc9c', '#e74c3c'],
            formatter: function (y) {
                return y + " Users";
            }
        });
    </script>


@endpush
