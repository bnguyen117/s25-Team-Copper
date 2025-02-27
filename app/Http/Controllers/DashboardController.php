<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Debt;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve debts belonging to the logged-in user
        $debts = Debt::where('user_id', $user->id)->get();

        // Calculate the total sum of minimum payments
        $totalMinimumPayments = $debts->sum('minimum_payment');

        // Calculate total debt amount
        $totalDebt = $debts->sum('amount');

        // Prepare data for the chart
        $debtChartData = $debts->map(function ($debt) {
            return [
                'name' => $debt->debt_name,
                'amount' => $debt->amount,
                'minimum_payment' => $debt->minimum_payment,
            ];
        });

        return view('dashboard', [
            'user' => $user,
            'debts' => $debts,
            'debt' => $totalDebt, // Pass the total debt to the view
            'debt2' => $totalMinimumPayments,
            'debtChartData' => $debtChartData,
        ]);
    }
}
