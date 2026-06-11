<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Products')] class extends Component {
    public string $search     = '';
    public string $categoryId = '';
    public array  $items      = [];
    public int    $loaded     = 0;
    public int    $perPage    = 6;

    public function mount(): void
    {
        $this->fetchMore();
    }

    #[Computed]
    public function totalCount(): int
    {
        return Product::count();
    }

    #[Computed]
    public function filteredCount(): int
    {
        return $this->baseQuery()->count();
    }

    #[Computed]
    public function hasMore(): bool
    {
        return $this->loaded < $this->filteredCount;
    }

    #[Computed]
    public function categories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    public function loadMore(): void
    {
        $this->fetchMore();
    }

    public function updatedSearch(): void
    {
        $this->resetList();
    }

    public function updatedCategoryId(): void
    {
        $this->resetList();
    }

    public function selectProduct(int $id): void
    {
        $this->dispatch('product-selected', productId: $id);
    }

    private function baseQuery()
    {
        return Product::with('category')
            ->when($this->search,     fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->categoryId, fn ($q) => $q->where('category_id', $this->categoryId));
    }

    private function fetchMore(): void
    {
        $rows = $this->baseQuery()->skip($this->loaded)->take($this->perPage)->get();
        $this->items   = array_merge($this->items, $rows->toArray());
        $this->loaded += $rows->count();
    }

    private function resetList(): void
    {
        $this->items  = [];
        $this->loaded = 0;
        $this->fetchMore();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <flux:heading size="xl">Products</flux:heading>
            <flux:badge color="zinc">
                Showing {{ $this->filteredCount }} of {{ $this->totalCount }}
            </flux:badge>
        </div>

        {{-- Filters (reactive state) --}}
        <div class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search products…"
                    icon="magnifying-glass"
                />
            </div>
            <div class="w-full sm:w-48">
                <flux:select wire:model.live="categoryId">
                    <flux:select.option value="">All categories</flux:select.option>
                    @foreach($this->categories as $cat)
                        <flux:select.option value="{{ $cat->id }}">{{ $cat->name }}</flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        {{-- Product grid --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($items as $item)
                <flux:card
                    class="cursor-pointer transition hover:shadow-md"
                    wire:click="selectProduct({{ $item['id'] }})"
                    wire:key="product-{{ $item['id'] }}"
                >
                    <div class="flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-2">
                            <flux:heading size="sm" class="leading-snug">
                                {{ $item['name'] }}
                            </flux:heading>
                            <flux:badge color="blue" size="sm">
                                {{ $item['category']['name'] }}
                            </flux:badge>
                        </div>
                        <flux:text class="line-clamp-2 text-sm text-zinc-500">
                            {{ $item['description'] }}
                        </flux:text>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-lg font-bold text-zinc-800 dark:text-zinc-100">
                                ${{ number_format($item['price'], 2) }}
                            </span>
                            <flux:button size="sm" variant="ghost">
                                View details
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @empty
                <div class="col-span-3 py-12 text-center text-zinc-400">
                    No products found.
                </div>
            @endforelse
        </div>

        {{-- Skeleton cards shown while loadMore request is in flight --}}
        <div wire:loading wire:target="loadMore" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @for($i = 0; $i < $perPage; $i++)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="h-4 w-2/3 animate-pulse rounded bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="h-5 w-20 animate-pulse rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-3 w-full animate-pulse rounded bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="h-3 w-4/5 animate-pulse rounded bg-zinc-200 dark:bg-zinc-700"></div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <div class="h-6 w-16 animate-pulse rounded bg-zinc-200 dark:bg-zinc-700"></div>
                            <div class="h-7 w-24 animate-pulse rounded-md bg-zinc-200 dark:bg-zinc-700"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- wire:intersect sentinel — triggers loadMore when scrolled into view --}}
        @if($this->hasMore)
            <div
                wire:intersect="loadMore"
                wire:loading.remove wire:target="loadMore"
                class="py-1"
                aria-hidden="true"
            ></div>
        @endif

        {{-- Product detail component (listens for product-selected event) --}}
        <livewire:product-detail />

</div>
