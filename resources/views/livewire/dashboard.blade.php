<div>
   <div class="max-w-4xl mx-auto p-6 space-y-8">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-teal-950">Bebu's money tracker</h1>
            <p class="text-sm text-teal-700">{{ now()->format('F Y') }}</p>
        </div>
        <div class="w-10 h-10 rounded-full bg-teal-950 text-teal-200 flex items-center justify-center text-sm font-medium">
            EB
        </div>
    </div>

    @if (session('status'))
        <div class="rounded-lg bg-teal-50 text-teal-800 text-sm px-4 py-2">
            {{ session('status') }}
        </div>
    @endif

    {{-- Coffee mug life meter --}}
    <div class="rounded-xl bg-teal-950 px-5 py-4 flex items-center justify-between">
        <div>
            <p class="text-sm text-teal-100">Coffee mugs left this month</p>
            <p class="text-xs text-teal-400">Lose one mug every time you buy coffee</p>
        </div>
        <div class="flex items-center gap-1.5">
            @for ($i = 0; $i < $maxMugs; $i++)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                     class="w-6 h-6 {{ $i < $mugsLeft ? 'text-teal-300' : 'text-teal-800' }}">
                    <path d="M4 8h11v7a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8Z" />
                    <path d="M15 9h2a2.5 2.5 0 0 1 0 5h-2" />
                    <path d="M7 3.5c0 .8-.6 1-.6 1.8s.6 1 .6 1.8" />
                    <path d="M11 3.5c0 .8-.6 1-.6 1.8s.6 1 .6 1.8" />
                </svg>
            @endfor
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-xl bg-teal-50 p-4">
            <p class="text-xs text-teal-700">Salary</p>
            <p class="text-2xl font-semibold text-teal-900">₱{{ number_format($totalSalary, 2) }}</p>
        </div>
        <div class="rounded-xl bg-teal-50 p-4">
            <p class="text-xs text-teal-700">Total spent</p>
            <p class="text-2xl font-semibold text-teal-950">₱{{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="rounded-xl bg-teal-50 p-4">
            <p class="text-xs text-teal-700">Balance</p>
            <p class="text-2xl font-semibold {{ $balance < 0 ? 'text-red-600' : 'text-teal-900' }}">
                ₱{{ number_format($balance, 2) }}
            </p>
        </div>
    </div>

    <flux:separator />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Add salary --}}
        <form wire:submit="saveSalary" class="rounded-xl border border-teal-100 p-4 space-y-3">
            <h2 class="font-medium text-teal-950 flex items-center gap-2">
                <flux:icon name="banknotes" class="w-4 h-4 text-teal-700" />
                Add salary
            </h2>
            <div>
                <label class="text-sm text-teal-700">Amount</label>
                <input type="number" step="0.01" wire:model="salaryAmount"
                       class="w-full rounded-lg border-teal-200 focus:border-teal-500 focus:ring-teal-500"
                       placeholder="e.g. 25000">
                @error('salaryAmount') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-teal-700">Date</label>
                <input type="date" wire:model="salaryDate"
                       class="w-full rounded-lg border-teal-200 focus:border-teal-500 focus:ring-teal-500">
                @error('salaryDate') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <flux:button type="submit" class="w-full bg-teal-800 hover:bg-teal-900 text-teal-50">
                Save salary
            </flux:button>
        </form>

        {{-- Add expense --}}
        <form wire:submit="saveExpense" class="rounded-xl border border-teal-100 p-4 space-y-3">
            <h2 class="font-medium text-teal-950 flex items-center gap-2">
                <flux:icon name="receipt-percent" class="w-4 h-4 text-teal-700" />
                Add expense
            </h2>
            <div>
                <label class="text-sm text-teal-700">Category</label>
                <flux:dropdown class="w-full">
                    <flux:button icon:trailing="chevron-down" class="w-full justify-between border-teal-200">
                        {{ ucfirst($expenseCategory) }}
                    </flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click="$set('expenseCategory', 'coffee')">Coffee</flux:menu.item>
                        <flux:menu.item wire:click="$set('expenseCategory', 'clothes')">Clothes</flux:menu.item>
                        <flux:menu.item wire:click="$set('expenseCategory', 'other')">Other</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </div>
            <div>
                <label class="text-sm text-teal-700">Amount</label>
                <input type="number" step="0.01" wire:model="expenseAmount"
                       class="w-full rounded-lg border-teal-200 focus:border-teal-500 focus:ring-teal-500"
                       placeholder="e.g. 150">
                @error('expenseAmount') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-teal-700">Date</label>
                <input type="date" wire:model="expenseDate"
                       class="w-full rounded-lg border-teal-200 focus:border-teal-500 focus:ring-teal-500">
                @error('expenseDate') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-sm text-teal-700">Note (optional)</label>
                <input type="text" wire:model="expenseDescription"
                       class="w-full rounded-lg border-teal-200 focus:border-teal-500 focus:ring-teal-500"
                       placeholder="e.g. iced latte at Kaffe">
            </div>
            <flux:button type="submit" class="w-full bg-teal-800 hover:bg-teal-900 text-teal-50">
                Log expense
            </flux:button>
        </form>

    </div>

    <flux:separator />

    {{-- Category totals --}}
    <div>
        <h2 class="font-medium text-teal-950 mb-3">Spending by category</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach (['coffee' => 'Coffee', 'clothes' => 'Clothes', 'other' => 'Other'] as $key => $label)
                <div class="rounded-xl bg-teal-50 p-4">
                    <p class="text-xs text-teal-700 flex items-center gap-1.5">
                        @if ($key === 'coffee')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                 class="w-3.5 h-3.5">
                                <path d="M4 8h11v7a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4V8Z" />
                                <path d="M15 9h2a2.5 2.5 0 0 1 0 5h-2" />
                            </svg>
                        @elseif ($key === 'clothes')
                            <flux:icon name="shopping-bag" class="w-3.5 h-3.5" />
                        @else
                            <flux:icon name="tag" class="w-3.5 h-3.5" />
                        @endif
                        {{ $label }}
                    </p>
                    <p class="text-lg font-semibold text-teal-950">₱{{ number_format($expensesByCategory[$key] ?? 0, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recent entries --}}
    <div>
        <h2 class="font-medium text-teal-950 mb-3">Recent entries</h2>
        <div class="space-y-2">
            @forelse ($recentEntries as $entry)
                <div class="flex items-center justify-between rounded-lg border border-teal-100 px-4 py-2 text-sm">
                    <span class="text-teal-900">
                        {{ $entry['label'] }} · {{ \Illuminate\Support\Carbon::parse($entry['date'])->format('M j, Y') }}
                    </span>
                    <span class="font-medium {{ $entry['type'] === 'salary' ? 'text-teal-700' : 'text-teal-950' }}">
                        {{ $entry['type'] === 'salary' ? '+' : '-' }}₱{{ number_format($entry['amount'], 2) }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-teal-400">No entries yet. Add your salary first.</p>
            @endforelse
        </div>
    </div>

</div>

</div>
