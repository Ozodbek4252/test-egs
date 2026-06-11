<div>
<flux:modal name="product-detail" class="md:w-96">
    @if($product)
        <div class="flex flex-col gap-4">
            <div>
                <flux:heading size="lg">{{ $product->name }}</flux:heading>
                <flux:badge color="blue" class="mt-1">{{ $product->category->name }}</flux:badge>
            </div>

            <flux:text class="text-sm text-zinc-500">
                {{ $product->description }}
            </flux:text>

            <div class="flex items-center justify-between border-t pt-4 dark:border-zinc-700">
                <span class="text-2xl font-bold text-zinc-800 dark:text-zinc-100">
                    ${{ number_format($product->price, 2) }}
                </span>
                <flux:button x-on:click="$flux.modal('product-detail').close()">
                    Close
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
</div>
