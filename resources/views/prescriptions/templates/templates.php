<script id="prescriptionMedicineTemplate" type="text/x-jsrender">
    <tr>
        <td class="table__item-desc">
            <select class="form-select prescriptionMedicineId select2Selector" name="medicine[]" placeholder="<?php echo ("messages.medicine_bills.select_medicine") ?>" data-id="{{:uniqueId}}" required>
                {{for medicines}}
                    <option value="{{:key}}">{{:value}}</option>
                {{/for}}
            </select>
        </td>
        <td>
            <input class="form-control" name="dosage[]" type="test">
        </td>
        <td>
            <select class="form-select prescriptionMedicineDurationId" name="day[]" data-id="{{:uniqueId}}">
                {{for doseDuration}}
                    <option value="{{:key}}">{{:value}}</option>
                {{/for}}
            </select>
        </td>
        <td>
            <select class="form-select prescriptionMedicineMealId" name="time[]" data-id="{{:uniqueId}}">
                {{for meals}}
                    <option value="{{:key}}">{{:value}}</option>
                {{/for}}
            </select>
        </td>
        <td>
            <select class="form-select prescriptionMedicineIntervalId" name="dose_interval[]" data-id="{{:uniqueId}}">
                {{for doseInterval}}
                    <option value="{{:key}}">{{:value}}</option>
                {{/for}}
            </select>
        </td>
        <td>
            <textarea class="form-control" name="comment[]" type="text" rows="1"></textarea>
        </td>
        <td class="text-center">
            <a href="javascript:void(0)" title="{{__('messages.common.delete')}}"
               class="delete-prescription-medicine-item btn px-1 text-danger fs-3 pe-0">
                     <i class="fa-solid fa-trash"></i>
            </a>
        </td>
    </tr>

</script>
