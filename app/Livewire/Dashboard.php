<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Salary;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{

     public int $maxMugs = 5;

    // --- Add salary form ---
    public string $salaryAmount = '';
    public string $salaryDate = '';

    // --- Add expense form ---
    public string $expenseCategory = 'coffee';
    public string $expenseAmount = '';
    public string $expenseDate = '';
    public string $expenseDescription = '';

    public function mount(): void
    {
        $this->salaryDate = now()->toDateString();
        $this->expenseDate = now()->toDateString();
    }

     public function saveSalary(): void
    {
        $this->validate([
            'salaryAmount' => 'required|numeric|min:0.01',
            'salaryDate' => 'required|date',
        ]);

        Salary::create([
            'user_id' => Auth::id(),
            'amount' => $this->salaryAmount,
            'date' => $this->salaryDate,
        ]);

        $this->reset('salaryAmount');
        session()->flash('status', 'Salary added.');
    }

    public function saveExpense(): void
    {
        $this->validate([
            'expenseCategory' => 'required|string|max:50',
            'expenseAmount' => 'required|numeric|min:0.01',
            'expenseDate' => 'required|date',
            'expenseDescription' => 'nullable|string|max:255',
        ]);

        Expense::create([
            'user_id' => Auth::id(),
            'category' => $this->expenseCategory,
            'amount' => $this->expenseAmount,
            'date' => $this->expenseDate,
            'description' => $this->expenseDescription,
        ]);

        $this->reset(['expenseAmount', 'expenseDescription']);

        if ($this->expenseCategory === Expense::CATEGORY_COFFEE && $this->mugsLeft() <= 0) {
            session()->flash('status', "That's your last coffee mug for the month, Bebu!");
        } else {
            session()->flash('status', 'Expense logged.');
        }
    }

    /**
     * Coffee mugs remaining this month. One mug is lost per coffee
     * purchase, floored at zero (never goes negative).
     */
    public function mugsLeft(): int
    {
        $coffeeCountThisMonth = Expense::query()
            ->where('user_id', Auth::id())
            ->category(Expense::CATEGORY_COFFEE)
            ->thisMonth()
            ->count();

        return max(0, $this->maxMugs - $coffeeCountThisMonth);
    }

    public function render()
    {
        $userId = Auth::id();

        $totalSalary = Salary::where('user_id', $userId)->sum('amount');
        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');

        $expensesByCategory = Expense::where('user_id', $userId)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $recentEntries = collect()
            ->merge(
                Salary::where('user_id', $userId)->latest('date')->take(10)->get()
                    ->map(fn ($s) => ['type' => 'salary', 'amount' => $s->amount, 'date' => $s->date, 'label' => 'Salary'])
            )
            ->merge(
                Expense::where('user_id', $userId)->latest('date')->take(10)->get()
                    ->map(fn ($e) => ['type' => 'expense', 'amount' => $e->amount, 'date' => $e->date, 'label' => ucfirst($e->category)])
            )
            ->sortByDesc('date')
            ->take(10);

        return view('livewire.dashboard', [
            'totalSalary' => $totalSalary,
            'totalExpenses' => $totalExpenses,
            'balance' => $totalSalary - $totalExpenses,
            'expensesByCategory' => $expensesByCategory,
            'recentEntries' => $recentEntries,
            'mugsLeft' => $this->mugsLeft(),
        ]);
    }
}
