<?php

namespace App\Services;

use App\Enums\BooksEnum;
use App\Exports\BooksExport;
use App\Repositories\BookRepository;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BookService
{
    use JsonResponseTrait;

    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * create
     *
     * @param  mixed $data
     * @return void
     */
    public function create(array $data)
    {
        try {
            $book = $this->bookRepository->create($data);
            Log::channel('additions')->info('New book added', ['book_id' => $book->id, 'title' => $book->title, 'author' => $book->author, 'isbn' => $book->isbn, 'status' => $book->status]);
            return $this->successResponse($book, 'messages.book.created', 201);
        } catch (Exception $e) {
            Log::error('Book creation failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('messages.book.creation_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * update
     *
     * @param  mixed $uuid
     * @param  mixed $data
     * @return JsonResponse
     */
    public function update($uuid, array $data): JsonResponse
    {
        try {
            $book = $this->bookRepository->findByUuid($uuid);
            if (!$book) {
                return $this->errorResponse('messages.book.not_found', 404);
            }
            $this->bookRepository->update($uuid, $data);
            $updatedBookData = $this->bookRepository->findByUuid($uuid);

            Log::channel('updates')->info('Book updated successfully', ['book_uuid' => $uuid, 'updated_fields' => array_keys($data)]);
            return $this->successResponse($updatedBookData, 'messages.book.updated', 200);
        } catch (Exception $e) {
            Log::error('Book update failed', ['error' => $e->getMessage()]);
            return $this->errorResponse('messages.book.update_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * delete
     *
     * @param  mixed $uuid
     * @return void
     */
    public function delete($uuid)
    {
        try {
            $book = $this->bookRepository->findByUuid($uuid);
            if (!$book) {
                return $this->errorResponse('messages.book.not_found', 404);
            }
            $this->bookRepository->delete($uuid);
            return $this->successResponse(null, 'messages.book.deleted', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.deletion_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getBooks
     *
     * @return JsonResponse
     */
    public function getBooks(): JsonResponse
    {
        try {
            $books = $this->bookRepository->getAll();
            return $this->successResponse($books, 'messages.book.retrieved', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.retrieval_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getByUuid
     *
     * @param  mixed $uuid
     * @return JsonResponse
     */
    public function getByUuid($uuid): JsonResponse
    {
        try {
            $book = $this->bookRepository->findByUuid($uuid);
            if (!$book) {
                return $this->errorResponse('messages.book.not_found', 404);
            }
            return $this->successResponse($book, 'messages.book.retrieved', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.retrieval_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * getBooksByStatus
     *
     * @param  mixed $status
     * @return JsonResponse
     */
    public function getBooksByStatus(int $status): JsonResponse
    {
        try {
            if (!in_array($status, [BooksEnum::AVAILABLE->value, BooksEnum::NOT_AVAILABLE->value])) {
                return $this->errorResponse('messages.book.invalid_status', 400);
            }
            $books = $this->bookRepository->getBooksByStatus(BooksEnum::from($status));
            return $this->successResponse($books, 'messages.book.retrieved', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.retrieval_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * searchBooks
     *
     * @param  mixed $request
     * @return JsonResponse
     */
    public function searchBooks($request): JsonResponse
    {
        try {
            $request->validate([
                'query' => 'required|string|max:255',
                'status' => 'nullable|integer|in:1,2',
            ]);
            $query = $request->input('query');

            if (!$query) {
                return $this->errorResponse('messages.book.query_required', 400);
            }

            $status = $request->input('status');
            $books = $this->bookRepository->search($query, $status);
            return $this->successResponse($books, 'messages.book.retrieved', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.search_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * importBooks
     *
     * @param  mixed $data
     * @param  mixed $userId
     * @return JsonResponse
     */
    public function importBooks($data, $userId): JsonResponse
    {
        try {
            $importedBooks = [];
            foreach ($data as $item) {
                $importedBooks[] = $this->bookRepository->create([
                    'user_id'         => $userId,
                    'title'           => $item['title'],
                    'author'          => $item['author'],
                    'isbn'            => $item['isbn'],
                    'status'          => $item['status'],
                    'publication_date' => $item['publication_date'],
                ]);
            }
            return $this->successResponse($importedBooks, 'messages.book.import_success', 201);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.import_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }

    /**
     * export
     *
     * @return JsonResponse
     */
    public function export(): JsonResponse
    {
        $date = now()->format('Y-m-d');
        $counter = 1;
        $filePath = "books/Export_books{$counter}_{$date}.csv";
        while (Storage::disk('public')->exists($filePath)) {
            $counter++;
            $filePath = "books/Export_books{$counter}_{$date}.csv";
        }
        try {
            Excel::store(new BooksExport, $filePath, 'public');
            return $this->successResponse(['path' => Storage::url($filePath)], 'messages.book.exported', 200);
        } catch (Exception $e) {
            return $this->errorResponse('messages.book.export_failed', 500, ['original_error' => $e->getMessage()]);
        }
    }
}
