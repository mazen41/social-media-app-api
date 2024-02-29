<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReplyAction;
use App\Models\Reply;
class ReplyActionController extends Controller
{
    // public function createAction(Request $request)
    // {
    //     $request->validate([
    //         'post_id' => 'required',
    //         'user_id' => 'required',
    //         'reply_id' => 'required',
    //         'action_type' => 'string|required'
    //     ]);
    //     $replyType = $request->action_type;
        
    //     $existing_action = ReplyAction::where('post_id', $request->post_id)
    //         ->where('user_id', $request->user_id)
    //         ->where('reply_id', $request->reply_id)
    //         ->where('action_type', $request->action_type)
    //         ->first();

    //     if ($existing_action) {
    //         $existing_action->delete();
    //         $reply_action_count = ReplyAction::where('post_id', $request->post_id)
    //             ->where('reply_id', $request->reply_id)
    //             ->where('action_type', $request->action_type)
    //             ->count();
    //         Reply::where('post_id', $request->post_id)->where('id', $request->reply_id)->update([
    //             $replyType => $reply_action_count,    
    //         ]);
    //         return response()->json($reply_action_count, 200);
    //     } else {
    //         $new_action = ReplyAction::create([
    //             'post_id' => $request->post_id,
    //             'user_id' => $request->user_id,
    //             'reply_id' => $request->reply_id, 
    //             'action_type' => $request->action_type,
    //         ]);

    //         if ($new_action) {
    //             $reply_action_count = ReplyAction::where('post_id', $request->post_id)
    //                 ->where('reply_id', $request->reply_id)
    //                 ->where('action_type', $request->action_type)
    //                 ->count();
    //             Reply::where('post_id', $request->post_id)->where('id', $request->reply_id)->update([
    //                 $replyType => $reply_action_count,    
    //             ]);
    //             return response()->json($reply_action_count, 200);
    //         }
    //     }

    //     return response()->json('Something Went Wrong!', 500);
    // }
    public function createAction(Request $request)
        {
            // return $request;
            $request->validate([
                'post_id' => 'required',
                'user_id' => 'required',
                'reply_id' => 'required',
                'action_type' => 'string|required|in:likes,dislikes',
            ]);

            $replyType = $request->action_type;

            $existingAction = ReplyAction::where('post_id', $request->post_id)
                ->where('user_id', $request->user_id)
                ->where('reply_id', $request->reply_id)
                ->where('action_type', $request->action_type)
                ->first();

            if ($existingAction) {
                $existingAction->delete();
                $replyActionCount = ReplyAction::where('post_id', $request->post_id)
                    ->where('reply_id', $request->reply_id)
                    ->where('action_type', $request->action_type)
                    ->count();

                Reply::where('post_id', $request->post_id)
                    ->where('id', $request->reply_id)
                    ->update([
                        $replyType => $replyActionCount,
                    ]);

                return response()->json(['action_count' => $replyActionCount, 'message' => 'Action removed.'], 200);
            } else {
                $newAction = ReplyAction::create([
                    'post_id' => $request->post_id,
                    'user_id' => $request->user_id,
                    'reply_id' => $request->reply_id,
                    'action_type' => $request->action_type,
                ]);

                if ($newAction) {
                    $replyActionCount = ReplyAction::where('post_id', $request->post_id)
                        ->where('reply_id', $request->reply_id)
                        ->where('action_type', $request->action_type)
                        ->count();

                    Reply::where('post_id', $request->post_id)
                        ->where('id', $request->reply_id)
                        ->update([
                            $replyType => $replyActionCount,
                        ]);

                    return response()->json(['action_count' => $replyActionCount, 'message' => 'Action added.'], 200);
                }
            }

            return response()->json(['message' => 'Something went wrong!'], 500);
        }

}
