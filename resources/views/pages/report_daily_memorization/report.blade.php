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
            الطالب
        </th>
        @if (auth()->user()->current_role !== \App\Models\User::TEACHER_ROLE)
            <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                اسم المحفظ
            </th>
        @endif
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            العملية
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            من سورة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آية
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            إلى سورة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آية
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            التقييم
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد الصفحات
        </th>
    </tr>
    </thead>
    <?php
    $numberPagesSaved = 0;
    $numberPagesReview = 0;
    $numberPagesCumulativeReview = 0;
    ?>
    <tbody>
    @forelse($reports_daily_memorization as $report)
        @if (isset($report['daily_memorization_type'] ))
            @if ($report['daily_memorization_type'] == \App\Models\StudentDailyMemorization::MEMORIZE_TYPE)
                <?php $numberPagesSaved += $report['number_pages'];?>
            @elseif($report['daily_memorization_type'] == \App\Models\StudentDailyMemorization::REVIEW_TYPE)
                <?php $numberPagesReview += $report['number_pages'];?>
            @elseif($report['daily_memorization_type'] == \App\Models\StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE)
                <?php $numberPagesCumulativeReview += $report['number_pages'];?>
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
                        @if (isset($report['quran_part_name']))
                            اختبار
                        @else
                            لم يحفظ
                        @endif
                    @else
                        {{\App\Models\StudentAttendance::status()[$report['attendance_status']]}}
                    @endif
                @endif
            </td>


            @if (isset($report['sura_from_name']))
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                    {{ $report['sura_from_name'] }}
                </td>
            @elseif (isset($report['quran_part_name']))
                @if ($report['mark'] >= $report['success_mark'])
                    <td style="background-color: #28a745; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['quran_part_name']}}</div>
                    </td>
                @else
                    <td style="background-color: #dc3545; color: #FFFFFF;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align: center; vertical-align: center;">
                        <div>{{$report['quran_part_name'].' '}}</div>
                    </td>
                @endif
            @else
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></td>
            @endif


            @if (isset($report['aya_from']))
                <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                    {{ $report['number_aya_from'] == $report['aya_from'] ? 'كاملة' : $report['aya_from'] }}
                </td>
            @elseif (isset($report['quran_part_name']))
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
                @if(isset($report['sura_to_name']))
                    {{ $report['sura_to_name'] }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['aya_to']))
                    {{ $report['number_aya_to'] == $report['aya_to'] ? 'كاملة' : $report['aya_to'] }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if(isset($report['evaluation']))
                    {{ \App\Models\StudentDailyMemorization::evaluations()[$report['evaluation']] }}
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['number_pages']))
                    {{ $report['number_pages']}}
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
        @if (auth()->user()->current_role != \App\Models\User::TEACHER_ROLE)
            <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        @endif
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberPagesSaved}}</th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberPagesReview}}</th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات مراجعة التجميعي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberPagesCumulativeReview}}</th>
    </tr>
    </tfoot>
</table>
