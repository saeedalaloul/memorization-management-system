<table>
    <thead>
    <tr>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم الطالب
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم هوية الطالب
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم هوية ولى الأمر
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم جوال ولى الأمر
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الميلاد
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            الوضع المادي
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المرحلة
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المحفظ
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أجزاء الحفظ
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر اختبار منفرد
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر اختبار تجميعي
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($students as $student)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $student->student_name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $student->student_identification_number }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $student->father_identification_number }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $student->father_phone }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $student->student_dob }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if ($student->economic_situation == \App\Models\UserInfo::GOOD_STATUS)
                    جيد
                @elseif($student->economic_situation == \App\Models\UserInfo::MODERATE_STATUS)
                    متوسط
                @else
                    صعب
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$student->grade_name}}
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$student->teacher_name}}
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$student->total_preservation_parts}}
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$student->quran_part_individual}}
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$student->quran_part_deserved}}
            </td>
        </tr>
    @empty
        <tr style="text-align: center">
            <td colspan="4">No data available in table</td>
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
            رقم هوية الطالب
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم هوية ولى الأمر
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم جوال ولى الأمر
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الميلاد
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            الوضع المادي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المرحلة
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            عدد أجزاء الحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر اختبار منفرد
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر اختبار تجميعي
        </th>
    </tr>
    </tfoot>
</table>
