<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{

    public function updateDeviceToken(Request $request)
    {
        UserFcmToken::updateOrCreate(['id' => auth()->id()], [
            'device_token' => $request->token,
        ]);
        return response()->json(['Token successfully stored.']);
    }

    public function markAsReadAndRedirect($id)
    {
        $notification = auth()->user()->notifications->where('id', $id)->first();
        $notification->markAsRead();

        if ($notification->type === 'App\Notifications\AcceptExamOrderForTeacherNotify'
            || $notification->type === 'App\Notifications\AcceptExamOrderForTesterNotify'
            || $notification->type === 'App\Notifications\FailureExamOrderForTeacherNotify'
            || $notification->type === 'App\Notifications\ImproveExamOrderForExamsSupervisorNotify'
            || $notification->type === 'App\Notifications\NewExamOrderForExamsSupervisorNotify'
            || $notification->type === 'App\Notifications\RejectionExamOrderForTeacherNotify') {
            return redirect()->route('manage_exams_orders', $notification->data['id']);
        }

        if ($notification->type === 'App\Notifications\NewExamForTeacherNotify' ||
            $notification->type === 'App\Notifications\ImproveExamForTeacherNotify') {
            if (auth()->user()->current_role === User::TEACHER_ROLE) {
                if (Group::where('teacher_id', auth()->id())->first()->type === Group::SUNNAH_TYPE) {
                    return redirect()->route('manage_sunnah_exams', $notification->data['id']);
                }

                return redirect()->route('manage_exams', $notification->data['id']);
            }
        } else if ($notification->type === 'App\Notifications\NewBoxComplaintSuggestionNotify' ||
            $notification->type === 'App\Notifications\ReplayBoxComplaintSuggestionNotify') {
            return redirect()->route('manage_box_complaint_suggestions', $notification->data['id']);
        } else if ($notification->type === 'App\Notifications\NewActivityOrderForActivitiesSupervisorNotify'
            || $notification->type === 'App\Notifications\AcceptActivityOrderForTeacherNotify'
            || $notification->type === 'App\Notifications\AcceptActivityOrderForActivityMemberNotify'
            || $notification->type === 'App\Notifications\RejectionActivityOrderForTeacherNotify'
            || $notification->type === 'App\Notifications\FailureActivityOrderForTeacherNotify') {
            return redirect()->route('manage_activities_orders', $notification->data['id']);
        } else if ($notification->type === 'App\Notifications\NewStudentWarningForTeacherNotify'
            || $notification->type === 'App\Notifications\ExpiredStudentWarningForTeacherNotify') {
            return redirect()->route('manage_student', $notification->data['student_id']);
        } else if ($notification->type === 'App\Notifications\NewStudentBlockForTeacherNotify'
            || $notification->type === 'App\Notifications\ExpiredStudentBlockForTeacherNotify') {
            return redirect()->route('manage_student', $notification->data['student_id']);
        } else if ($notification->type === 'App\Notifications\NewStudentForTeacherNotify'
            || $notification->type === 'App\Notifications\MoveStudentForTeacherNotify') {
            return redirect()->route('manage_track_student_transfers', $notification->data['id']);
        } else if ($notification->type === 'App\Notifications\NewVisitOrderForOversightMemberNotify'
            || $notification->type === 'App\Notifications\UpdateVisitOrderForOversightMemberNotify'
            || $notification->type === 'App\Notifications\SendVisitOrderForOversightSupervisorNotify') {
            return redirect()->route('manage_visits_orders', $notification->data['id']);
        } else if ($notification->type === 'App\Notifications\NewVisitForAdminNotify'
            || $notification->type === 'App\Notifications\ReplyToVisitForOversightSupervisorNotify'
            || $notification->type === 'App\Notifications\SolvedVisitForAdminNotify'
            || $notification->type === 'App\Notifications\FailureProcessingOfVisitForAdminNotify'
            || $notification->type === 'App\Notifications\ReminderOfVisitForAdminNotify'
            || $notification->type === 'App\Notifications\ReminderOfVisitForOversightSupervisorNotify') {
            return redirect()->route('manage_visits', $notification->data['id']);
        } else {
            return redirect()->back();
        }
    }

}
