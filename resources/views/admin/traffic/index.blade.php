<x-app-layout head="Traffic" title="Admin - Traffic">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-6">
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20">
            <div class="space-y-4 sm:space-y-6">
                <div class=" w-full flex sm:items-center flex-col sm:flex-row justify-between gap-4">
                    <div class=" font-semibold px-4 py-1.5 rounded-md bg-byolink-1 text-white capitalize">Access This {{$mode}} : {{$totalaccess}}</div>
                    <div class=" w-auto grid grid-cols-3 gap-3 text-sm">
                        <a href="{{ route('traffic.index', ['mode' => 'day']) }}"
                            class="{{ $mode == 'day' ? 'bg-byolink-1 text-white' : ' text-black hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center px-2 py-1.5 font-semibold rounded-md duration-300">
                            Day
                        </a>
                        <a href="{{ route('traffic.index', ['mode' => 'week']) }}"
                            class="{{ $mode == 'week' ? 'bg-byolink-1 text-white' : ' text-black hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center px-2 py-1.5 font-semibold rounded-md duration-300">
                            Week
                        </a>
                        <a href="{{ route('traffic.index', ['mode' => 'month']) }}"
                            class="{{ $mode == 'month' ? 'bg-byolink-1 text-white' : ' text-black hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center px-2 py-1.5 font-semibold rounded-md duration-300">
                            Month
                        </a>
                    </div>
                </div>

                <div class="w-full text-sm sm:text-base">
                    <div x-data="lineChart()" x-init="init()" class="w-full bg-white rounded-xl">
                        <div id="chart"></div>
                    </div>

                    <script>
                        function lineChart() {
                            return {
                                chart: null,

                                init() {
                                    const options = {
                                        chart: {
                                            type: "line",
                                            height: 300,
                                            toolbar: {
                                                show: false
                                            }
                                        },

                                        series: [{
                                            name: "Article Access",
                                            data: @json($traffic['values']) // ← nilai traffic
                                        }],

                                        xaxis: {
                                            categories: @json($traffic['labels']), // ← label jam/hari/tanggal
                                            labels: {
                                                style: {
                                                    rotate: 0,
                                                    rotateAlways: true,
                                                    fontSize: "13px"
                                                }
                                            },
                                            tickAmount: 4,
                                        },

                                        yaxis: {
                                            labels: {
                                                style: {
                                                    fontSize: "13px"
                                                }
                                            }
                                        },

                                        responsive: [{
                                            breakpoint: 640,
                                            options: {
                                                xaxis: {
                                                    tickAmount: 3
                                                }
                                            }
                                        }],

                                        stroke: {
                                            width: 2,
                                            curve: "straight"
                                        },

                                        colors: ["#3b82f6"],
                                        grid: {
                                            borderColor: "#E5E7EB"
                                        },

                                        plugins: {
                                            zoom: {
                                                zoom: {
                                                    wheel: {
                                                        enabled: false
                                                    },
                                                    pinch: {
                                                        enabled: false
                                                    },
                                                    mode: 'xy'
                                                },
                                                pan: {
                                                    enabled: false
                                                }
                                            }
                                        }
                                    };

                                    if (this.chart) {
                                        this.chart.destroy();
                                    }

                                    this.chart = new ApexCharts(document.querySelector("#chart"), options);
                                    this.chart.render();
                                }
                            }
                        }
                    </script>

                </div>
            </div>
        </div>
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20">
            <div x-data="{ tab: 'guardian' }" class="space-y-8">
                <div class=" w-full grid grid-cols-3 gap-2 sm:gap-4">
                    <button @click="tab = 'guardian'" :class="tab === 'guardian' ? 'bg-byolink-1 text-white' : 'text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1'"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                        Guardian
                    </button>
                    <button @click="tab = 'category'" :class="tab === 'category' ? 'bg-byolink-1 text-white' : 'text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1'"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                        Category
                    </button>
                    <button @click="tab = 'articles'" :class="tab === 'articles' ? 'bg-byolink-1 text-white' : 'text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1'"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                        Article
                    </button>
                </div>
                <div x-show="tab === 'guardian'" class=" space-y-4">
                    <table class=" w-full">
                        <tr class=" border-b">
                            <th class=" pb-2 text-left">Guardian</th>
                            <th class=" pb-2 text-right">Access</th>
                        </tr>
                        @foreach ($guardians as $item)
                            <tr class="text-neutral-600 border-b">
                                <td class=" font-semibold line-clamp-2">{{$item->url}}</td>
                                <td class=" text-right">{{$item->access}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div x-show="tab === 'category'" class=" space-y-4">
                    <table class=" w-full">
                        <tr class=" border-b">
                            <th class=" pb-2 text-left">Category</th>
                            <th class=" pb-2 text-right">Access</th>
                        </tr>
                        @foreach ($categories as $item)
                            <tr class="text-neutral-600 border-b">
                                <td class=" font-semibold line-clamp-2">{{$item->category}}</td>
                                <td class=" text-right">{{$item->access}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <div x-show="tab === 'articles'" class=" space-y-4">
                    <table class=" w-full">
                        <tr class=" border-b">
                            <th class=" pb-2 text-left">Title</th>
                            <th class=" pb-2 text-right">Access</th>
                        </tr>
                        @foreach ($articles as $item)
                            <tr class="text-neutral-600 border-b">
                                <td class=" font-semibold line-clamp-2">{{$item->judul}}</td>
                                <td class=" text-right">{{$item->access}}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
