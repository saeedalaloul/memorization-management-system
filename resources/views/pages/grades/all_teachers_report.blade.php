<table>
    <thead>
    <tr>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            #
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المحفظ
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم الهوية
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم الجوال
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الميلاد
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            الوضع المادي
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر دورة أحكام
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            المؤهل العلمي
        </th>
        <th style="height: 30px; background:#D9D9D9; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المرحلة
        </th>
    </tr>
    </thead>
    <tbody>
    @forelse($teachers as $teacher)
        <tr>
            <td style="background:#D9D9D9;height: 25px; border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $loop->iteration }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $teacher->name }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $teacher->identification_number }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $teacher->phone }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">{{ $teacher->dob }}</td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if ($teacher->economic_situation == \App\Models\UserInfo::GOOD_STATUS)
                    جيد
                @elseif($teacher->economic_situation == \App\Models\UserInfo::MODERATE_STATUS)
                    متوسط
                @else
                    صعب
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                @if ($teacher->recitation_level == \App\Models\UserInfo::AL_NOORANIAH_LEVEL)
                    القاعدة النورانية
                @elseif($teacher->economic_situation == \App\Models\UserInfo::QUALIFYING_LEVEL)
                    التأهيلية
                @elseif($teacher->economic_situation == \App\Models\UserInfo::HIGH_LEVEL)
                    العليا
                @elseif($teacher->economic_situation == \App\Models\UserInfo::TAHIL_ALSANAD_LEVEL)
                    تأهيل السند
                @else
                    سند
                @endif
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$teacher->academic_qualification}}
            </td>
            <td style="border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
                {{$teacher->grade_name}}
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
            اسم المحفظ
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم الهوية
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            رقم الجوال
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            تاريخ الميلاد
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            الوضع المادي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            آخر دورة أحكام
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            المؤهل العلمي
        </th>
        <th style="background:#D9D9D9;border:solid;font-weight: bold;font-size: 12px;font-family: Calibri;text-align:center;vertical-align: center;">
            اسم المرحلة
        </th>
    </tr>
    </tfoot>
</table>
