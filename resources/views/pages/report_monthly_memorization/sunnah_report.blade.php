\
<table>
    <thead>
    <tr>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">#
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">اسم الطالب
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">اسم الكتاب
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">أحاديث الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">أحاديث المراجعة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أحاديث الحفظ
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أحاديث المراجعة
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">الاختبارات
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أيام الحضور
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            rowspan="2">عدد أيام الغياب
        </th>
    </tr>
    <tr>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ( من )
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ( إلى )
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ( من )
        </th>
        <th style="height: 30px;background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            ( إلى )
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($reports_monthly_memorization as $report)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{$loop->iteration}}</td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['student_name']))
                    {{$report['student_name']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['book_name']))
                    {{$report['book_name']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['memorize_hadith_from']))
                    {{strval($report['memorize_hadith_from'])}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['memorize_hadith_to']))
                    {{strval($report['memorize_hadith_to'])}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['review_hadith_from']))
                    {{strval($report['review_hadith_from'])}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['review_hadith_to']))
                    {{strval($report['review_hadith_to'])}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['memorize_hadith_from']) && isset($report['memorize_hadith_to']))
                    {{($report['memorize_hadith_to'] - $report['memorize_hadith_from']) + 1}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['review_hadith_from']) && isset($report['review_hadith_to']))
                    {{($report['review_hadith_to'] - $report['review_hadith_from']) + 1}}
                @else
                    _
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['sunnah_part_name']))
                    {{$report['sunnah_part_name']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['presence_days_count']))
                    {{$report['presence_days_count']}}
                @endif
            </td>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if (isset($report['absence_days_count']))
                    {{$report['absence_days_count']}}
                @endif
            </td>
        </tr>
    @empty
        <tr style="text-align: center">
            <td colspan="12">No data available in table</td>
        </tr>
    @endforelse
    </tbody>
    <tfoot>
    <tr>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الكتاب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">أحاديث الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;"
            colspan="2">أحاديث المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أحاديث الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أحاديث المراجعة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            الإختبارات
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أيام الغياب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أيام الحضور
        </th>
    </tr>
    </tfoot>
</table>
