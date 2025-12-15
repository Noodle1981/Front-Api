<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mi Pipeline') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="grid grid-cols-1 gap-6">
                    @forelse($deals as $stageName => $stageDeals)
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $stageName }}</h3>
                            <div class="space-y-4">
                                @foreach($stageDeals as $deal)
                                    <div class="border p-4 rounded-lg hover:bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h4 class="font-medium">{{ $deal->title }}</h4>
                                                <p class="text-sm text-gray-500">{{ $deal->client->name }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-semibold">${{ number_format($deal->value, 2) }}</p>
                                                <p class="text-sm text-gray-500">{{ $deal->expected_close_date?->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-500">No hay deals en el pipeline actualmente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>