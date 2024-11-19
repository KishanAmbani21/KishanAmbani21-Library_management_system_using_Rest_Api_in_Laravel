<?php

namespace App\Services;

use App\Enums\BooksEnum;
use App\Repositories\BookRepository;
use App\Repositories\BorrowRepository;
use App\Traits\JsonResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BorrowService
{
    use JsonResponseTrait;

    protected $borrowRepository;
    protected $bookRepository;

    /**
     * __construct
     *
     * @param BorrowRepository $borrowRepository
     * @param BookRepository $bookRepository
     */
    public function __construct(BorrowRepository $borrowRepository, BookRepository $bookRepository)
    {
        $this->borrowRepository = $borrowRepository;
        $this->bookRepository = $bookRepository;
    }

    /**
     * Create a borrow record.
     *
     * @param array $data
     */
    public function create(array $data)
    {
        $response = null;
        try {
            $user = Auth::user();
            $data['user_id'] = $user->id;
            $errors = [];

            $borrowCount = $this->borrowRepository->countByUserId($user->id);
            $maxBorrowLimit = 5;
            if ($borrowCount >= $maxBorrowLimit) {
                $errors[] = ['message' => 'messages.borrow.limit_reached', 'code' => 403];
            }
            $borrowDate = $data['borrow_date'] ?? Carbon::now()->format('Y-m-d');
            $data['borrow_date'] = $borrowDate;
            $book = $this->bookRepository->findById($data['book_id']);
            if ($book->status == BooksEnum::NOT_AVAILABLE->value) {
                $errors[] = ['message' => 'messages.borrow.book_not_available', 'code' => 409];
            }
            if (!empty($errors)) {
                return $this->errorResponse($errors[0]['message'], $errors[0]['code']);
            }
            $activeBorrow = $this->borrowRepository->findActiveBorrowByBook($data['book_id']);
            if ($activeBorrow) {
                return $this->errorResponse('messages.borrow.book_already_borrowed', 409);
            }

            $data['due_date'] = Carbon::parse($borrowDate)->addDays(10)->format('Y-m-d');

            $total_penalty = $this->bookRepository->calculatePenalty($data['due_date']);
            $data['total_penalty'] = $total_penalty;

            $borrow = $this->borrowRepository->create($data);
            $this->bookRepository->updateStatus($data['book_id'], BooksEnum::NOT_AVAILABLE->value);
            Log::channel('borrow')->info('Book borrowed successfully', ['borrow_id' => $borrow->id, 'user_id' => $data['user_id'], 'book_id' => $data['book_id'], 'borrow_date' => $borrowDate, 'due_date' => $data['due_date']]);
            $response = $this->successResponse($borrow, 'messages.borrow.created_success', 201);
        } catch (Exception $e) {
            Log::error('Borrow creation failed', ['error' => $e->getMessage()]);
            $response = $this->errorResponse('messages.borrow.creation_failed', 500, ['original_error' => $e->getMessage()]);
        }
        return $response;
    }

    /**
     * getBorrows
     *
     * @return void
     */
    public function getBorrows()
    {
        try {
            $borrows = $this->borrowRepository->getAllWithRelations();
            return $this->successResponse($borrows, 'messages.borrow.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.retrieval_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getBorrowsByUuid
     *
     * @param  mixed $uuid
     * @return void
     */
    public function getBorrowsByUuid($uuid)
    {
        try {
            $borrow = $this->borrowRepository->findByUuid($uuid);
            if (!$borrow) {
                return $this->errorResponse('messages.borrow.not_found', 404);
            }
            return $this->successResponse($borrow, 'messages.borrow.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * searchBorrows
     *
     * @param  mixed $request
     * @return void
     */
    public function searchBorrows($request)
    {
        try {
            $query = $request->input('query');
            if (!$query) {
                return $this->errorResponse('messages.user.query_required', 400);
            }
            $request->validate(['query' => 'required|string|max:255']);
            $borrows = $this->borrowRepository->search($query);
            return $this->successResponse($borrows, 'messages.borrow.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }


    /**
     * returnBook
     *
     * @param  mixed $id
     * @return void
     */
    public function returnBook($id)
    {
        $response = null;
        try {
            $borrow = $this->borrowRepository->getByBookAndUser($id);

            if ($borrow->returned) {
                return $this->errorResponse('messages.borrow.already_returned', 403);
            }

            $now = Carbon::now();
            $penalty = $this->borrowRepository->calculatePenalty($borrow->due_date);

            if ($penalty > 0) {
                $borrow->total_penalty = $penalty;
                $borrow->save();
                if (!$borrow->penalty_paid) {
                    return response()->json([
                        'message' => 'Penalty has not been paid. Cannot mark the book as returned.',
                        'pay_penalty_url' => url('/pay-penalty/' . $borrow->id)
                    ], 500);
                }
            }

            $borrow->returned = true;
            $borrow->return_date = $now;
            $this->bookRepository->updateStatus($borrow->book_id, BooksEnum::AVAILABLE->value);

            $borrow->save();

            Log::channel('return')->info('Book returned successfully', [
                'book_id' => $borrow->book_id,
                'user_id' => $borrow->user_id,
                'return_date' => now()->format('Y-m-d H:i:s')
            ]);

            $response = $this->successResponse($borrow, 'messages.borrow.return_success', 200);
        } catch (Exception $e) {
            Log::error('Error returning book', ['book_id' => $id, 'error_message' => $e->getMessage()]);
            $response = $this->errorResponse('messages.borrow.return_failed', 500, ['original_error' => $e->getMessage()]);
        }
        return $response;
    }

    /**
     * getAllReturnedBooks
     *
     * @return void
     */
    public function getAllReturnedBooks()
    {
        try {
            $returnedBooks = $this->borrowRepository->getAllReturnedBooks();
            return $this->successResponse($returnedBooks, 'messages.borrow.return_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.return_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }


    /**
     * getBorrowsByUser
     *
     * @param  mixed $userId
     * @return void
     */
    public function getBorrowsByUser($userId)
    {
        try {
            $borrows = $this->borrowRepository->getByUser($userId);
            return $this->successResponse($borrows, 'messages.borrow.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * Get borrows by book ID.
     *
     * @param int $bookId
     * @return mixed
     */
    public function getBorrowsByBook($bookId)
    {
        try {
            $borrows = $this->borrowRepository->getByBook($bookId);
            return $this->successResponse($borrows, 'messages.borrow.retrieve_success', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.borrow.retrieve_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }
}
