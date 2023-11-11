<?php

namespace App\Http\Controllers;
use App\Models\word;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\ProgressVocabulary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProgressVocabularyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }





    public function index(Request $request)
{
    try {
        $userId = Auth::id();
        $userWords = Word::where('user_id', $userId)->get();
        $totalWordCount = $userWords->count();
        $today = now()->toDateString();
        $rememberWordToday = $userWords->filter(function ($word) use ($today) {
            return $word->remember && $word->updated_at->toDateString() == $today;
        })->count();
        $rememberedWordCount = $userWords->where('remember', true)->count();
        $nonRememberedWordCount = $totalWordCount - $rememberedWordCount;
        $progress = ProgressVocabulary::firstOrNew(['user_id' => $userId]);
        if (!$progress->exists) {
            $progress->save();
        }

        $targetDay = $progress->target_day;
        $targetRememberPerDay = $progress->target_remember_perday;
        $startDay = $progress->start_day;
        $todayDate = Carbon::parse($today);
        $startDate = Carbon::parse($startDay);
        $runningDay = $todayDate->diffInDays($startDate) + 1;

        $response = [
            "user_id" => $userId,
            "total_word" => $totalWordCount,
            "remember" => $rememberedWordCount,
            "no_remember" => $nonRememberedWordCount,
            "target_day" => $targetDay,
            "target_remember_perday" => $targetRememberPerDay,
            "achieved" => $rememberWordToday,
            "running_day" => $runningDay
        ];

        return response([
            "status_code" => 200,
            "message" => "Success",
            "data" => $response
        ]);
    } catch (\Exception $e) {
        return response([
            "status_code" => 500,
            "message" => "Server error",
            "error" => $e->getMessage()
        ]);
    }
}

public function update(Request $request, $id)
{
    $user = Auth::user();
    $targetDay = $request->input('target_day');
    $targetRememberPerDay = $request->input('target_remember_perday');
    $today = now()->toDateString();
    $progress = ProgressVocabulary::where('user_id', $user->id)->find($id);

    if (!$progress) {
        return response([
            "status_code" => 404,
            "message" => "Progress record not found for the user",
        ]);
    }

    $validator = Validator::make($request->all(), [
        'target_day' => 'nullable|integer',
        'target_remember_perday' => 'nullable|integer',
    ]);

    if ($validator->fails()) {
        return response([
            "status_code" => 400,
            "message" => "Validation failed",
            "errors" => $validator->messages(),
        ]);
    }

    $progress->start_day = $today;

    $progress->update($request->all());

    return response()->json([
        "status_code" => 200,
        "message" => "Progress updated successfully",
        "data" => [
            "target_day" => $progress->target_day,
            "target_remember_perday" => $progress->target_remember_perday,
            "user_id" => $progress->user_id,
            "created_at" => $progress->created_at,
            "updated_at" => $progress->updated_at,
        ],
    ]);
}


}
