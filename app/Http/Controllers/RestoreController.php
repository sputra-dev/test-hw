<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restore;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class RestoreController extends Controller
{
    public function createRestore(Request $request)
    {
        $request->validate([
            'loan_id' => [
                'required',
                Rule::exists('loans', 'loan_id')->where('status', '!=', 'finished'),
            ],
            'restore_date' => 'required|date',
            'status' => 'required|in:incomplete,complete',
            'penalty' => 'required_if:status,incomplete|numeric|min:0',
        ]);

        try {
            $restore = Restore::create([
                'loan_id' => $request->input('loan_id'),
                'restore_date' => $request->input('restore_date'),
                'status' => $request->input('status'),
                'penalty' => $request->input('penalty', 0.00),
            ]);

            // Update status on the loan and loan details
            $loan = $restore->loan;
            $loanDetails = $loan->loanDetails;

            if ($request->input('status') == 'complete') {
                // Update status on the loan
                $loan->update(['status' => 'finished']);

                // Update status on each loan detail
                $loanDetails->each(function ($detail) {
                    $detail->update(['status' => 'complete']);
                });
            }

            $responseData = [
                'message' => 'Restore Created Successfully',
                'data' => [
                    'restore_id' => $restore->id,
                    'loan_id' => $restore->loan_id,
                    'status' => $restore->status,
                ],
            ];

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            Log::error('Error creating restore: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

}
