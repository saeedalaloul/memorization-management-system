<table>
    <thead>
    <tr>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            التاريخ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        @if (auth()->user()->current_role != \App\Models\User::TEACHER_ROLE)
            <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                اسم المحفظ
            </th>
        @endif
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            العملية
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الكتاب
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            من حديث
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            إلى حديث
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            التقييم
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد الأحاديث
        </th>
    </tr>
    </thead>
    <tbody>
    <?php $numberHadithsSaved = 0; $numberHadithsReview = 0;?>
    @forelse($reports_daily_memorization as $report)
        @if (isset($report['daily_memorization_type']))
            @if ($report['daily_memorization_type'] == \App\Models\StudentSunnahDailyMemorization::MEMORIZE_TYPE)
                <?php $numberHadithsSaved += ($report['hadith_to'] - $report['hadith_from']) + 1;?>
            @elseif($report['daily_memorization_type'] == \App\Models\StudentSunnahDailyMemorization::REVIEW_TYPE)
                <?php $numberHadithsReview += ($report['hadith_to'] - $report['hadith_from']) + 1;?>
            @endif
        @endif
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{\Carbon\Carbon::parse($report['datetime'])
                                      ->translatedFormat('l') . '  ' . \Illuminate\Support\Carbon::parse($report['datetime'])->format('Y-m-d') }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report['student_name'] }}</td>
            @if (auth()->user()->current_role !== \App\Models\User::TEACHER_ROLE)
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report['teacher_name'] }}</td>
            @endif
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['daily_memorization_type']))
                    {{ \App\Models\StudentDailyMemorization::types()[$report['daily_memorization_type']] }}
                @else
                    @if($report['attendance_status'] === \App\Models\StudentAttendance::PRESENCE_STATUS
                         || $report['attendance_status'] === \App\Models\StudentAttendance::LATE_STATUS)
                        @if (isset($report['sunnah_part_name']))
                            اختبار
                        @else
                            لم يحفظ
                        @endif
                    @else
                        {{\App\Models\StudentAttendance::status()[$report['attendance_status']]}}
                    @endif
                @endif
            </td>

            @if (isset($report['book_name']))
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                    {{ $report['book_name'] }}
                </td>
            @elseif (isset($report['sunnah_part_name']))
                @if ($report['mark'] >= $report['success_mark'])
                    <td style="background-color: #28a745; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['sunnah_part_name']}}</div>
                    </td>
                @else
                    <td style="background-color: #dc3545; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['sunnah_part_name']}}</div>
                    </td>
                @endif
            @else
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></td>
            @endif

            @if (isset($report['hadith_from']))
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                    {{ $report['total_number_hadith'] === $report['hadith_from'] ? 'كامل' : $report['hadith_from'] }}
                </td>
            @elseif (isset($report['sunnah_part_name']))
                @if ($report['mark'] >= $report['success_mark'])
                    <td style="background-color: #28a745; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['mark'].'%' }}</div>
                    </td>
                @else
                    <td style="background-color: #dc3545; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['mark'].'%' }}</div>
                    </td>
                @endif
            @else
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></td>
            @endif

            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['hadith_to']))
                    {{ $report['total_number_hadith'] === $report['hadith_to'] ? 'كامل' : $report['hadith_to'] }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if(isset($report['evaluation']))
                    {{ \App\Models\StudentSunnahDailyMemorization::evaluations()[$report['evaluation']] }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['hadith_from']) && isset($report['hadith_to']))
                    {{ ($report['hadith_to'] - $report['hadith_from']) + 1}}
                @endif
            </td>
        </tr>
    @empty
        <tr style="text-align: center">
            <td colspan="8">No data available in table</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        @if (auth()->user()->current_role !== \App\Models\User::TEACHER_ROLE)
            <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        @endif
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أحاديث الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberHadithsSaved ?? 0}}</th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أحاديث المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberHadithsReview ?? 0}}</th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
    </tr>
    </tfoot>
</table>
