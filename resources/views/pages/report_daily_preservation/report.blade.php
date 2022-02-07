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
    $numberPagesSaved = 0;
    $numberPagesReview = 0;
    ?>
    <tbody>
    @forelse($reports_daily_preservation as $report)
        <!--                                --><?php
        //                                if (isset($report)) {
        //                                    if ($report->type == 1) {
        //                                        $numberPagesSaved += $report->calcnumberpages();
        //                                    } else {
        //                                        $numberPagesReview += $report->calcnumberpages();
        //                                    }
        //                                }
        //                                ?>
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{\Carbon\Carbon::parse($report->daily_preservation_date)
                                      ->translatedFormat('l') . '  ' . $report->daily_preservation_date }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->student->user->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->dailyPreservationType->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->quranSuraFrom->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->fromaya() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->quranSuraTo->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->toaya() }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $report->dailyPreservationEvaluation->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></td>
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
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"></th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد صفحات المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$numberPagesReview}}</th>
    </tr>
    </tfoot>
</table>
