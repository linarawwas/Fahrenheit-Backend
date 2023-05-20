<?php
namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Add a new note for the authenticated user and book.
     */
    public function addNote(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'text' => 'required|string|max:255',
            'book_id' => 'required|exists:books,id',
        ]);

        $note = new Note($validatedData);
        $note->user_id = $request->user()->id;
        $note->save();

        // Increment reading rank by 2
        $user->reading_rank += 2;
        $user->save();

        return response()->json(['note' => $note]);
    }

    /**
     * Update an existing note for the authenticated user and book.
     */
    public function updateNote(Request $request, $noteId)
    {
        $note = Note::where('user_id', $request->user()->id)->findOrFail($noteId);
        $validatedData = $request->validate([
            'text' => 'required|string|max:255',
            'book_id' => 'required|exists:books,id',
        ]);

        $note->update($validatedData);

        return response()->json(['note' => $note]);
    }

    /**
     * Get all notes for the authenticated user.
     */
    public function viewAllNotes(Request $request)
    {
        $notes = Note::with('user', 'book')->get();
        return response()->json(['notes' => $notes]);
    }

    /**
     * Get all notes for the authenticated user and book.
     */
    public function viewNotesByBook(Request $request, $bookId)
    {
        $notes = Note::where('user_id', $request->user()->id)->where('book_id', $bookId)->get();
        return response()->json(['notes' => $notes]);
    }

    public function deleteNote(Request $request, $id)
    {
        $note = Note::findOrFail($id);

        // check if the authenticated user has permission to delete the note
        if ($request->user()->cannot('delete', $note)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $note->delete();

        return response()->json(['message' => 'Note deleted successfully']);
    }
}
