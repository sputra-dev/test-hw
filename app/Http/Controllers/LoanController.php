<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\LoanDetail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Book;

class LoanController extends Controller
{
    public function createLoan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'loan_date' => 'required|date',
            'restore_date' => 'required|date',
            'note' => 'nullable|string',
            'books' => 'required|array',
            'books.*.book_id' => 'required|exists:books,id',
        ]);

        try {
            $existingLoansCount = Loan::count();
            $loanId = "TRX" . str_pad($existingLoansCount + 1, 4, '0', STR_PAD_LEFT);

            $booksToBorrow = Book::whereIn('id', $request->input('books.*.book_id'))
                ->where('status', true)
                ->get();

            $unavailableBookIds = [];
            $loanDetailsToCreate = [];

            foreach ($request->input('books') as $book) {
                $bookId = $book['book_id'];
                $bookToBorrow = $booksToBorrow->where('id', $bookId)->first();

                if ($bookToBorrow) {
                    $loanDetailsToCreate[] = [
                        'book_id' => $bookId,
                        'status' => $book['status'],
                    ];
                    Book::where('id', $bookId)->update(['status' => 'false']);
                } else {
                    $unavailableBookIds[] = $bookId;
                }
            }

            if (!empty($unavailableBookIds)) {
                return response()->json(['message' => 'Books with IDs ' . implode(', ', $unavailableBookIds) . ' are not available for borrowing.'], 400);
            }

            $loan = Loan::create([
                'loan_id' => $loanId,
                'user_id' => $request->input('user_id'),
                'loan_date' => $request->input('loan_date'),
                'restore_date' => $request->input('restore_date'),
                'note' => $request->input('note'),
            ]);

            foreach ($loanDetailsToCreate as $loanDetailData) {
                $loanDetailData['loan_id'] = $loanId;
                LoanDetail::create($loanDetailData);
            }

            $responseData = [
                'message' => 'Loan Created Successfully',
                'data' => [
                    'loan_id' => $loanId,
                    'user_id' => $loan->user_id,
                ],
            ];

            return response()->json($responseData, 201);
        } catch (\Exception $e) {
            Log::error('Error creating loan: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateRestore(Request $request, $loanId)
    {
        $request->validate([
            'restore_at' => 'required|date',
        ]);

        try {
            $loan = Loan::where('loan_id', $loanId)->first();

            if (!$loan) {
                return response()->json(['message' => 'Loan not found'], 404);
            }

            $restoreAt = Carbon::parse($request->input('restore_at'));
            $restoreDate = Carbon::parse($loan->restore_date);

            if ($restoreAt->gt($restoreDate)) {
                $penalty = $this->calculatePenalty($loan->penalty, $restoreAt, $restoreDate);

                $loan->update([
                    'restore_at' => $restoreAt,
                    'penalty' => $penalty,
                    'status' => 'finished',
                ]);

                $loan->loanDetails()->update(['status' => 'complete']);
            } else {
                $loan->update([
                    'restore_at' => $restoreAt,
                    'status' => 'finished',
                ]);

                $loan->loanDetails()->update(['status' => 'complete']);
            }

            $loan->loanDetails()->each(function ($loanDetail) {
                $bookId = $loanDetail->book_id;
                Book::where('id', $bookId)->update(['status' => 'true']);
            });

            $loan->refresh();

            $responseData = [
                'message' => 'Loan Updated Successfully',
                'data' => [
                    'loan_id' => $loan->loan_id,
                    'user_id' => $loan->user_id,
                    'status'  => $loan->status,
                ],
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            Log::error('Error updating loan restore: ' . $e->getMessage());

            return response()->json(['message' => 'Internal Server Error', 'error' => $e->getMessage()], 500);
        }
    }

    private function calculatePenalty($basePenalty, $restoreAt, $restoreDate)
    {
        $daysOverdue = $restoreAt->diffInDays($restoreDate);
        $penaltyCalculation = $basePenalty + ($daysOverdue * 30000); // Penalty per day is 30,000

        return $penaltyCalculation;
    }
}
