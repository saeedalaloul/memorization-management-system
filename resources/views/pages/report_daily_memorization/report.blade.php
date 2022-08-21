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
    $numberPagesSaved = $reports_daily_memorization->sum(function ($report) {
        if ($report->type == \App\Models\StudentDailyMemorization::MEMORIZE_TYPE) {
            return $report->number_pages;
        }
        return 0;
    });
    $numberPagesReview = $reports_daily_memorization->sum(function ($report) {
        if ($report->type == \App\Models\StudentDailyMemorization::REVIEW_TYPE) {
            return $report->number_pages;
        }
        return 0;
    });

    $numberPagesCumulativeReview = $reports_daily_memorization->sum(function ($report) {
        if ($report->type == \App\Models\StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
            return $report->number_pages;
        }
        return 0;
    });
    ?>
    <tbody>
    @forelse($reports_daily_memorization as $report)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{\Carbon\Carbon::parse($report->datetime)
                                      ->translatedFormat('l') . '  ' . \Illuminate\Support\Carbon::parse($report->datetime)->format('Y-m-d') }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->student->user->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->TypeName() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->quranSuraFrom->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->AyaFrom() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->quranSuraTo->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->AyaTo() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->evaluation() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->number_pages}}</td>
        </tr>
    @empty
        <tr style="text-align: center">
            <td colspan="8">No data available in table</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
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
